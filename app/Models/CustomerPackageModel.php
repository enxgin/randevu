<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerPackageModel extends Model
{
    protected $table            = 'customer_packages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_id',
        'package_id',
        'purchase_date',
        'expiry_date',
        'remaining_sessions',
        'remaining_minutes',
        'used_sessions',
        'used_minutes',
        'status',
        'notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules = [
        'customer_id'   => 'required|integer|is_not_unique[customers.id]',
        'package_id'    => 'required|integer|is_not_unique[packages.id]',
        'purchase_date' => 'required|valid_date',
        'expiry_date'   => 'permit_empty|valid_date',
        'status'        => 'permit_empty|in_list[active,expired,completed,cancelled]'
    ];

    protected $validationMessages = [
        'customer_id' => [
            'required'      => 'Müşteri seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz müşteri seçimi.'
        ],
        'package_id' => [
            'required'      => 'Paket seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz paket seçimi.'
        ],
        'purchase_date' => [
            'required'   => 'Satın alma tarihi zorunludur.',
            'valid_date' => 'Geçerli bir tarih giriniz.'
        ],
        'expiry_date' => [
            'valid_date' => 'Geçerli bir tarih giriniz.'
        ],
        'status' => [
            'required' => 'Durum seçimi zorunludur.',
            'in_list'  => 'Geçersiz durum seçimi.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calculateExpiryDate'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Bitiş tarihini otomatik hesapla
     */
    protected function calculateExpiryDate(array $data)
    {
        if (isset($data['data']['package_id']) && isset($data['data']['purchase_date'])) {
            $packageModel = new PackageModel();
            $package = $packageModel->find($data['data']['package_id']);
            
            if ($package) {
                $purchaseDate = new \DateTime($data['data']['purchase_date']);
                $expiryDate = $purchaseDate->add(new \DateInterval('P' . $package['validity_months'] . 'M'));
                $data['data']['expiry_date'] = $expiryDate->format('Y-m-d H:i:s');
                
                // Kalan seans/dakika bilgilerini set et
                if ($package['type'] === 'session') {
                    $data['data']['remaining_sessions'] = $package['total_sessions'];
                    $data['data']['remaining_minutes'] = null;
                } else {
                    $data['data']['remaining_minutes'] = $package['total_minutes'];
                    $data['data']['remaining_sessions'] = null;
                }
            }
        }
        
        return $data;
    }

    /**
     * Müşterinin aktif paketlerini getir
     */
    public function getActivePackages($customerId)
    {
        return $this->select('customer_packages.*, packages.name, packages.type, packages.total_sessions, packages.total_minutes')
            ->join('packages', 'packages.id = customer_packages.package_id')
            ->where('customer_packages.customer_id', $customerId)
            ->where('customer_packages.status', 'active')
            ->where('customer_packages.expiry_date >', date('Y-m-d H:i:s'))
            ->orderBy('customer_packages.expiry_date', 'ASC')
            ->findAll();
    }

    /**
     * Müşterinin tüm paket geçmişini getir
     */
    public function getCustomerPackageHistory($customerId)
    {
        return $this->select('customer_packages.*, packages.name, packages.type, packages.total_sessions, packages.total_minutes')
            ->join('packages', 'packages.id = customer_packages.package_id')
            ->where('customer_packages.customer_id', $customerId)
            ->orderBy('customer_packages.purchase_date', 'DESC')
            ->findAll();
    }

    /**
     * Paket kullanımını güncelle (seans veya dakika düş)
     */
    public function usePackage($customerPackageId, $sessions = 0, $minutes = 0)
    {
        $customerPackage = $this->find($customerPackageId);
        if (!$customerPackage) {
            return false;
        }

        $updateData = [];

        // Seans düşümü
        if ($sessions > 0 && $customerPackage['remaining_sessions'] !== null) {
            $newRemainingSessions = max(0, $customerPackage['remaining_sessions'] - $sessions);
            $newUsedSessions = $customerPackage['used_sessions'] + $sessions;
            
            $updateData['remaining_sessions'] = $newRemainingSessions;
            $updateData['used_sessions'] = $newUsedSessions;
            
            // Seans bittiyse durumu güncelle
            if ($newRemainingSessions <= 0) {
                $updateData['status'] = 'completed';
            }
        }

        // Dakika düşümü
        if ($minutes > 0 && $customerPackage['remaining_minutes'] !== null) {
            $newRemainingMinutes = max(0, $customerPackage['remaining_minutes'] - $minutes);
            $newUsedMinutes = $customerPackage['used_minutes'] + $minutes;
            
            $updateData['remaining_minutes'] = $newRemainingMinutes;
            $updateData['used_minutes'] = $newUsedMinutes;
            
            // Dakika bittiyse durumu güncelle
            if ($newRemainingMinutes <= 0) {
                $updateData['status'] = 'completed';
            }
        }

        if (!empty($updateData)) {
            return $this->update($customerPackageId, $updateData);
        }

        return false;
    }

    /**
     * Müşterinin belirli hizmet için kullanabileceği paketleri getir
     */
    public function getAvailablePackagesForService($customerId, $serviceId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('customer_packages cp')
            ->select('cp.*, p.name, p.type')
            ->join('packages p', 'p.id = cp.package_id')
            ->join('package_services ps', 'ps.package_id = p.id')
            ->where('cp.customer_id', $customerId)
            ->where('ps.service_id', $serviceId)
            ->where('cp.status', 'active')
            ->where('cp.expiry_date >', date('Y-m-d H:i:s'))
            ->groupStart()
                ->where('cp.remaining_sessions >', 0)
                ->orWhere('cp.remaining_minutes >', 0)
            ->groupEnd()
            ->orderBy('cp.expiry_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Süresi dolmuş paketleri expired yap
     */
    public function expireOldPackages()
    {
        return $this->where('expiry_date <', date('Y-m-d H:i:s'))
            ->where('status', 'active')
            ->set('status', 'expired')
            ->update();
    }

    /**
     * Paket bitiminde uyarı gereken müşterileri getir
     */
    public function getPackagesNearExpiry($days = 7)
    {
        $expiryDate = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        
        return $this->select('customer_packages.*, customers.first_name, customers.last_name, customers.phone, packages.name as package_name')
            ->join('customers', 'customers.id = customer_packages.customer_id')
            ->join('packages', 'packages.id = customer_packages.package_id')
            ->where('customer_packages.status', 'active')
            ->where('customer_packages.expiry_date <=', $expiryDate)
            ->where('customer_packages.expiry_date >', date('Y-m-d H:i:s'))
            ->orderBy('customer_packages.expiry_date', 'ASC')
            ->findAll();
    }

    /**
     * Son seans/dakika kalan paketleri getir
     */
    public function getPackagesNearCompletion()
    {
        return $this->select('customer_packages.*, customers.first_name, customers.last_name, customers.phone, packages.name as package_name')
            ->join('customers', 'customers.id = customer_packages.customer_id')
            ->join('packages', 'packages.id = customer_packages.package_id')
            ->where('customer_packages.status', 'active')
            ->groupStart()
                ->where('customer_packages.remaining_sessions <=', 1)
                ->orWhere('customer_packages.remaining_minutes <=', 60)
            ->groupEnd()
            ->orderBy('customer_packages.remaining_sessions, customer_packages.remaining_minutes', 'ASC')
            ->findAll();
    }

    /**
     * Paket kullanım raporları
     */
    public function getPackageUsageReport($branchId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
            customer_packages.*,
            customers.first_name,
            customers.last_name,
            customers.phone,
            packages.name as package_name,
            packages.type,
            packages.price,
            branches.name as branch_name
        ')
        ->join('customers', 'customers.id = customer_packages.customer_id')
        ->join('packages', 'packages.id = customer_packages.package_id')
        ->join('branches', 'branches.id = customers.branch_id', 'left');

        if ($branchId) {
            $builder->where('customers.branch_id', $branchId);
        }

        if ($startDate) {
            $builder->where('customer_packages.purchase_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('customer_packages.purchase_date <=', $endDate);
        }

        return $builder->orderBy('customer_packages.purchase_date', 'DESC')->findAll();
    }

    /**
     * Durum etiketini getir
     */
    public function getStatusLabel($status)
    {
        $labels = [
            'active'    => 'Aktif',
            'expired'   => 'Süresi Dolmuş',
            'completed' => 'Tamamlandı',
            'cancelled' => 'İptal Edildi'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Durum rengini getir
     */
    public function getStatusColor($status)
    {
        $colors = [
            'active'    => 'green',
            'expired'   => 'red',
            'completed' => 'blue',
            'cancelled' => 'gray'
        ];

        return $colors[$status] ?? 'gray';
    }

    /**
     * Paket kullanım yüzdesini hesapla
     */
    public function getUsagePercentage($customerPackage)
    {
        if ($customerPackage['remaining_sessions'] !== null) {
            $total = $customerPackage['used_sessions'] + $customerPackage['remaining_sessions'];
            if ($total > 0) {
                return round(($customerPackage['used_sessions'] / $total) * 100, 1);
            }
        } elseif ($customerPackage['remaining_minutes'] !== null) {
            $total = $customerPackage['used_minutes'] + $customerPackage['remaining_minutes'];
            if ($total > 0) {
                return round(($customerPackage['used_minutes'] / $total) * 100, 1);
            }
        }
        
        return 0;
    }

    /**
     * Paket satış istatistikleri
     */
    public function getPackageSalesStats($branchId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('COUNT(*) as total_sales, SUM(packages.price) as total_revenue')
            ->join('packages', 'packages.id = customer_packages.package_id');

        if ($branchId) {
            $builder->join('customers', 'customers.id = customer_packages.customer_id')
                ->where('customers.branch_id', $branchId);
        }

        if ($startDate) {
            $builder->where('customer_packages.purchase_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('customer_packages.purchase_date <=', $endDate);
        }

        return $builder->get()->getRowArray();
    }
}