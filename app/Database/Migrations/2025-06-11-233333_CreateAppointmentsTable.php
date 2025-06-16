<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppointmentsTable extends Migration
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
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'service_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'staff_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Hizmeti verecek personel',
            ],
            'customer_package_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Eğer paket kullanılıyorsa',
            ],
            'appointment_date' => [
                'type' => 'DATE',
            ],
            'start_time' => [
                'type' => 'TIME',
            ],
            'end_time' => [
                'type' => 'TIME',
            ],
            'duration' => [
                'type'       => 'INT',
                'constraint' => 5,
                'comment'    => 'Randevu süresi (dakika)',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'],
                'default'    => 'confirmed',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['one_time', 'recurring'],
                'default'    => 'one_time',
            ],
            'recurrence_pattern' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Tekrar eden randevu için pattern (weekly, monthly vb.)',
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Randevu ücreti',
            ],
            'paid_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'Ödenen tutar',
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'partial', 'paid', 'refunded'],
                'default'    => 'pending',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Randevu notları',
            ],
            'service_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Hizmet sonrası personel notları',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Randevuyu oluşturan kullanıcı',
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'cancellation_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('staff_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->addKey(['appointment_date', 'start_time']);
        $this->forge->addKey('status');
        $this->forge->addKey(['staff_id', 'appointment_date']);
        $this->forge->createTable('appointments');
    }

    public function down()
    {
        $this->forge->dropTable('appointments');
    }
}
