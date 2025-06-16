<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionsTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'İzin kategorisi (branch, user, appointment vb.)',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('permissions');

        // Varsayılan izinleri ekle
        $data = [
            // Şube Yönetimi
            ['name' => 'branch.create', 'display_name' => 'Şube Oluştur', 'category' => 'branch', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'branch.read', 'display_name' => 'Şube Görüntüle', 'category' => 'branch', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'branch.update', 'display_name' => 'Şube Güncelle', 'category' => 'branch', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'branch.delete', 'display_name' => 'Şube Sil', 'category' => 'branch', 'created_at' => date('Y-m-d H:i:s')],
            
            // Kullanıcı Yönetimi
            ['name' => 'user.create', 'display_name' => 'Kullanıcı Oluştur', 'category' => 'user', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'user.read', 'display_name' => 'Kullanıcı Görüntüle', 'category' => 'user', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'user.update', 'display_name' => 'Kullanıcı Güncelle', 'category' => 'user', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'user.delete', 'display_name' => 'Kullanıcı Sil', 'category' => 'user', 'created_at' => date('Y-m-d H:i:s')],
            
            // Müşteri Yönetimi
            ['name' => 'customer.create', 'display_name' => 'Müşteri Oluştur', 'category' => 'customer', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'customer.read', 'display_name' => 'Müşteri Görüntüle', 'category' => 'customer', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'customer.update', 'display_name' => 'Müşteri Güncelle', 'category' => 'customer', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'customer.delete', 'display_name' => 'Müşteri Sil', 'category' => 'customer', 'created_at' => date('Y-m-d H:i:s')],
            
            // Randevu Yönetimi
            ['name' => 'appointment.create', 'display_name' => 'Randevu Oluştur', 'category' => 'appointment', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'appointment.read', 'display_name' => 'Randevu Görüntüle', 'category' => 'appointment', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'appointment.update', 'display_name' => 'Randevu Güncelle', 'category' => 'appointment', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'appointment.delete', 'display_name' => 'Randevu Sil', 'category' => 'appointment', 'created_at' => date('Y-m-d H:i:s')],
            
            // Hizmet Yönetimi
            ['name' => 'service.create', 'display_name' => 'Hizmet Oluştur', 'category' => 'service', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'service.read', 'display_name' => 'Hizmet Görüntüle', 'category' => 'service', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'service.update', 'display_name' => 'Hizmet Güncelle', 'category' => 'service', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'service.delete', 'display_name' => 'Hizmet Sil', 'category' => 'service', 'created_at' => date('Y-m-d H:i:s')],
            
            // Paket Yönetimi
            ['name' => 'package.create', 'display_name' => 'Paket Oluştur', 'category' => 'package', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'package.read', 'display_name' => 'Paket Görüntüle', 'category' => 'package', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'package.update', 'display_name' => 'Paket Güncelle', 'category' => 'package', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'package.delete', 'display_name' => 'Paket Sil', 'category' => 'package', 'created_at' => date('Y-m-d H:i:s')],
            
            // Ödeme Yönetimi
            ['name' => 'payment.create', 'display_name' => 'Ödeme Al', 'category' => 'payment', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'payment.read', 'display_name' => 'Ödeme Görüntüle', 'category' => 'payment', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'payment.update', 'display_name' => 'Ödeme Güncelle', 'category' => 'payment', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'payment.delete', 'display_name' => 'Ödeme Sil', 'category' => 'payment', 'created_at' => date('Y-m-d H:i:s')],
            
            // Kasa Yönetimi
            ['name' => 'cash.manage', 'display_name' => 'Kasa Yönet', 'category' => 'cash', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'cash.report', 'display_name' => 'Kasa Raporu', 'category' => 'cash', 'created_at' => date('Y-m-d H:i:s')],
            
            // Raporlar
            ['name' => 'report.financial', 'display_name' => 'Finansal Rapor', 'category' => 'report', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'report.commission', 'display_name' => 'Prim Raporu', 'category' => 'report', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'report.customer', 'display_name' => 'Müşteri Raporu', 'category' => 'report', 'created_at' => date('Y-m-d H:i:s')],
            
            // Sistem Ayarları
            ['name' => 'setting.manage', 'display_name' => 'Sistem Ayarları', 'category' => 'setting', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'notification.manage', 'display_name' => 'Bildirim Ayarları', 'category' => 'setting', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('permissions')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('permissions');
    }
}
