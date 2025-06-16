<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageTemplateModel extends Model
{
    protected $table = 'message_templates';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'template_key',
        'template_name',
        'template_content',
        'template_type',
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
        'template_key' => 'required|max_length[100]',
        'template_name' => 'required|max_length[200]',
        'template_content' => 'required',
        'template_type' => 'required|in_list[sms,whatsapp]',
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
     * Şube bazlı şablonları getir
     */
    public function getTemplatesByBranch($branchId, $templateType = null)
    {
        $builder = $this->where('branch_id', $branchId)
                       ->where('is_active', 1);
        
        if ($templateType) {
            $builder->where('template_type', $templateType);
        }
        
        return $builder->orderBy('template_name', 'ASC')->findAll();
    }

    /**
     * Belirli bir şablonu getir
     */
    public function getTemplate($branchId, $templateKey, $templateType = 'sms')
    {
        return $this->where('branch_id', $branchId)
                   ->where('template_key', $templateKey)
                   ->where('template_type', $templateType)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * Şablon içeriğini değişkenlerle doldur
     */
    public function processTemplate($templateContent, $variables = [])
    {
        $content = $templateContent;
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Varsayılan şablonları oluştur
     */
    public function createDefaultTemplates($branchId)
    {
        $defaultTemplates = [
            [
                'template_key' => 'appointment_reminder_24h',
                'template_name' => 'Randevu Hatırlatma (24 Saat Öncesi)',
                'template_content' => 'Sayın {musteri_adi}, yarın saat {randevu_saati} randevunuz bulunmaktadır. {salon_adi}',
                'template_type' => 'sms'
            ],
            [
                'template_key' => 'appointment_reminder_2h',
                'template_name' => 'Randevu Hatırlatma (2 Saat Öncesi)',
                'template_content' => 'Sayın {musteri_adi}, 2 saat sonra saat {randevu_saati} randevunuz bulunmaktadır. {salon_adi}',
                'template_type' => 'sms'
            ],
            [
                'template_key' => 'package_warning',
                'template_name' => 'Paket Uyarısı',
                'template_content' => 'Sayın {musteri_adi}, {paket_adi} paketinizde son kullanım hakkınız kalmıştır. {salon_adi}',
                'template_type' => 'sms'
            ],
            [
                'template_key' => 'no_show_notification',
                'template_name' => 'Gelmedi Bildirimi',
                'template_content' => 'Sayın {musteri_adi}, sizi aramızda göremedik. Yeni randevu için bize ulaşabilirsiniz. {salon_adi}',
                'template_type' => 'sms'
            ],
            [
                'template_key' => 'birthday_greeting',
                'template_name' => 'Doğum Günü Kutlaması',
                'template_content' => 'Sayın {musteri_adi}, doğum gününüz kutlu olsun! Size özel indirimlerimiz için bizi arayın. {salon_adi}',
                'template_type' => 'sms'
            ],
            // WhatsApp şablonları
            [
                'template_key' => 'appointment_reminder_24h',
                'template_name' => 'Randevu Hatırlatma (24 Saat Öncesi)',
                'template_content' => '🌸 Merhaba {musteri_adi}!\n\nYarın saat {randevu_saati} randevunuz bulunmaktadır.\n\n📍 {salon_adi}\n📞 İptal/değişiklik için bizi arayın.',
                'template_type' => 'whatsapp'
            ],
            [
                'template_key' => 'appointment_reminder_2h',
                'template_name' => 'Randevu Hatırlatma (2 Saat Öncesi)',
                'template_content' => '⏰ Merhaba {musteri_adi}!\n\n2 saat sonra saat {randevu_saati} randevunuz bulunmaktadır.\n\n📍 {salon_adi}\nSizi bekliyoruz! 💄',
                'template_type' => 'whatsapp'
            ]
        ];

        foreach ($defaultTemplates as $template) {
            $existing = $this->where('branch_id', $branchId)
                           ->where('template_key', $template['template_key'])
                           ->where('template_type', $template['template_type'])
                           ->first();
            
            if (!$existing) {
                $this->insert(array_merge($template, ['branch_id' => $branchId]));
            }
        }
    }

    /**
     * Kullanılabilir değişkenleri getir
     */
    public function getAvailableVariables()
    {
        return [
            'musteri_adi' => 'Müşteri Adı',
            'musteri_telefon' => 'Müşteri Telefon',
            'randevu_tarihi' => 'Randevu Tarihi',
            'randevu_saati' => 'Randevu Saati',
            'hizmet_adi' => 'Hizmet Adı',
            'personel_adi' => 'Personel Adı',
            'salon_adi' => 'Salon Adı',
            'salon_telefon' => 'Salon Telefon',
            'paket_adi' => 'Paket Adı',
            'kalan_seans' => 'Kalan Seans',
            'kalan_dakika' => 'Kalan Dakika'
        ];
    }
}