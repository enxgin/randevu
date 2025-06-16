<?php

namespace App\Libraries;

use App\Models\NotificationTriggerModel;
use App\Models\NotificationQueueModel;
use App\Models\MessageTemplateModel;
use App\Models\AppointmentModel;
use App\Models\CustomerModel;
use App\Models\CustomerPackageModel;
use App\Libraries\NotificationService;

class NotificationTriggerService
{
    protected $triggerModel;
    protected $queueModel;
    protected $templateModel;
    protected $appointmentModel;
    protected $customerModel;
    protected $customerPackageModel;
    protected $notificationService;

    public function __construct()
    {
        $this->triggerModel = new NotificationTriggerModel();
        $this->queueModel = new NotificationQueueModel();
        $this->templateModel = new MessageTemplateModel();
        $this->appointmentModel = new AppointmentModel();
        $this->customerModel = new CustomerModel();
        $this->customerPackageModel = new CustomerPackageModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Randevu oluşturulduğunda hatırlatma mesajlarını planla
     */
    public function scheduleAppointmentReminders($appointmentId)
    {
        $appointment = $this->appointmentModel->getAppointmentDetails($appointmentId);
        
        if (!$appointment) {
            return false;
        }

        // Randevu hatırlatma tetikleyicilerini al
        $triggers = $this->triggerModel->getAppointmentReminderTriggers($appointment['branch_id']);

        foreach ($triggers as $trigger) {
            if (!$trigger['send_before_minutes']) {
                continue;
            }

            // Gönderim zamanını hesapla
            $appointmentTime = strtotime($appointment['start_time']);
            $sendTime = $appointmentTime - ($trigger['send_before_minutes'] * 60);
            $scheduledAt = date('Y-m-d H:i:s', $sendTime);

            // Geçmiş bir zaman ise atla
            if ($sendTime <= time()) {
                continue;
            }

            // Mesaj içeriğini hazırla
            $variables = [
                'randevu_tarihi' => date('d.m.Y', $appointmentTime),
                'randevu_saati' => date('H:i', $appointmentTime),
                'hizmet_adi' => $appointment['service_name'],
                'personel_adi' => $appointment['staff_name']
            ];

            $message = $this->templateModel->processTemplate($trigger['template_content'], $variables);

            // Mesaj türlerini belirle
            $messageTypes = $trigger['message_type'] === 'both' ? ['sms', 'whatsapp'] : [$trigger['message_type']];

            foreach ($messageTypes as $messageType) {
                $this->queueModel->scheduleAppointmentReminder(
                    $appointment['branch_id'],
                    $trigger['id'],
                    $appointment['customer_id'],
                    $appointmentId,
                    $messageType,
                    $appointment['customer_phone'],
                    $message,
                    $scheduledAt
                );
            }
        }

        return true;
    }

    /**
     * Randevu güncellendiğinde hatırlatma mesajlarını yeniden planla
     */
    public function rescheduleAppointmentReminders($appointmentId)
    {
        // Mevcut bekleyen mesajları iptal et
        $this->queueModel->cancelAppointmentMessages($appointmentId);

        // Yeni mesajları planla
        return $this->scheduleAppointmentReminders($appointmentId);
    }

    /**
     * Randevu iptal edildiğinde mesajları iptal et
     */
    public function cancelAppointmentReminders($appointmentId)
    {
        return $this->queueModel->cancelAppointmentMessages($appointmentId);
    }

    /**
     * Randevu tamamlandığında paket uyarısı kontrol et
     */
    public function checkPackageWarning($appointmentId)
    {
        $appointment = $this->appointmentModel->getAppointmentDetails($appointmentId);
        
        if (!$appointment || !$appointment['customer_package_id']) {
            return false;
        }

        // Müşterinin paket bilgilerini al
        $package = $this->customerPackageModel->getCustomerPackageDetails($appointment['customer_package_id']);
        
        if (!$package) {
            return false;
        }

        // Son seans/dakika kontrolü
        $isLastSession = false;
        
        if ($package['package_type'] === 'session') {
            $isLastSession = $package['remaining_sessions'] <= 1;
        } else {
            $isLastSession = $package['remaining_minutes'] <= 30; // 30 dakika kaldığında uyar
        }

        if (!$isLastSession) {
            return false;
        }

        // Paket uyarı tetikleyicilerini al
        $triggers = $this->triggerModel->getPackageWarningTriggers($appointment['branch_id']);

        foreach ($triggers as $trigger) {
            // Mesaj içeriğini hazırla
            $variables = [
                'paket_adi' => $package['package_name'],
                'kalan_seans' => $package['remaining_sessions'],
                'kalan_dakika' => $package['remaining_minutes']
            ];

            $message = $this->templateModel->processTemplate($trigger['template_content'], $variables);

            // Mesaj türlerini belirle
            $messageTypes = $trigger['message_type'] === 'both' ? ['sms', 'whatsapp'] : [$trigger['message_type']];

            foreach ($messageTypes as $messageType) {
                $this->queueModel->schedulePackageWarning(
                    $appointment['branch_id'],
                    $trigger['id'],
                    $appointment['customer_id'],
                    $messageType,
                    $appointment['customer_phone'],
                    $message
                );
            }
        }

        return true;
    }

    /**
     * No-show bildirimi planla
     */
    public function scheduleNoShowNotification($appointmentId)
    {
        $appointment = $this->appointmentModel->getAppointmentDetails($appointmentId);
        
        if (!$appointment) {
            return false;
        }

        // No-show tetikleyicilerini al
        $triggers = $this->triggerModel->getNoShowTriggers($appointment['branch_id']);

        foreach ($triggers as $trigger) {
            if (!$trigger['send_after_minutes']) {
                continue;
            }

            // Gönderim zamanını hesapla
            $appointmentTime = strtotime($appointment['start_time']);
            $sendTime = $appointmentTime + ($trigger['send_after_minutes'] * 60);
            $scheduledAt = date('Y-m-d H:i:s', $sendTime);

            // Mesaj içeriğini hazırla
            $variables = [
                'randevu_tarihi' => date('d.m.Y', $appointmentTime),
                'randevu_saati' => date('H:i', $appointmentTime)
            ];

            $message = $this->templateModel->processTemplate($trigger['template_content'], $variables);

            // Mesaj türlerini belirle
            $messageTypes = $trigger['message_type'] === 'both' ? ['sms', 'whatsapp'] : [$trigger['message_type']];

            foreach ($messageTypes as $messageType) {
                $this->queueModel->scheduleNoShowNotification(
                    $appointment['branch_id'],
                    $trigger['id'],
                    $appointment['customer_id'],
                    $appointmentId,
                    $messageType,
                    $appointment['customer_phone'],
                    $message,
                    $scheduledAt
                );
            }
        }

        return true;
    }

    /**
     * Doğum günü kutlama mesajlarını planla
     */
    public function scheduleBirthdayGreetings()
    {
        // Bugün doğum günü olan müşterileri al
        $today = date('m-d');
        $customers = $this->customerModel->getCustomersByBirthday($today);

        foreach ($customers as $customer) {
            // Doğum günü tetikleyicilerini al
            $triggers = $this->triggerModel->getBirthdayTriggers($customer['branch_id']);

            foreach ($triggers as $trigger) {
                // Mesaj içeriğini hazırla
                $variables = [
                    'dogum_gunu' => date('d.m.Y', strtotime($customer['birth_date']))
                ];

                $message = $this->templateModel->processTemplate($trigger['template_content'], $variables);

                // Gönderim zamanını belirle (sabah 10:00)
                $scheduledAt = date('Y-m-d 10:00:00');

                // Mesaj türlerini belirle
                $messageTypes = $trigger['message_type'] === 'both' ? ['sms', 'whatsapp'] : [$trigger['message_type']];

                foreach ($messageTypes as $messageType) {
                    $this->queueModel->scheduleBirthdayGreeting(
                        $customer['branch_id'],
                        $trigger['id'],
                        $customer['id'],
                        $messageType,
                        $customer['phone'],
                        $message,
                        $scheduledAt
                    );
                }
            }
        }

        return true;
    }

    /**
     * Kuyruktaki mesajları işle ve gönder
     */
    public function processQueue($limit = 50)
    {
        $pendingMessages = $this->queueModel->getPendingMessages($limit);
        $processedCount = 0;
        $successCount = 0;
        $failedCount = 0;

        foreach ($pendingMessages as $message) {
            $processedCount++;

            try {
                // Mesajı gönder
                if ($message['message_type'] === 'whatsapp') {
                    $result = $this->notificationService->sendWhatsApp(
                        $message['branch_id'],
                        $message['recipient_phone'],
                        $message['message_content'],
                        $message['customer_id'],
                        $message['appointment_id'],
                        'auto'
                    );
                } else {
                    $result = $this->notificationService->sendSMS(
                        $message['branch_id'],
                        $message['recipient_phone'],
                        $message['message_content'],
                        $message['customer_id'],
                        $message['appointment_id'],
                        'auto'
                    );
                }

                if ($result['success']) {
                    $this->queueModel->updateMessageStatus($message['id'], 'sent');
                    $successCount++;
                } else {
                    $this->queueModel->updateMessageStatus($message['id'], 'failed', $result['message']);
                    $failedCount++;
                }

            } catch (\Exception $e) {
                $this->queueModel->updateMessageStatus($message['id'], 'failed', $e->getMessage());
                $failedCount++;
                log_message('error', 'Kuyruk mesajı gönderim hatası: ' . $e->getMessage());
            }

            // CPU'yu rahatlatmak için kısa bir bekleme
            usleep(100000); // 0.1 saniye
        }

        return [
            'processed' => $processedCount,
            'success' => $successCount,
            'failed' => $failedCount
        ];
    }

    /**
     * Tetikleyici kurallarını test et
     */
    public function testTrigger($triggerId, $customerId, $appointmentId = null)
    {
        $trigger = $this->triggerModel->find($triggerId);
        $customer = $this->customerModel->find($customerId);

        if (!$trigger || !$customer) {
            return [
                'success' => false,
                'message' => 'Tetikleyici veya müşteri bulunamadı'
            ];
        }

        // Test mesajı hazırla
        $variables = [
            'randevu_tarihi' => date('d.m.Y'),
            'randevu_saati' => date('H:i'),
            'hizmet_adi' => 'Test Hizmeti',
            'personel_adi' => 'Test Personeli',
            'paket_adi' => 'Test Paketi',
            'kalan_seans' => '1',
            'kalan_dakika' => '30'
        ];

        $template = $this->templateModel->find($trigger['message_template_id']);
        $message = $this->templateModel->processTemplate($template['template_content'], $variables);

        // Test mesajını gönder
        if ($trigger['message_type'] === 'whatsapp' || $trigger['message_type'] === 'both') {
            $result = $this->notificationService->sendWhatsApp(
                $trigger['branch_id'],
                $customer['phone'],
                '[TEST] ' . $message,
                $customerId,
                $appointmentId,
                'test'
            );
        } else {
            $result = $this->notificationService->sendSMS(
                $trigger['branch_id'],
                $customer['phone'],
                '[TEST] ' . $message,
                $customerId,
                $appointmentId,
                'test'
            );
        }

        return $result;
    }
}