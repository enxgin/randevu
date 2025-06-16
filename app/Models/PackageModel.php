<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageModel extends Model
{
    protected $table            = 'packages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'name',
        'description',
        'type',
        'total_sessions',
        'total_minutes',
        'price',
        'validity_months',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'branch_id'        => 'required|integer|is_not_unique[branches.id]',
        'name'            => 'required|max_length[255]',
        'type'            => 'required|in_list[session,time]',
        'total_sessions'  => 'permit_empty|integer|greater_than[0]',
        'total_minutes'   => 'permit_empty|integer|greater_than[0]',
        'price'           => 'required|numeric|greater_than[0]',
        'validity_months' => 'required|integer|greater_than[0]',
        'is_active'       => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required'      => 'Şube seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz şube seçimi.'
        ],
        'name' => [
            'required'   => 'Paket adı zorunludur.',
            'max_length' => 'Paket adı en fazla 255 karakter olabilir.'
        ],
        'type' => [
            'required' => 'Paket türü seçimi zorunludur.',
            'in_list'  => 'Geçersiz paket türü.'
        ],
        'total_sessions' => [
            'integer'      => 'Toplam seans sayısı geçerli bir sayı olmalıdır.',
            'greater_than' => 'Toplam seans sayısı 0\'dan büyük olmalıdır.'
        ],
        'total_minutes' => [
            'integer'      => 'Toplam dakika geçerli bir sayı olmalıdır.',
            'greater_than' => 'Toplam dakika 0\'dan büyük olmalıdır.'
        ],
        'price' => [
            'required'     => 'Fiyat zorunludur.',
            'numeric'      => 'Fiyat geçerli bir sayı olmalıdır.',
            'greater_than' => 'Fiyat 0\'dan büyük olmalıdır.'
        ],
        'validity_months' => [
            'required'     => 'Geçerlilik süresi zorunludur.',
            'integer'      => 'Geçerlilik süresi geçerli bir sayı olmalıdır.',
            'greater_than' => 'Geçerlilik süresi 0\'dan büyük olmalıdır.'
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
     * Şubeye göre paketleri getir
     */
    public function getByBranch($branchId, $activeOnly = true)
    {
        $builder = $this->where('branch_id', $branchId);
        
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }
        
        return $builder->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Paket detaylarını hizmetlerle birlikte getir
     */
    public function getWithServices($id)
    {
        $package = $this->find($id);
        if (!$package) {
            return null;
        }

        // Paket hizmetlerini getir
        $db = \Config\Database::connect();
        $services = $db->table('package_services ps')
            ->select('s.id, s.name, s.duration, s.price, sc.name as category_name')
            ->join('services s', 's.id = ps.service_id')
            ->join('service_categories sc', 'sc.id = s.category_id')
            ->where('ps.package_id', $id)
            ->orderBy('sc.name, s.name')
            ->get()
            ->getResultArray();

        $package['services'] = $services;
        return $package;
    }

    /**
     * Paket türü etiketini getir
     */
    public function getTypeLabel($type)
    {
        $labels = [
            'session' => 'Adet Bazlı',
            'time'    => 'Dakika Bazlı'
        ];

        return $labels[$type] ?? $type;
    }

    /**
     * Paket durumu etiketini getir
     */
    public function getStatusLabel($isActive)
    {
        return $isActive ? 'Aktif' : 'Pasif';
    }

    /**
     * Paket istatistiklerini getir
     */
    public function getPackageStats($branchId = null)
    {
        $builder = $this->selectCount('id', 'total');
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }
        
        $total = $builder->get()->getRow()->total;

        $builder = $this->selectCount('id', 'active');
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }
        
        $active = $builder->where('is_active', 1)->get()->getRow()->active;

        return [
            'total'  => $total,
            'active' => $active,
            'inactive' => $total - $active
        ];
    }

    /**
     * Arama ve filtreleme
     */
    public function search($params = [])
    {
        $builder = $this->select('packages.*, branches.name as branch_name')
            ->join('branches', 'branches.id = packages.branch_id');

        // Şube filtresi
        if (!empty($params['branch_id'])) {
            $builder->where('packages.branch_id', $params['branch_id']);
        }

        // Durum filtresi
        if (isset($params['is_active']) && $params['is_active'] !== '') {
            $builder->where('packages.is_active', $params['is_active']);
        }

        // Tür filtresi
        if (!empty($params['type'])) {
            $builder->where('packages.type', $params['type']);
        }

        // Arama
        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('packages.name', $params['search'])
                ->orLike('packages.description', $params['search'])
                ->groupEnd();
        }

        // Sıralama
        $orderBy = $params['order_by'] ?? 'packages.name';
        $orderDir = $params['order_dir'] ?? 'ASC';
        $builder->orderBy($orderBy, $orderDir);

        return $builder;
    }
}