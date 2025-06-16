<?php

namespace App\Models;

use CodeIgniter\Model;

class UserSettingModel extends Model
{
    protected $table = 'user_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'setting_key',
        'setting_value'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'setting_key' => 'required|string|max_length[100]',
        'setting_value' => 'permit_empty|string'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'Kullanıcı ID zorunludur.',
            'integer' => 'Geçerli bir kullanıcı ID giriniz.'
        ],
        'setting_key' => [
            'required' => 'Ayar anahtarı zorunludur.',
            'string' => 'Ayar anahtarı metin olmalıdır.',
            'max_length' => 'Ayar anahtarı en fazla 100 karakter olabilir.'
        ]
    ];

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
     * Kullanıcının belirli bir ayarını getir
     */
    public function getUserSetting($userId, $settingKey, $defaultValue = null)
    {
        $setting = $this->where('user_id', $userId)
                        ->where('setting_key', $settingKey)
                        ->first();

        return $setting ? $setting['setting_value'] : $defaultValue;
    }

    /**
     * Kullanıcının ayarını kaydet veya güncelle
     */
    public function setUserSetting($userId, $settingKey, $settingValue)
    {
        $existing = $this->where('user_id', $userId)
                         ->where('setting_key', $settingKey)
                         ->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'setting_value' => $settingValue
            ]);
        } else {
            return $this->insert([
                'user_id' => $userId,
                'setting_key' => $settingKey,
                'setting_value' => $settingValue
            ]);
        }
    }

    /**
     * Kullanıcının tüm ayarlarını getir
     */
    public function getUserSettings($userId)
    {
        $settings = $this->where('user_id', $userId)->findAll();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }

    /**
     * Kullanıcının birden fazla ayarını toplu olarak kaydet
     */
    public function setUserSettings($userId, $settings)
    {
        $success = true;
        
        foreach ($settings as $key => $value) {
            if (!$this->setUserSetting($userId, $key, $value)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Varsayılan ayarları getir
     */
    public function getDefaultSettings()
    {
        return [
            'theme_mode' => 'light', // light, dark
            'notifications_enabled' => '1',
            'notification_sound' => '1',
            'notification_desktop' => '1',
            'notification_email' => '1',
            'language' => 'tr',
            'timezone' => 'Europe/Istanbul'
        ];
    }

    /**
     * Kullanıcının ayarlarını varsayılanlarla birleştir
     */
    public function getUserSettingsWithDefaults($userId)
    {
        $userSettings = $this->getUserSettings($userId);
        $defaultSettings = $this->getDefaultSettings();
        
        return array_merge($defaultSettings, $userSettings);
    }
}