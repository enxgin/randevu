<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationTriggersTable extends Migration
{
    public function up()
    {
        // Bildirim tetikleyici kuralları tablosu
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
            'trigger_type' => [
                'type' => 'ENUM',
                'constraint' => ['appointment_reminder', 'package_warning', 'no_show_notification', 'birthday_greeting'],
            ],
            'trigger_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'trigger_conditions' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Tetikleyici koşulları (JSON format)',
            ],
            'message_template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'message_type' => [
                'type' => 'ENUM',
                'constraint' => ['sms', 'whatsapp', 'both'],
                'default' => 'sms',
            ],
            'send_before_minutes' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Kaç dakika önce gönderilecek (randevu hatırlatma için)',
            ],
            'send_after_minutes' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Kaç dakika sonra gönderilecek (no-show için)',
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
        $this->forge->addKey('branch_id');
        $this->forge->addKey('trigger_type');
        $this->forge->addKey('is_active');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('message_template_id', 'message_templates', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('notification_triggers');

        // Bildirim kuyruğu tablosu (scheduled messages)
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
            'trigger_id' => [
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
            ],
            'recipient_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'message_content' => [
                'type' => 'TEXT',
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'comment' => 'Mesajın gönderileceği tarih/saat',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed', 'cancelled'],
                'default' => 'pending',
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('branch_id');
        $this->forge->addKey('trigger_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('appointment_id');
        $this->forge->addKey('scheduled_at');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('trigger_id', 'notification_triggers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('appointment_id', 'appointments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notification_queue');
    }

    public function down()
    {
        $this->forge->dropTable('notification_queue');
        $this->forge->dropTable('notification_triggers');
    }
}