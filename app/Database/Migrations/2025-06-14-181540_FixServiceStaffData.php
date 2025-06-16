<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixServiceStaffData extends Migration
{
    public function up()
    {
        // 1. Geçersiz service_staff kayıtlarını sil
        $this->db->query("DELETE FROM service_staff WHERE service_id NOT IN (SELECT id FROM services)");
        
        // 2. Mevcut hizmetler için personel ataması yap
        $services = $this->db->query("SELECT id, name FROM services WHERE is_active = 1")->getResultArray();
        $users = $this->db->query("SELECT id, first_name, last_name FROM users WHERE is_active = 1 AND role_id IN (SELECT id FROM roles WHERE name IN ('staff', 'manager', 'receptionist'))")->getResultArray();
        
        // Her hizmet için en az bir personel ata
        foreach ($services as $service) {
            // Bu hizmet için zaten personel var mı kontrol et
            $existingStaff = $this->db->query("SELECT COUNT(*) as count FROM service_staff WHERE service_id = ?", [$service['id']])->getRow();
            
            if ($existingStaff->count == 0) {
                // İlk personeli ata
                if (!empty($users)) {
                    $this->db->query("INSERT INTO service_staff (service_id, user_id, created_at) VALUES (?, ?, NOW())", 
                                  [$service['id'], $users[0]['id']]);
                }
            }
        }
    }

    public function down()
    {
        // Geri alma işlemi gerekirse burada tanımlanabilir
    }
}