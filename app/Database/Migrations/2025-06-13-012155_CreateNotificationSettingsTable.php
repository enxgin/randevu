<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationSettingsTable extends Migration
{
    public function up()
    {
        // Bildirim ayarları tablosu
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addKey(['branch_id', 'setting_key']);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notification_settings');

        // Mesaj şablonları tablosu
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'template_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'template_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'template_content' => [
                'type' => 'TEXT',
            ],
            'template_type' => [
                'type' => 'ENUM',
                'constraint' => ['sms', 'whatsapp'],
                'default' => 'sms',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addKey(['branch_id', 'template_key']);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('message_templates');

        // Gönderilen mesajlar tablosu
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'appointment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'message_type' => [
                'type' => 'ENUM',
                'constraint' => ['sms', 'whatsapp'],
                'default' => 'sms',
            ],
            'trigger_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'recipient_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'message_content' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed', 'delivered'],
                'default' => 'pending',
            ],
            'provider_response' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey(['branch_id', 'customer_id']);
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('appointment_id', 'appointments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sent_messages');
    }

    public function down()
    {
        $this->forge->dropTable('sent_messages');
        $this->forge->dropTable('message_templates');
        $this->forge->dropTable('notification_settings');
    }
}
