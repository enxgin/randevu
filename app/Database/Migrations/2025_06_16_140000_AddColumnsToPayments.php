<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToPayments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('payments', [
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'processed_by'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('payments', ['description', 'created_by']);
    }
}
