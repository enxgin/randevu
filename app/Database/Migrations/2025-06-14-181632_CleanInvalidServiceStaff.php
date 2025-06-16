<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanInvalidServiceStaff extends Migration
{
    public function up()
    {
        // Geçersiz service_staff kayıtlarını sil (service_id 13, 14, 15 gibi mevcut olmayan hizmetler)
        $this->db->query("DELETE FROM service_staff WHERE id IN (1, 2, 3, 4)");
        
        // Kalan hizmetler için eksik personel atamalarını tamamla
        $services = $this->db->query("SELECT id, name FROM services WHERE is_active = 1 AND id NOT IN (SELECT DISTINCT service_id FROM service_staff)")->getResultArray();
        
        if (!empty($services)) {
            // İlk aktif personeli bul
            $firstUser = $this->db->query("SELECT id FROM users WHERE is_active = 1 AND role_id IN (SELECT id FROM roles WHERE name IN ('staff', 'manager', 'receptionist')) LIMIT 1")->getRow();
            
            if ($firstUser) {
                foreach ($services as $service) {
                    $this->db->query("INSERT INTO service_staff (service_id, user_id, created_at) VALUES (?, ?, NOW())", 
                                  [$service['id'], $firstUser->id]);
                }
            }
        }
    }

    public function down()
    {
        // Geri alma işlemi
    }
}