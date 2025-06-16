<?php

namespace App\Libraries;

use App\Models\NotificationSettingModel;
use App\Models\MessageTemplateModel;
use App\Models\SentMessageModel;
use CodeIgniter\HTTP\CURLRequest;

class NotificationService
{
    protected $notificationSettingModel;
    protected $messageTemplateModel;
    protected $sentMessageModel;
    protected $httpClient;

    public function __construct()
    {
        $this->notificationSettingModel = new NotificationSettingModel();
        $this->messageTemplateModel = new MessageTemplateModel();
        $this->sentMessageModel = new SentMessageModel();
        $this->httpClient = \Config\Services::curlrequest();
    }

    /**
     * SMS gönder
     */
    public function sendSMS($branchId, $phone, $message, $customerId = null, $appointmentId = null, $triggerType = 'manual')
    {
        // SMS aktif mi kontrol et
        if (!$this->notificationSettingModel->isSmsEnabled($branchId)) {
            return [
                'success' => false,
                'message' => 'SMS servisi aktif değil'
            ];
        }

        // SMS konfigürasyonunu al
        $config = $this->notificationSettingModel->getSmsConfig($branchId);
        
        if (empty($config['api_key']) || empty($config['api_secret'])) {
            return [
                'success' => false,
                'message' => 'SMS API bilgileri eksik'
            ];
        }

        // Telefon numarasını temizle
        $phone = $this->cleanPhoneNumber($phone);
        
        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Geçersiz telefon numarası'
            ];
        }

        // Mesajı veritabanına kaydet
        $messageId = $this->sentMessageModel->insert([
            'branch_id' => $branchId,
            'customer_id' => $customerId,
            'appointment_id' => $appointmentId,
            'message_type' => 'sms',
            'trigger_type' => $triggerType,
            'recipient_phone' => $phone,
            'message_content' => $message,
            'status' => 'pending'
        ]);

        try {
            // SMS sağlayıcısına göre gönder
            $result = $this->sendViaSmsProvider($config, $phone, $message);
            
            if ($result['success']) {
                $this->sentMessageModel->updateMessageStatus($messageId, 'sent', $result['response']);
                return [
                    'success' => true,
                    'message' => 'SMS başarıyla gönderildi',
                    'message_id' => $messageId
                ];
            } else {
                $this->sentMessageModel->updateMessageStatus($messageId, 'failed', $result['response']);
                return [
                    'success' => false,
                    'message' => 'SMS gönderilemedi: ' . $result['message'],
                    'message_id' => $messageId
                ];
            }
        } catch (\Exception $e) {
            $this->sentMessageModel->updateMessageStatus($messageId, 'failed', $e->getMessage());
            log_message('error', 'SMS gönderim hatası: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'SMS gönderim hatası: ' . $e->getMessage(),
                'message_id' => $messageId
            ];
        }
    }

    /**
     * WhatsApp mesajı gönder
     */
    public function sendWhatsApp($branchId, $phone, $message, $customerId = null, $appointmentId = null, $triggerType = 'manual')
    {
        // WhatsApp aktif mi kontrol et
        if (!$this->notificationSettingModel->isWhatsAppEnabled($branchId)) {
            return [
                'success' => false,
                'message' => 'WhatsApp servisi aktif değil'
            ];
        }

        // WhatsApp konfigürasyonunu al
        $config = $this->notificationSettingModel->getWhatsAppConfig($branchId);
        
        if (empty($config['api_url']) || empty($config['api_token'])) {
            return [
                'success' => false,
                'message' => 'WhatsApp API bilgileri eksik'
            ];
        }

        // Telefon numarasını temizle
        $phone = $this->cleanPhoneNumber($phone);
        
        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Geçersiz telefon numarası'
            ];
        }

        // Mesajı veritabanına kaydet
        $messageId = $this->sentMessageModel->insert([
            'branch_id' => $branchId,
            'customer_id' => $customerId,
            'appointment_id' => $appointmentId,
            'message_type' => 'whatsapp',
            'trigger_type' => $triggerType,
            'recipient_phone' => $phone,
            'message_content' => $message,
            'status' => 'pending'
        ]);

        try {
            // WhatsApp API'ye gönder
            $result = $this->sendViaWhatsApp($config, $phone, $message);
            
            if ($result['success']) {
                $this->sentMessageModel->updateMessageStatus($messageId, 'sent', $result['response']);
                return [
                    'success' => true,
                    'message' => 'WhatsApp mesajı başarıyla gönderildi',
                    'message_id' => $messageId
                ];
            } else {
                $this->sentMessageModel->updateMessageStatus($messageId, 'failed', $result['response']);
                return [
                    'success' => false,
                    'message' => 'WhatsApp mesajı gönderilemedi: ' . $result['message'],
                    'message_id' => $messageId
                ];
            }
        } catch (\Exception $e) {
            $this->sentMessageModel->updateMessageStatus($messageId, 'failed', $e->getMessage());
            log_message('error', 'WhatsApp gönderim hatası: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'WhatsApp gönderim hatası: ' . $e->getMessage(),
                'message_id' => $messageId
            ];
        }
    }

    /**
     * Şablon kullanarak mesaj gönder
     */
    public function sendTemplateMessage($branchId, $customerId, $templateKey, $variables = [], $messageType = 'sms', $appointmentId = null, $triggerType = 'auto')
    {
        // Müşteri bilgilerini al
        $customerModel = new \App\Models\CustomerModel();
        $customer = $customerModel->find($customerId);
        
        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Müşteri bulunamadı'
            ];
        }

        // Şablonu al
        $template = $this->messageTemplateModel->getTemplate($branchId, $templateKey, $messageType);
        
        if (!$template) {
            return [
                'success' => false,
                'message' => 'Mesaj şablonu bulunamadı'
            ];
        }

        // Varsayılan değişkenleri ekle
        $defaultVariables = [
            'musteri_adi' => $customer['first_name'] . ' ' . $customer['last_name'],
            'musteri_telefon' => $customer['phone'],
            'salon_adi' => 'Güzellik Salonu', // Bu ayarlardan alınabilir
            'salon_telefon' => '0212 XXX XX XX' // Bu ayarlardan alınabilir
        ];

        $allVariables = array_merge($defaultVariables, $variables);

        // Şablon içeriğini işle
        $message = $this->messageTemplateModel->processTemplate($template['template_content'], $allVariables);

        // Mesajı gönder
        if ($messageType === 'whatsapp') {
            return $this->sendWhatsApp($branchId, $customer['phone'], $message, $customerId, $appointmentId, $triggerType);
        } else {
            return $this->sendSMS($branchId, $customer['phone'], $message, $customerId, $appointmentId, $triggerType);
        }
    }

    /**
     * SMS sağlayıcısı ile gönder (Netgsm)
     */
    private function sendViaSmsProvider($config, $phone, $message)
    {
        $provider = $config['provider'] ?? 'netgsm';
        
        if ($provider === 'netgsm') {
            return $this->sendViaNetgsm($config, $phone, $message);
        }
        
        // Diğer sağlayıcılar buraya eklenebilir
        return [
            'success' => false,
            'message' => 'Desteklenmeyen SMS sağlayıcısı'
        ];
    }

    /**
     * Netgsm ile SMS gönder
     */
    private function sendViaNetgsm($config, $phone, $message)
    {
        $url = 'https://api.netgsm.com.tr/sms/send/get';
        
        $params = [
            'usercode' => $config['api_key'],
            'password' => $config['api_secret'],
            'gsmno' => $phone,
            'message' => $message,
            'msgheader' => $config['sender_name'] ?? 'SALON'
        ];

        try {
            $response = $this->httpClient->get($url, ['query' => $params]);
            $body = $response->getBody();
            
            // Netgsm yanıt kodlarını kontrol et
            if (strpos($body, '00') === 0) {
                return [
                    'success' => true,
                    'response' => $body,
                    'message' => 'SMS başarıyla gönderildi'
                ];
            } else {
                return [
                    'success' => false,
                    'response' => $body,
                    'message' => 'SMS gönderilemedi: ' . $this->getNetgsmErrorMessage($body)
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'response' => $e->getMessage(),
                'message' => 'API bağlantı hatası'
            ];
        }
    }

    /**
     * WAHA ile WhatsApp gönder
     */
    private function sendViaWhatsApp($config, $phone, $message)
    {
        $url = rtrim($config['api_url'], '/') . '/api/sendText';
        
        $data = [
            'session' => $config['session_name'] ?? 'default',
            'chatId' => $phone . '@c.us',
            'text' => $message
        ];

        try {
            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $config['api_token'],
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            $body = $response->getBody();
            $result = json_decode($body, true);
            
            if ($response->getStatusCode() === 200 && isset($result['id'])) {
                return [
                    'success' => true,
                    'response' => $body,
                    'message' => 'WhatsApp mesajı başarıyla gönderildi'
                ];
            } else {
                return [
                    'success' => false,
                    'response' => $body,
                    'message' => 'WhatsApp mesajı gönderilemedi'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'response' => $e->getMessage(),
                'message' => 'WhatsApp API bağlantı hatası'
            ];
        }
    }

    /**
     * Telefon numarasını temizle
     */
    private function cleanPhoneNumber($phone)
    {
        // Sadece rakamları al
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Türkiye formatına çevir
        if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
            $phone = '90' . substr($phone, 1);
        } elseif (strlen($phone) === 10) {
            $phone = '90' . $phone;
        } elseif (strlen($phone) === 13 && substr($phone, 0, 2) === '90') {
            // Zaten doğru format
        } else {
            return false; // Geçersiz format
        }
        
        return $phone;
    }

    /**
     * Netgsm hata mesajlarını çevir
     */
    private function getNetgsmErrorMessage($code)
    {
        $errors = [
            '01' => 'Mesaj gövdesi boş',
            '02' => 'Geçersiz kullanıcı adı/şifre',
            '03' => 'Yetersiz kredi',
            '04' => 'Geçersiz telefon numarası',
            '05' => 'Mesaj başlığı hatalı'
        ];
        
        return $errors[$code] ?? 'Bilinmeyen hata: ' . $code;
    }

    /**
     * Test mesajı gönder
     */
    public function sendTestMessage($branchId, $phone, $messageType = 'sms')
    {
        $message = 'Bu bir test mesajıdır. Bildirim sisteminiz çalışıyor! - ' . date('d.m.Y H:i');
        
        if ($messageType === 'whatsapp') {
            return $this->sendWhatsApp($branchId, $phone, $message, null, null, 'test');
        } else {
            return $this->sendSMS($branchId, $phone, $message, null, null, 'test');
        }
    }
}