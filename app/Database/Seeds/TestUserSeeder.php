<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@beautypro.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone' => '0555 555 55 55',
                'branch_id' => 1,
                'role_id' => 1,
                'commission_rate' => 0,
                'working_hours' => json_encode([
                    'monday' => ['start' => '09:00', 'end' => '18:00'],
                    'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                    'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                    'thursday' => ['start' => '09:00', 'end' => '18:00'],
                    'friday' => ['start' => '09:00', 'end' => '18:00'],
                    'saturday' => ['start' => '09:00', 'end' => '17:00'],
                    'sunday' => ['start' => null, 'end' => null]
                ])
            ],
            [
                'username' => 'manager',
                'email' => 'manager@beautypro.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'first_name' => 'Şube',
                'last_name' => 'Müdürü',
                'phone' => '0555 555 55 56',
                'branch_id' => 1,
                'role_id' => 2,
                'commission_rate' => 5.0,
                'working_hours' => json_encode([
                    'monday' => ['start' => '09:00', 'end' => '18:00'],
                    'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                    'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                    'thursday' => ['start' => '09:00', 'end' => '18:00'],
                    'friday' => ['start' => '09:00', 'end' => '18:00'],
                    'saturday' => ['start' => '09:00', 'end' => '17:00'],
                    'sunday' => ['start' => null, 'end' => null]
                ])
            ],
            [
                'username' => 'reception',
                'email' => 'reception@beautypro.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'first_name' => 'Ayşe',
                'last_name' => 'Receptionist',
                'phone' => '0555 555 55 57',
                'branch_id' => 1,
                'role_id' => 3,
                'commission_rate' => 0,
                'working_hours' => json_encode([
                    'monday' => ['start' => '09:00', 'end' => '18:00'],
                    'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                    'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                    'thursday' => ['start' => '09:00', 'end' => '18:00'],
                    'friday' => ['start' => '09:00', 'end' => '18:00'],
                    'saturday' => ['start' => '09:00', 'end' => '17:00'],
                    'sunday' => ['start' => null, 'end' => null]
                ])
            ],
            [
                'username' => 'staff',
                'email' => 'staff@beautypro.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'first_name' => 'Fatma',
                'last_name' => 'Uzman',
                'phone' => '0555 555 55 58',
                'branch_id' => 1,
                'role_id' => 4,
                'commission_rate' => 15.0,
                'working_hours' => json_encode([
                    'monday' => ['start' => '10:00', 'end' => '19:00'],
                    'tuesday' => ['start' => '10:00', 'end' => '19:00'],
                    'wednesday' => ['start' => '10:00', 'end' => '19:00'],
                    'thursday' => ['start' => '10:00', 'end' => '19:00'],
                    'friday' => ['start' => '10:00', 'end' => '19:00'],
                    'saturday' => ['start' => '09:00', 'end' => '18:00'],
                    'sunday' => ['start' => null, 'end' => null]
                ])
            ]
        ];

        foreach ($users as $userData) {
            // Kullanıcının zaten var olup olmadığını kontrol et
            $existingUser = $this->db->table('users')
                ->where('email', $userData['email'])
                ->get()
                ->getRow();

            if (!$existingUser) {
                // Kullanıcı yoksa ekle
                $userData['is_active'] = 1;
                $userData['created_at'] = date('Y-m-d H:i:s');
                $userData['updated_at'] = date('Y-m-d H:i:s');
                
                $this->db->table('users')->insert($userData);
                echo "✅ {$userData['email']} kullanıcısı eklendi.\n";
            } else {
                echo "⚠️  {$userData['email']} kullanıcısı zaten mevcut.\n";
            }
        }

        echo "\nTest kullanıcıları hazır:\n";
        echo "Admin: admin@beautypro.com / password123\n";
        echo "Yönetici: manager@beautypro.com / password123\n";
        echo "Danışma: reception@beautypro.com / password123\n";
        echo "Personel: staff@beautypro.com / password123\n";
    }
}