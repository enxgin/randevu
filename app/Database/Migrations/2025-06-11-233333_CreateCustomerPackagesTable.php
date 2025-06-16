<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerPackagesTable extends Migration
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
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'package_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'purchase_date' => [
                'type' => 'DATETIME',
            ],
            'expiry_date' => [
                'type' => 'DATETIME',
            ],
            'remaining_sessions' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true,
                'comment'    => 'Kalan seans sayısı (adet bazlı paketler için)',
            ],
            'remaining_minutes' => [
                'type'       => 'INT',
                'constraint' => 8,
                'null'       => true,
                'comment'    => 'Kalan dakika (dakika bazlı paketler için)',
            ],
            'used_sessions' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
                'comment'    => 'Kullanılan seans sayısı',
            ],
            'used_minutes' => [
                'type'       => 'INT',
                'constraint' => 8,
                'default'    => 0,
                'comment'    => 'Kullanılan dakika',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'expired', 'completed', 'cancelled'],
                'default'    => 'active',
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
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('package_id', 'packages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey('status');
        $this->forge->addKey('expiry_date');
        $this->forge->createTable('customer_packages');
    }

    public function down()
    {
        $this->forge->dropTable('customer_packages');
    }
}
