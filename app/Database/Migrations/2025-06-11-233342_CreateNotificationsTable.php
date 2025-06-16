<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
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
            ],
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'appointment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['appointment_reminder', 'package_warning', 'no_show_notification', 'birthday_greeting', 'custom'],
            ],
            'channel' => [
                'type'       => 'ENUM',
                'constraint' => ['sms', 'whatsapp', 'email'],
            ],
            'recipient' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'Alıcı telefon/email',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'template_variables' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Şablonda kullanılan değişkenler',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed', 'cancelled'],
                'default'    => 'pending',
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Gönderilmesi planlanan zaman',
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'provider_response' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'SMS/WhatsApp sağlayıcısının yanıtı',
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
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('appointment_id', 'appointments', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->addKey('status');
        $this->forge->addKey('scheduled_at');
        $this->forge->addKey(['type', 'status']);
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}
