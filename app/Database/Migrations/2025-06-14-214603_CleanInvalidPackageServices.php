<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanInvalidPackageServices extends Migration
{
    public function up()
    {
        // Geçersiz package_services kayıtlarını sil (mevcut olmayan service_id'ler)
        $this->db->query("DELETE FROM package_services WHERE service_id NOT IN (SELECT id FROM services)");
        
        // Mevcut paketler için eksik hizmet ilişkilerini ekle
        $packages = $this->db->query("SELECT id FROM packages WHERE is_active = 1")->getResultArray();
        $services = $this->db->query("SELECT id FROM services WHERE is_active = 1 LIMIT 3")->getResultArray(); // İlk 3 hizmeti al
        
        foreach ($packages as $package) {
            // Bu paket için zaten hizmet var mı kontrol et
            $existingServices = $this->db->query("SELECT COUNT(*) as count FROM package_services WHERE package_id = ?", [$package['id']])->getRow();
            
            if ($existingServices->count == 0 && !empty($services)) {
                // İlk hizmeti pakete ekle
                $this->db->query("INSERT INTO package_services (package_id, service_id, created_at) VALUES (?, ?, NOW())", 
                              [$package['id'], $services[0]['id']]);
            }
        }
    }

    public function down()
    {
        // Geri alma işlemi
    }
}