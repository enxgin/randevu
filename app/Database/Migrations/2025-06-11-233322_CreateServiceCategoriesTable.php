<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateServiceCategoriesTable extends Migration
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
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null'       => true,
                'comment'    => 'Kategori rengi (hex kod)',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
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
        $this->forge->createTable('service_categories');

        // Varsayılan kategorileri ekle
        $data = [
            [
                'name' => 'Cilt Bakımı',
                'description' => 'Yüz ve vücut cilt bakım hizmetleri',
                'color' => '#FF6B6B',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Lazer Epilasyon',
                'description' => 'Lazer ile epilasyon hizmetleri',
                'color' => '#4ECDC4',
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Masaj',
                'description' => 'Rahatlama ve tedavi amaçlı masaj hizmetleri',
                'color' => '#45B7D1',
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Makyaj',
                'description' => 'Günlük ve özel günler için makyaj hizmetleri',
                'color' => '#F39C12',
                'sort_order' => 4,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Saç Bakımı',
                'description' => 'Saç kesimi, boyama ve bakım hizmetleri',
                'color' => '#9B59B6',
                'sort_order' => 5,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('service_categories')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('service_categories');
    }
}
