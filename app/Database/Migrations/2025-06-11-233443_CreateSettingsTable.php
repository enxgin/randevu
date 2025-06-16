<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        // Tablo zaten varsa önce silelim
        if ($this->db->tableExists('settings')) {
            $this->forge->dropTable('settings');
        }
        
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Şubeye özel ayarlar için, null ise global ayar',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Ayar kategorisi (sms, whatsapp, general vb.)',
            ],
            'key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['string', 'integer', 'boolean', 'json', 'encrypted'],
                'default'    => 'string',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_public' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'comment'    => 'Frontend\'de görülebilir mi',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['branch_id', 'category', 'key']);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('settings');

        // Varsayılan sistem ayarlarını ekle
        $data = [
            // SMS Ayarları
            [
                'branch_id' => null,
                'category' => 'sms',
                'key' => 'provider',
                'value' => 'netgsm',
                'type' => 'string',
                'description' => 'SMS sağlayıcısı',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id' => null,
                'category' => 'sms',
                'key' => 'api_key',
                'value' => '',
                'type' => 'encrypted',
                'description' => 'SMS API anahtarı',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id' => null,
                'category' => 'sms',
                'key' => 'sender_name',
                'value' => '',
                'type' => 'string',
                'description' => 'SMS gönderici adı',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // WhatsApp Ayarları
            [
                'branch_id' => null,
                'category' => 'whatsapp',
                'key' => 'api_url',
                'value' => '',
                'type' => 'string',
                'description' => 'WhatsApp API URL',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id' => null,
                'category' => 'whatsapp',
                'key' => 'api_token',
                'value' => '',
                'type' => 'encrypted',
                'description' => 'WhatsApp API Token',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Bildirim Ayarları
            [
                'branch_id' => null,
                'category' => 'notification',
                'key' => 'appointment_reminder_24h',
                'value' => '1',
                'type' => 'boolean',
                'description' => '24 saat öncesi randevu hatırlatması',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id' => null,
                'category' => 'notification',
                'key' => 'appointment_reminder_2h',
                'value' => '1',
                'type' => 'boolean',
                'description' => '2 saat öncesi randevu hatırlatması',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id' => null,
                'category' => 'notification',
                'key' => 'package_warning',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Paket bitme uyarısı',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id' => null,
                'category' => 'notification',
                'key' => 'birthday_greeting',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Doğum günü kutlaması',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
            // Genel Ayarlar
            [
                'branch_id' => null,
                'category' => 'general',
                'key' => 'company_name',
                'value' => 'Güzellik Salonu',
                'type' => 'string',
                'description' => 'Şirket adı',
                'is_public' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id' => null,
                'category' => 'general',
                'key' => 'default_appointment_duration',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Varsayılan randevu süresi (dakika)',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id' => null,
                'category' => 'general',
                'key' => 'timezone',
                'value' => 'Europe/Istanbul',
                'type' => 'string',
                'description' => 'Zaman dilimi',
                'is_public' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
