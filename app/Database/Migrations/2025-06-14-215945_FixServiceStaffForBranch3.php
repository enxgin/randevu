<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixServiceStaffForBranch3 extends Migration
{
    public function up()
    {
        // Service ID 16 için personel ataması yap (şube 3'teki personeller: user_id 8 ve 9)
        $this->db->query("INSERT INTO service_staff (service_id, user_id, created_at) VALUES (16, 8, NOW())");
        $this->db->query("INSERT INTO service_staff (service_id, user_id, created_at) VALUES (16, 9, NOW())");
    }

    public function down()
    {
        // Geri alma işlemi
        $this->db->query("DELETE FROM service_staff WHERE service_id = 16");
    }
}