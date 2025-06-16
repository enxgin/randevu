<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationSettingModel extends Model
{
    protected $table = 'notification_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'setting_key',
        'setting_value',
        'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'branch_id' => 'required|integer',
        'setting_key' => 'required|max_length[100]',
        'setting_value' => 'permit_empty',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Şube bazlı ayarları getir
     */
    public function getSettingsByBranch($branchId)
    {
        return $this->where('branch_id', $branchId)
                   ->where('is_active', 1)
                   ->findAll();
    }

    /**
     * Belirli bir ayarı getir
     */
    public function getSetting($branchId, $settingKey)
    {
        $setting = $this->where('branch_id', $branchId)
                       ->where('setting_key', $settingKey)
                       ->where('is_active', 1)
                       ->first();
        
        return $setting ? $setting['setting_value'] : null;
    }

    /**
     * Ayar kaydet veya güncelle
     */
    public function saveSetting($branchId, $settingKey, $settingValue)
    {
        $existing = $this->where('branch_id', $branchId)
                        ->where('setting_key', $settingKey)
                        ->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'setting_value' => $settingValue,
                'is_active' => 1
            ]);
        } else {
            return $this->insert([
                'branch_id' => $branchId,
                'setting_key' => $settingKey,
                'setting_value' => $settingValue,
                'is_active' => 1
            ]);
        }
    }

    /**
     * SMS ayarlarını kontrol et
     */
    public function isSmsEnabled($branchId)
    {
        $enabled = $this->getSetting($branchId, 'sms_enabled');
        return $enabled === '1' || $enabled === 'true';
    }

    /**
     * WhatsApp ayarlarını kontrol et
     */
    public function isWhatsAppEnabled($branchId)
    {
        $enabled = $this->getSetting($branchId, 'whatsapp_enabled');
        return $enabled === '1' || $enabled === 'true';
    }

    /**
     * SMS API bilgilerini getir
     */
    public function getSmsConfig($branchId)
    {
        return [
            'api_key' => $this->getSetting($branchId, 'sms_api_key'),
            'api_secret' => $this->getSetting($branchId, 'sms_api_secret'),
            'sender_name' => $this->getSetting($branchId, 'sms_sender_name'),
            'provider' => $this->getSetting($branchId, 'sms_provider') ?: 'netgsm'
        ];
    }

    /**
     * WhatsApp API bilgilerini getir
     */
    public function getWhatsAppConfig($branchId)
    {
        return [
            'api_url' => $this->getSetting($branchId, 'whatsapp_api_url'),
            'api_token' => $this->getSetting($branchId, 'whatsapp_api_token'),
            'session_name' => $this->getSetting($branchId, 'whatsapp_session_name')
        ];
    }
}