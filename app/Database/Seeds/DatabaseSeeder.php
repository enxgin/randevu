<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // İlk önce şube ekle
        $branchData = [
            'name' => 'Ana Şube',
            'address' => 'Test Adres, Test Mahalle, Test İlçe',
            'phone' => '0312 555 55 55',
            'email' => 'info@beautypro.com',
            'working_hours' => json_encode([
                'monday' => ['start' => '09:00', 'end' => '18:00'],
                'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                'thursday' => ['start' => '09:00', 'end' => '18:00'],
                'friday' => ['start' => '09:00', 'end' => '18:00'],
                'saturday' => ['start' => '09:00', 'end' => '17:00'],
                'sunday' => ['start' => null, 'end' => null]
            ]),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('branches')->insert($branchData);

        // Roller ekle
        $roles = [
            [
                'name' => 'Admin',
                'display_name' => 'Sistem Yöneticisi',
                'description' => 'Tüm sistem yetkilerine sahip kullanıcı',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Manager',
                'display_name' => 'Şube Müdürü',
                'description' => 'Şube yönetim yetkilerine sahip kullanıcı',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Receptionist',
                'display_name' => 'Danışma/Resepsiyon',
                'description' => 'Randevu ve müşteri yönetimi yetkilerine sahip kullanıcı',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Staff',
                'display_name' => 'Personel/Uzman',
                'description' => 'Kendi randevularını görme yetkisine sahip kullanıcı',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('roles')->insertBatch($roles);

        // İzinler ekle
        $permissions = [
            [
                'name' => 'admin.all',
                'display_name' => 'Tüm Admin Yetkileri',
                'description' => 'Sistem yönetimi için tüm yetkiler',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'branch.manage',
                'display_name' => 'Şube Yönetimi',
                'description' => 'Şube ekleme, düzenleme, silme yetkileri',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'appointment.manage',
                'display_name' => 'Randevu Yönetimi',
                'description' => 'Randevu oluşturma, düzenleme, silme yetkileri',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'customer.manage',
                'display_name' => 'Müşteri Yönetimi',
                'description' => 'Müşteri ekleme, düzenleme, görüntüleme yetkileri',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('permissions')->insertBatch($permissions);

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

        echo "Temel veriler başarıyla eklendi:\n";
        echo "- Ana Şube oluşturuldu\n";
        echo "- 4 rol eklendi (Admin, Manager, Receptionist, Staff)\n";
        echo "- 4 izin eklendi\n";
        echo "- Admin kullanıcısı eklendi: admin@beautypro.com / 123456\n";
    }
}