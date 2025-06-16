<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBranchIdToServiceCategoriesTable extends Migration
{
    public function up()
    {
        // branch_id sütununu ekle
        $this->forge->addColumn('service_categories', [
            'branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        // Foreign key constraint ekle
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');

        // Mevcut kayıtları güncelle - varsayılan olarak branch_id = 1 (ilk şube)
        $this->db->query("UPDATE service_categories SET branch_id = 1 WHERE branch_id IS NULL");
    }

    public function down()
    {
        // Foreign key constraint'i kaldır
        $this->forge->dropForeignKey('service_categories', 'service_categories_branch_id_foreign');
        
        // branch_id sütununu kaldır
        $this->forge->dropColumn('service_categories', 'branch_id');
    }
}