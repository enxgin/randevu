<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTagsColumnInCustomersTable extends Migration
{
    public function up()
    {
        $fields = [
            'tags' => [
                'type' => 'JSON', // Veri tipini JSON olarak değiştiriyoruz
                'null' => true,   // Null olabilir
                // 'default' => null, // JSON tipi için MySQL'de string default kabul etmeyebilir, null daha güvenli.
            ],
        ];
        $this->forge->modifyColumn('customers', $fields);
    }

    public function down()
    {
        // Geri alma işlemi: JSON'dan TEXT'e
        $fields = [
            'tags' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('customers', $fields);
    }
}