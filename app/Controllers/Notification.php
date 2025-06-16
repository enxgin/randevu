<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationSettingModel;
use App\Models\MessageTemplateModel;
use App\Models\SentMessageModel;
use App\Models\NotificationTriggerModel;
use App\Models\NotificationQueueModel;
use App\Libraries\NotificationService;
use App\Libraries\NotificationTriggerService;

class Notification extends BaseController
{
    protected $notificationSettingModel;
    protected $messageTemplateModel;
    protected $sentMessageModel;
    protected $triggerModel;
    protected $queueModel;
    protected $notificationService;
    protected $triggerService;

    public function __construct()
    {
        $this->notificationSettingModel = new NotificationSettingModel();
        $this->messageTemplateModel = new MessageTemplateModel();
        $this->sentMessageModel = new SentMessageModel();
        $this->triggerModel = new NotificationTriggerModel();
        $this->queueModel = new NotificationQueueModel();
        $this->notificationService = new NotificationService();
        $this->triggerService = new NotificationTriggerService();
    }

    /**
     * Bildirim ayarları ana sayfası
     */
    public function index()
    {
        $branchId = session('branch_id');
        
        // Mevcut ayarları al
        $settings = $this->notificationSettingModel->getSettingsByBranch($branchId);
        $settingsArray = [];
        
        foreach ($settings as $setting) {
            $settingsArray[$setting['setting_key']] = $setting['setting_value'];
        }

        // Mesaj istatistikleri
        $messageStats = $this->sentMessageModel->getMessageStats($branchId, date('Y-m-01'), date('Y-m-t'));

        $data = [
            'title' => 'Bildirim Ayarları',
            'settings' => $settingsArray,
            'messageStats' => $messageStats
        ];

        return view('notifications/settings', $data);
    }

    /**
     * Bildirim ayarlarını kaydet
     */
    public function saveSettings()
    {
        $branchId = session('branch_id');
        $rules = [
            'sms_enabled' => 'permit_empty|in_list[0,1]',
            'whatsapp_enabled' => 'permit_empty|in_list[0,1]',
            'sms_provider' => 'permit_empty|max_length[50]',
            'sms_api_key' => 'permit_empty|max_length[200]',
            'sms_api_secret' => 'permit_empty|max_length[200]',
            'sms_sender_name' => 'permit_empty|max_length[11]',
            'whatsapp_api_url' => 'permit_empty|valid_url',
            'whatsapp_api_token' => 'permit_empty|max_length[500]',
            'whatsapp_session_name' => 'permit_empty|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $settings = [
                'sms_enabled' => $this->request->getPost('sms_enabled') ? '1' : '0',
                'whatsapp_enabled' => $this->request->getPost('whatsapp_enabled') ? '1' : '0',
                'sms_provider' => $this->request->getPost('sms_provider') ?: 'netgsm',
                'sms_api_key' => $this->request->getPost('sms_api_key'),
                'sms_api_secret' => $this->request->getPost('sms_api_secret'),
                'sms_sender_name' => $this->request->getPost('sms_sender_name'),
                'whatsapp_api_url' => $this->request->getPost('whatsapp_api_url'),
                'whatsapp_api_token' => $this->request->getPost('whatsapp_api_token'),
                'whatsapp_session_name' => $this->request->getPost('whatsapp_session_name')
            ];

            foreach ($settings as $key => $value) {
                $this->notificationSettingModel->saveSetting($branchId, $key, $value);
            }

            return redirect()->to('/notifications')->with('success', 'Bildirim ayarları başarıyla kaydedildi.');
        } catch (\Exception $e) {
            log_message('error', 'Bildirim ayarları kaydetme hatası: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Ayarlar kaydedilirken bir hata oluştu.');
        }
    }

    /**
     * Test mesajı gönder
     */
    public function sendTest()
    {
        $branchId = session('branch_id');
        $phone = $this->request->getPost('phone');
        $messageType = $this->request->getPost('message_type') ?: 'sms';

        if (!$phone) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Telefon numarası gerekli'
            ]);
        }

        try {
            $result = $this->notificationService->sendTestMessage($branchId, $phone, $messageType);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Test mesajı gönderme hatası: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test mesajı gönderilemedi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mesaj şablonları
     */
    public function templates()
    {
        $branchId = session('branch_id');
        $templates = $this->messageTemplateModel->getTemplatesByBranch($branchId);
        $availableVariables = $this->messageTemplateModel->getAvailableVariables();

        $data = [
            'title' => 'Mesaj Şablonları',
            'templates' => $templates,
            'availableVariables' => $availableVariables
        ];

        return view('notifications/templates', $data);
    }

    /**
     * Yeni şablon oluştur
     */
    public function createTemplate()
    {
        $availableVariables = $this->messageTemplateModel->getAvailableVariables();

        $data = [
            'title' => 'Yeni Mesaj Şablonu',
            'availableVariables' => $availableVariables
        ];

        return view('notifications/create_template', $data);
    }

    /**
     * Şablon kaydet
     */
    public function saveTemplate()
    {
        $branchId = session('branch_id');
        $rules = [
            'template_key' => 'required|max_length[100]',
            'template_name' => 'required|max_length[200]',
            'template_content' => 'required',
            'template_type' => 'required|in_list[sms,whatsapp]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'branch_id' => $branchId,
                'template_key' => $this->request->getPost('template_key'),
                'template_name' => $this->request->getPost('template_name'),
                'template_content' => $this->request->getPost('template_content'),
                'template_type' => $this->request->getPost('template_type'),
                'is_active' => 1
            ];

            $this->messageTemplateModel->insert($data);

            return redirect()->to('/notifications/templates')->with('success', 'Mesaj şablonu başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            log_message('error', 'Şablon kaydetme hatası: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Şablon kaydedilirken bir hata oluştu.');
        }
    }

    /**
     * Şablon düzenle
     */
    public function editTemplate($id)
    {
        $branchId = session('branch_id');
        $template = $this->messageTemplateModel->where('branch_id', $branchId)->find($id);

        if (!$template) {
            return redirect()->to('/notifications/templates')->with('error', 'Şablon bulunamadı.');
        }

        $availableVariables = $this->messageTemplateModel->getAvailableVariables();

        $data = [
            'title' => 'Mesaj Şablonu Düzenle',
            'template' => $template,
            'availableVariables' => $availableVariables
        ];

        return view('notifications/edit_template', $data);
    }

    /**
     * Şablon güncelle
     */
    public function updateTemplate($id)
    {
        $branchId = session('branch_id');
        $template = $this->messageTemplateModel->where('branch_id', $branchId)->find($id);

        if (!$template) {
            return redirect()->to('/notifications/templates')->with('error', 'Şablon bulunamadı.');
        }

        $rules = [
            'template_name' => 'required|max_length[200]',
            'template_content' => 'required',
            'template_type' => 'required|in_list[sms,whatsapp]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'template_name' => $this->request->getPost('template_name'),
                'template_content' => $this->request->getPost('template_content'),
                'template_type' => $this->request->getPost('template_type')
            ];

            $this->messageTemplateModel->update($id, $data);

            return redirect()->to('/notifications/templates')->with('success', 'Mesaj şablonu başarıyla güncellendi.');
        } catch (\Exception $e) {
            log_message('error', 'Şablon güncelleme hatası: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Şablon güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Şablon sil
     */
    public function deleteTemplate($id)
    {
        $branchId = session('branch_id');
        $template = $this->messageTemplateModel->where('branch_id', $branchId)->find($id);

        if (!$template) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Şablon bulunamadı'
            ]);
        }

        try {
            $this->messageTemplateModel->delete($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Şablon başarıyla silindi'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Şablon silme hatası: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Şablon silinirken bir hata oluştu'
            ]);
        }
    }

    /**
     * Gönderilen mesajlar
     */
    public function messages()
    {
        $branchId = session('branch_id');
        
        // Filtreleme
        $filters = [
            'status' => $this->request->getGet('status'),
            'message_type' => $this->request->getGet('message_type'),
            'trigger_type' => $this->request->getGet('trigger_type'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $messages = $this->sentMessageModel->getBranchMessages($branchId, $filters);
        $pager = $this->sentMessageModel->pager;

        $data = [
            'title' => 'Gönderilen Mesajlar',
            'messages' => $messages,
            'pager' => $pager,
            'filters' => $filters
        ];

        return view('notifications/messages', $data);
    }

    /**
     * Varsayılan şablonları oluştur
     */
    public function createDefaultTemplates()
    {
        $branchId = session('branch_id');

        try {
            $this->messageTemplateModel->createDefaultTemplates($branchId);
            return redirect()->to('/notifications/templates')->with('success', 'Varsayılan şablonlar başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            log_message('error', 'Varsayılan şablon oluşturma hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Varsayılan şablonlar oluşturulurken bir hata oluştu.');
        }
    }

    /**
     * Tetikleyici kuralları
     */
    public function triggers()
    {
        $branchId = session('branch_id');
        $triggers = $this->triggerModel->getTriggersByBranch($branchId);
        $templates = $this->messageTemplateModel->getTemplatesByBranch($branchId);

        $data = [
            'title' => 'Bildirim Tetikleyicileri',
            'triggers' => $triggers,
            'templates' => $templates
        ];

        return view('notifications/triggers', $data);
    }

    /**
     * Yeni tetikleyici oluştur
     */
    public function createTrigger()
    {
        $branchId = session('branch_id');
        $templates = $this->messageTemplateModel->getTemplatesByBranch($branchId);

        $data = [
            'title' => 'Yeni Tetikleyici Kuralı',
            'templates' => $templates,
            'triggerTypes' => [
                'appointment_reminder' => 'Randevu Hatırlatma',
                'package_warning' => 'Paket Uyarısı',
                'no_show_notification' => 'Gelmedi Bildirimi',
                'birthday_greeting' => 'Doğum Günü Kutlaması'
            ]
        ];

        return view('notifications/create_trigger', $data);
    }

    /**
     * Tetikleyici kaydet
     */
    public function saveTrigger()
    {
        $branchId = session('branch_id');
        $rules = [
            'trigger_type' => 'required|in_list[appointment_reminder,package_warning,no_show_notification,birthday_greeting]',
            'trigger_name' => 'required|max_length[200]',
            'message_template_id' => 'required|integer',
            'message_type' => 'required|in_list[sms,whatsapp,both]',
            'send_before_minutes' => 'permit_empty|integer',
            'send_after_minutes' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $triggerType = $this->request->getPost('trigger_type');
            $conditions = [];

            // Tetikleyici türüne göre koşulları ayarla
            if ($triggerType === 'appointment_reminder') {
                $conditions['reminder_type'] = $this->request->getPost('send_before_minutes') == 1440 ? '24_hours' : '2_hours';
            } elseif ($triggerType === 'package_warning') {
                $conditions['warning_type'] = 'last_session';
            } elseif ($triggerType === 'no_show_notification') {
                $conditions['notification_type'] = 'no_show';
            } elseif ($triggerType === 'birthday_greeting') {
                $conditions['greeting_type'] = 'birthday';
            }

            $data = [
                'branch_id' => $branchId,
                'trigger_type' => $triggerType,
                'trigger_name' => $this->request->getPost('trigger_name'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'trigger_conditions' => json_encode($conditions),
                'message_template_id' => $this->request->getPost('message_template_id'),
                'message_type' => $this->request->getPost('message_type'),
                'send_before_minutes' => $this->request->getPost('send_before_minutes') ?: null,
                'send_after_minutes' => $this->request->getPost('send_after_minutes') ?: null
            ];

            $this->triggerModel->insert($data);

            return redirect()->to('/notifications/triggers')->with('success', 'Tetikleyici kuralı başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            log_message('error', 'Tetikleyici kaydetme hatası: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Tetikleyici kaydedilirken bir hata oluştu.');
        }
    }

    /**
     * Tetikleyici düzenle
     */
    public function editTrigger($id)
    {
        $branchId = session('branch_id');
        $trigger = $this->triggerModel->where('branch_id', $branchId)->find($id);

        if (!$trigger) {
            return redirect()->to('/notifications/triggers')->with('error', 'Tetikleyici bulunamadı.');
        }

        $templates = $this->messageTemplateModel->getTemplatesByBranch($branchId);

        $data = [
            'title' => 'Tetikleyici Düzenle',
            'trigger' => $trigger,
            'templates' => $templates,
            'triggerTypes' => [
                'appointment_reminder' => 'Randevu Hatırlatma',
                'package_warning' => 'Paket Uyarısı',
                'no_show_notification' => 'Gelmedi Bildirimi',
                'birthday_greeting' => 'Doğum Günü Kutlaması'
            ]
        ];

        return view('notifications/edit_trigger', $data);
    }

    /**
     * Tetikleyici güncelle
     */
    public function updateTrigger($id)
    {
        $branchId = session('branch_id');
        $trigger = $this->triggerModel->where('branch_id', $branchId)->find($id);

        if (!$trigger) {
            return redirect()->to('/notifications/triggers')->with('error', 'Tetikleyici bulunamadı.');
        }

        $rules = [
            'trigger_name' => 'required|max_length[200]',
            'message_template_id' => 'required|integer',
            'message_type' => 'required|in_list[sms,whatsapp,both]',
            'send_before_minutes' => 'permit_empty|integer',
            'send_after_minutes' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'trigger_name' => $this->request->getPost('trigger_name'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'message_template_id' => $this->request->getPost('message_template_id'),
                'message_type' => $this->request->getPost('message_type'),
                'send_before_minutes' => $this->request->getPost('send_before_minutes') ?: null,
                'send_after_minutes' => $this->request->getPost('send_after_minutes') ?: null
            ];

            $this->triggerModel->update($id, $data);

            return redirect()->to('/notifications/triggers')->with('success', 'Tetikleyici kuralı başarıyla güncellendi.');
        } catch (\Exception $e) {
            log_message('error', 'Tetikleyici güncelleme hatası: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Tetikleyici güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Tetikleyici sil
     */
    public function deleteTrigger($id)
    {
        $branchId = session('branch_id');
        $trigger = $this->triggerModel->where('branch_id', $branchId)->find($id);

        if (!$trigger) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tetikleyici bulunamadı'
            ]);
        }

        try {
            $this->triggerModel->delete($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tetikleyici başarıyla silindi'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Tetikleyici silme hatası: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tetikleyici silinirken bir hata oluştu'
            ]);
        }
    }

    /**
     * Tetikleyici durumunu değiştir
     */
    public function toggleTrigger($id)
    {
        $branchId = session('branch_id');

        try {
            $result = $this->triggerModel->toggleTriggerStatus($id, $branchId);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tetikleyici durumu güncellendi'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tetikleyici bulunamadı'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Tetikleyici durum değiştirme hatası: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Durum güncellenirken bir hata oluştu'
            ]);
        }
    }

    /**
     * Bildirim kuyruğu
     */
    public function queue()
    {
        $branchId = session('branch_id');
        
        // Filtreleme
        $filters = [
            'status' => $this->request->getGet('status'),
            'message_type' => $this->request->getGet('message_type'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $queueMessages = $this->queueModel->getQueueByBranch($branchId, $filters);
        $pager = $this->queueModel->pager;
        $stats = $this->queueModel->getQueueStats($branchId);

        $data = [
            'title' => 'Bildirim Kuyruğu',
            'queueMessages' => $queueMessages,
            'pager' => $pager,
            'filters' => $filters,
            'stats' => $stats
        ];

        return view('notifications/queue', $data);
    }

    /**
     * Varsayılan tetikleyicileri oluştur
     */
    public function createDefaultTriggers()
    {
        $branchId = session('branch_id');

        try {
            // Önce varsayılan şablonları oluştur
            $this->messageTemplateModel->createDefaultTemplates($branchId);
            
            // Sonra varsayılan tetikleyicileri oluştur
            $this->triggerModel->createDefaultTriggers($branchId);
            
            return redirect()->to('/notifications/triggers')->with('success', 'Varsayılan tetikleyiciler başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            log_message('error', 'Varsayılan tetikleyici oluşturma hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Varsayılan tetikleyiciler oluşturulurken bir hata oluştu.');
        }
    }

    /**
     * Tetikleyici test et
     */
    public function testTrigger()
    {
        $triggerId = $this->request->getPost('trigger_id');
        $customerId = $this->request->getPost('customer_id');

        if (!$triggerId || !$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tetikleyici ve müşteri seçimi gerekli'
            ]);
        }

        try {
            $result = $this->triggerService->testTrigger($triggerId, $customerId);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Tetikleyici test hatası: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test mesajı gönderilemedi: ' . $e->getMessage()
            ]);
        }
    }
}