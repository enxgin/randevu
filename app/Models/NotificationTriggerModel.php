<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationTriggerModel extends Model
{
    protected $table = 'notification_triggers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'trigger_type',
        'trigger_name',
        'is_active',
        'trigger_conditions',
        'message_template_id',
        'message_type',
        'send_before_minutes',
        'send_after_minutes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'branch_id' => 'required|integer',
        'trigger_type' => 'required|in_list[appointment_reminder,package_warning,no_show_notification,birthday_greeting]',
        'trigger_name' => 'required|max_length[200]',
        'message_type' => 'required|in_list[sms,whatsapp,both]'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required' => 'Şube seçimi zorunludur',
            'integer' => 'Geçersiz şube'
        ],
        'trigger_type' => [
            'required' => 'Tetikleyici türü seçimi zorunludur',
            'in_list' => 'Geçersiz tetikleyici türü'
        ],
        'trigger_name' => [
            'required' => 'Tetikleyici adı zorunludur',
            'max_length' => 'Tetikleyici adı en fazla 200 karakter olabilir'
        ],
        'message_type' => [
            'required' => 'Mesaj türü seçimi zorunludur',
            'in_list' => 'Geçersiz mesaj türü'
        ]
    ];

    /**
     * Şubeye göre tetikleyicileri getir
     */
    public function getTriggersByBranch($branchId, $isActive = null)
    {
        $builder = $this->select('notification_triggers.*, message_templates.template_name')
                        ->join('message_templates', 'message_templates.id = notification_triggers.message_template_id', 'left')
                        ->where('notification_triggers.branch_id', $branchId);

        if ($isActive !== null) {
            $builder->where('notification_triggers.is_active', $isActive);
        }

        return $builder->orderBy('notification_triggers.trigger_type')
                      ->orderBy('notification_triggers.trigger_name')
                      ->findAll();
    }

    /**
     * Tetikleyici türüne göre aktif tetikleyicileri getir
     */
    public function getActiveTriggersByType($branchId, $triggerType)
    {
        return $this->select('notification_triggers.*, message_templates.template_content, message_templates.template_type')
                    ->join('message_templates', 'message_templates.id = notification_triggers.message_template_id', 'left')
                    ->where('notification_triggers.branch_id', $branchId)
                    ->where('notification_triggers.trigger_type', $triggerType)
                    ->where('notification_triggers.is_active', 1)
                    ->findAll();
    }

    /**
     * Randevu hatırlatma tetikleyicilerini getir
     */
    public function getAppointmentReminderTriggers($branchId)
    {
        return $this->getActiveTriggersByType($branchId, 'appointment_reminder');
    }

    /**
     * Paket uyarı tetikleyicilerini getir
     */
    public function getPackageWarningTriggers($branchId)
    {
        return $this->getActiveTriggersByType($branchId, 'package_warning');
    }

    /**
     * No-show bildirim tetikleyicilerini getir
     */
    public function getNoShowTriggers($branchId)
    {
        return $this->getActiveTriggersByType($branchId, 'no_show_notification');
    }

    /**
     * Doğum günü kutlama tetikleyicilerini getir
     */
    public function getBirthdayTriggers($branchId)
    {
        return $this->getActiveTriggersByType($branchId, 'birthday_greeting');
    }

    /**
     * Varsayılan tetikleyicileri oluştur
     */
    public function createDefaultTriggers($branchId)
    {
        $messageTemplateModel = new MessageTemplateModel();
        
        // Varsayılan şablonları al
        $templates = $messageTemplateModel->getTemplatesByBranch($branchId);
        $templateMap = [];
        foreach ($templates as $template) {
            $templateMap[$template['template_key']] = $template['id'];
        }

        $defaultTriggers = [
            [
                'branch_id' => $branchId,
                'trigger_type' => 'appointment_reminder',
                'trigger_name' => 'Randevu Hatırlatma - 24 Saat Önce',
                'is_active' => 1,
                'trigger_conditions' => json_encode(['reminder_type' => '24_hours']),
                'message_template_id' => $templateMap['appointment_reminder_24h'] ?? null,
                'message_type' => 'sms',
                'send_before_minutes' => 1440, // 24 saat = 1440 dakika
                'send_after_minutes' => null
            ],
            [
                'branch_id' => $branchId,
                'trigger_type' => 'appointment_reminder',
                'trigger_name' => 'Randevu Hatırlatma - 2 Saat Önce',
                'is_active' => 1,
                'trigger_conditions' => json_encode(['reminder_type' => '2_hours']),
                'message_template_id' => $templateMap['appointment_reminder_2h'] ?? null,
                'message_type' => 'sms',
                'send_before_minutes' => 120, // 2 saat = 120 dakika
                'send_after_minutes' => null
            ],
            [
                'branch_id' => $branchId,
                'trigger_type' => 'package_warning',
                'trigger_name' => 'Paket Uyarısı - Son Seans',
                'is_active' => 1,
                'trigger_conditions' => json_encode(['warning_type' => 'last_session']),
                'message_template_id' => $templateMap['package_warning'] ?? null,
                'message_type' => 'sms',
                'send_before_minutes' => null,
                'send_after_minutes' => null
            ],
            [
                'branch_id' => $branchId,
                'trigger_type' => 'no_show_notification',
                'trigger_name' => 'Gelmedi Bildirimi',
                'is_active' => 1,
                'trigger_conditions' => json_encode(['notification_type' => 'no_show']),
                'message_template_id' => $templateMap['no_show_notification'] ?? null,
                'message_type' => 'sms',
                'send_before_minutes' => null,
                'send_after_minutes' => 60 // 1 saat sonra
            ],
            [
                'branch_id' => $branchId,
                'trigger_type' => 'birthday_greeting',
                'trigger_name' => 'Doğum Günü Kutlaması',
                'is_active' => 0, // Varsayılan olarak kapalı
                'trigger_conditions' => json_encode(['greeting_type' => 'birthday']),
                'message_template_id' => $templateMap['birthday_greeting'] ?? null,
                'message_type' => 'sms',
                'send_before_minutes' => null,
                'send_after_minutes' => null
            ]
        ];

        foreach ($defaultTriggers as $trigger) {
            // Aynı tetikleyici zaten varsa ekleme
            $existing = $this->where('branch_id', $branchId)
                            ->where('trigger_type', $trigger['trigger_type'])
                            ->where('trigger_name', $trigger['trigger_name'])
                            ->first();
            
            if (!$existing) {
                $this->insert($trigger);
            }
        }

        return true;
    }

    /**
     * Tetikleyici durumunu değiştir
     */
    public function toggleTriggerStatus($id, $branchId)
    {
        $trigger = $this->where('branch_id', $branchId)->find($id);
        
        if (!$trigger) {
            return false;
        }

        $newStatus = $trigger['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }
}