<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePackagesTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['session', 'time'],
                'comment'    => 'session: adet bazlı, time: dakika bazlı',
            ],
            'total_sessions' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true,
                'comment'    => 'Toplam seans sayısı (adet bazlı paketler için)',
            ],
            'total_minutes' => [
                'type'       => 'INT',
                'constraint' => 8,
                'null'       => true,
                'comment'    => 'Toplam dakika (dakika bazlı paketler için)',
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'validity_months' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 6,
                'comment'    => 'Geçerlilik süresi (ay)',
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('packages');
    }

    public function down()
    {
        $this->forge->dropTable('packages');
    }
}
