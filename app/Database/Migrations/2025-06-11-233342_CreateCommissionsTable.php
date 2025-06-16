<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionsTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Primi alan personel',
            ],
            'appointment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'service_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Hizmet tutarı',
            ],
            'commission_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Prim tutarı',
            ],
            'commission_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'comment'    => 'Uygulanan prim oranı (%)',
            ],
            'is_package_service' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'comment'    => 'Paketli hizmet mi',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'paid', 'cancelled'],
                'default'    => 'pending',
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
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
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('appointment_id', 'appointments', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addKey('status');
        $this->forge->createTable('commissions');
    }

    public function down()
    {
        $this->forge->dropTable('commissions');
    }
}
