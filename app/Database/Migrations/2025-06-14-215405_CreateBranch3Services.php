<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBranch3Services extends Migration
{
    public function up()
    {
        // Şube 3 için hizmet oluştur
        $this->db->query("INSERT INTO services (branch_id, category_id, name, description, duration, price, is_active, created_at, updated_at) VALUES (3, 2, 'Conco Hizmet 1', 'Şube 3 için özel hizmet', 60, 1000.00, 1, NOW(), NOW())");
        
        // Oluşturulan hizmetin ID'sini al
        $serviceId = $this->db->insertID();
        
        // Bu hizmeti şube 3'teki personele ata (user_id 8 ve 9)
        $this->db->query("INSERT INTO service_staff (service_id, user_id, created_at) VALUES (?, 8, NOW())", [$serviceId]);
        $this->db->query("INSERT INTO service_staff (service_id, user_id, created_at) VALUES (?, 9, NOW())", [$serviceId]);
        
        // Paket 2'yi bu hizmetle ilişkilendir (önce eski ilişkiyi sil)
        $this->db->query("DELETE FROM package_services WHERE package_id = 2");
        $this->db->query("INSERT INTO package_services (package_id, service_id, created_at) VALUES (2, ?, NOW())", [$serviceId]);
    }

    public function down()
    {
        // Geri alma işlemi
        $this->db->query("DELETE FROM services WHERE branch_id = 3");
    }
}