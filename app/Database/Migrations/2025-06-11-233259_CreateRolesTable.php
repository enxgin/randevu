<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('roles');

        // Varsayılan rolleri ekle
        $data = [
            [
                'name' => 'admin',
                'display_name' => 'Admin (Süper Yönetici)',
                'description' => 'Tüm sistem yetkilerine sahip süper yönetici',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manager',
                'display_name' => 'Yönetici (Şube Müdürü)',
                'description' => 'Şube düzeyinde yönetim yetkilerine sahip',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'receptionist',
                'display_name' => 'Danışma (Resepsiyon)',
                'description' => 'Randevu ve müşteri yönetimi yetkilerine sahip',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'staff',
                'display_name' => 'Personel (Uzman/Terapist)',
                'description' => 'Kendi randevularını görme ve prim raporları yetkilerine sahip',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('roles')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
