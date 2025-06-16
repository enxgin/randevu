<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCashMovementsTable extends Migration
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
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['opening', 'closing', 'income', 'expense', 'adjustment'],
                'comment'    => 'opening: gün açılışı, closing: gün kapanışı, income: gelir, expense: gider, adjustment: düzeltme',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Hareket kategorisi (fatura, avans, düzeltme vb.)',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'reference_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Referans tür (payment, appointment vb.)',
            ],
            'reference_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Referans ID',
            ],
            'balance_before' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'comment'    => 'İşlem öncesi kasa bakiyesi',
            ],
            'balance_after' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'comment'    => 'İşlem sonrası kasa bakiyesi',
            ],
            'processed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addForeignKey('processed_by', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->addKey('type');
        $this->forge->addKey(['branch_id', 'created_at']);
        $this->forge->addKey(['reference_type', 'reference_id']);
        $this->forge->createTable('cash_movements');
    }

    public function down()
    {
        $this->forge->dropTable('cash_movements');
    }
}
