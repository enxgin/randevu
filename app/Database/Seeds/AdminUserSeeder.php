<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Önce mevcut admin kullanıcısı var mı kontrol et
        $existingUser = $this->db->table('users')->where('email', 'admin@beautypro.com')->get()->getRow();
        
        if ($existingUser) {
            echo "Admin kullanıcısı zaten mevcut: admin@beautypro.com\n";
            return;
        }

        // Admin kullanıcısı ekle
        $userData = [
            'branch_id' => 1,
            'role_id' => 1,
            'username' => 'admin',
            'email' => 'admin@beautypro.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '0555 555 55 55',
            'working_hours' => json_encode([
                'monday' => ['start' => '09:00', 'end' => '18:00'],
                'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                'thursday' => ['start' => '09:00', 'end' => '18:00'],
                'friday' => ['start' => '09:00', 'end' => '18:00'],
                'saturday' => ['start' => '09:00', 'end' => '17:00'],
                'sunday' => ['start' => null, 'end' => null]
            ]),
            'commission_rate' => 0,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('users')->insert($userData);
        echo "Admin kullanıcısı başarıyla eklendi: admin@beautypro.com / 123456\n";
    }
}