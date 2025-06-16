<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ForceCleanPackageServices extends Migration
{
    public function up()
    {
        // Geçersiz kayıtları manuel olarak sil
        $this->db->query("DELETE FROM package_services WHERE id = 5"); // service_id 15 olan kayıt
        
        // Paket 2 için geçerli bir hizmet ekle
        $this->db->query("INSERT INTO package_services (package_id, service_id, created_at) VALUES (2, 2, NOW())");
    }

    public function down()
    {
        // Geri alma işlemi
    }
}