<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionsTable extends Migration
{
    public function up()
    {
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'Primi alan personel'
            ],
            'appointment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'İlgili randevu'
            ],
            'payment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'İlgili ödeme kaydı'
            ],
            'service_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'Verilen hizmet'
            ],
            'commission_rule_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Kullanılan prim kuralı'
            ],
            'service_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'comment' => 'Hizmet tutarı'
            ],
            'commission_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'comment' => 'Hesaplanan prim tutarı'
            ],
            'commission_type' => [
                'type' => 'ENUM',
                'constraint' => ['percentage', 'fixed_amount'],
                'comment' => 'Prim hesaplama tipi'
            ],
            'commission_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'Kullanılan prim oranı/tutarı'
            ],
            'is_package_service' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '0: Normal hizmet, 1: Paketli hizmet'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'paid', 'cancelled', 'refunded'],
                'default' => 'pending',
                'comment' => 'Prim durumu'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Prim notları'
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('appointment_id');
        $this->forge->addKey('payment_id');
        $this->forge->addKey('service_id');
        $this->forge->addKey('commission_rule_id');
        $this->forge->addKey(['user_id', 'status']);
        $this->forge->addKey(['branch_id', 'status']);
        $this->forge->addKey(['created_at']);

        // Foreign key constraints
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('appointment_id', 'appointments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('payment_id', 'payments', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('commission_rule_id', 'commission_rules', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('commissions');
    }

    public function down()
    {
        $this->forge->dropTable('commissions');
    }
}
