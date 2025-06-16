<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionRulesTable extends Migration
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
                'null' => true,
                'comment' => 'NULL ise genel kural, dolu ise personele özel kural'
            ],
            'service_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'NULL ise genel kural, dolu ise hizmete özel kural'
            ],
            'rule_type' => [
                'type' => 'ENUM',
                'constraint' => ['general', 'service_specific', 'user_specific'],
                'default' => 'general',
                'comment' => 'Kural tipi: genel, hizmete özel, personele özel'
            ],
            'commission_type' => [
                'type' => 'ENUM',
                'constraint' => ['percentage', 'fixed_amount'],
                'default' => 'percentage',
                'comment' => 'Prim tipi: yüzdesel veya sabit tutar'
            ],
            'commission_value' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'comment' => 'Prim değeri (% için 0-100 arası, sabit tutar için TL)'
            ],
            'is_package_rule' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '0: Normal hizmet, 1: Paketli hizmet'
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '0: Pasif, 1: Aktif'
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
        $this->forge->addKey('service_id');
        $this->forge->addKey(['branch_id', 'is_active']);
        $this->forge->addKey(['user_id', 'is_active']);
        $this->forge->addKey(['service_id', 'is_active']);

        // Foreign key constraints
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('commission_rules');
    }

    public function down()
    {
        $this->forge->dropTable('commission_rules');
    }
}
