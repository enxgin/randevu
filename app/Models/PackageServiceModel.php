<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageServiceModel extends Model
{
    protected $table            = 'package_services';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'package_id',
        'service_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules = [
        'package_id' => 'required|integer|is_not_unique[packages.id]',
        'service_id' => 'required|integer|is_not_unique[services.id]'
    ];

    protected $validationMessages = [
        'package_id' => [
            'required'      => 'Paket seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz paket seçimi.'
        ],
        'service_id' => [
            'required'      => 'Hizmet seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz hizmet seçimi.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Pakete ait hizmetleri getir
     */
    public function getPackageServices($packageId)
    {
        return $this->select('package_services.*, services.name, services.duration, services.price, service_categories.name as category_name')
            ->join('services', 'services.id = package_services.service_id')
            ->join('service_categories', 'service_categories.id = services.category_id')
            ->where('package_services.package_id', $packageId)
            ->orderBy('service_categories.name, services.name')
            ->findAll();
    }

    /**
     * Hizmete ait paketleri getir
     */
    public function getServicePackages($serviceId)
    {
        return $this->select('package_services.*, packages.name, packages.type, packages.price')
            ->join('packages', 'packages.id = package_services.package_id')
            ->where('package_services.service_id', $serviceId)
            ->where('packages.is_active', 1)
            ->orderBy('packages.name')
            ->findAll();
    }

    /**
     * Pakete hizmet ekle
     */
    public function addServiceToPackage($packageId, $serviceId)
    {
        // Zaten var mı kontrol et
        $existing = $this->where('package_id', $packageId)
            ->where('service_id', $serviceId)
            ->first();

        if ($existing) {
            return false; // Zaten mevcut
        }

        return $this->insert([
            'package_id' => $packageId,
            'service_id' => $serviceId
        ]);
    }

    /**
     * Paketten hizmet çıkar
     */
    public function removeServiceFromPackage($packageId, $serviceId)
    {
        return $this->where('package_id', $packageId)
            ->where('service_id', $serviceId)
            ->delete();
    }

    /**
     * Paketin tüm hizmetlerini güncelle
     */
    public function updatePackageServices($packageId, $serviceIds)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Mevcut hizmetleri sil
            $this->where('package_id', $packageId)->delete();

            // Yeni hizmetleri ekle
            if (!empty($serviceIds)) {
                $data = [];
                foreach ($serviceIds as $serviceId) {
                    $data[] = [
                        'package_id' => $packageId,
                        'service_id' => $serviceId,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
                $this->insertBatch($data);
            }

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }

    /**
     * Hizmetin hangi paketlerde kullanıldığını kontrol et
     */
    public function isServiceUsedInPackages($serviceId)
    {
        return $this->where('service_id', $serviceId)->countAllResults() > 0;
    }

    /**
     * Paketin hizmet sayısını getir
     */
    public function getPackageServiceCount($packageId)
    {
        return $this->where('package_id', $packageId)->countAllResults();
    }
}