<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table            = 'services';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'branch_id',
        'category_id',
        'name',
        'description',
        'duration',
        'price',
        'cost',
        'commission_rate',
        'commission_amount',
        'package_commission_rate',
        'package_commission_amount',
        'color',
        'sort_order',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = false; // Tablo yapısında updated_at yok
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|string|max_length[150]',
        'branch_id' => 'required|is_natural_no_zero|is_not_unique[branches.id]',
        'category_id' => 'required|is_natural_no_zero|is_not_unique[service_categories.id]',
        'duration' => 'required|integer|greater_than[0]',
        'price' => 'required|numeric|greater_than_equal_to[0]',
        'cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'commission_rate' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'is_active' => 'permit_empty|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = ['assignDefaultStaff']; // Hizmet oluşturulduktan sonra varsayılan personel ataması
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // İlişkiler
    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategoryModel::class, 'category_id');
    }

    public function staff()
    {
        // service_staff pivot tablosu üzerinden User (personel) modeli ile ilişki
        return $this->belongsToMany(UserModel::class, 'service_staff', 'service_id', 'user_id');
    }

    public function packages()
    {
        // package_services pivot tablosu üzerinden Package modeli ile ilişki
        return $this->belongsToMany(PackageModel::class, 'package_services', 'service_id', 'package_id');
    }

    /**
     * Yeni bir hizmet eklendiğinde, eğer "Tüm Personeller" gibi bir seçenek varsa
     * veya belirli bir mantığa göre varsayılan personelleri atar.
     * Bu örnek bir callback fonksiyonudur, projenizin mantığına göre özelleştirilmelidir.
     */
    protected function assignDefaultStaff(array $data)
    {
        if (isset($data['id']) && $data['id'] > 0) {
            $serviceId = $data['id'];
            // Örnek: Bu hizmeti verebilecek tüm aktif personelleri ata
            // $userModel = new UserModel();
            // $staff = $userModel->where('is_active', 1)->where('role_id', /* Personel Rol ID */ )->findAll();
            // $serviceStaffModel = new ServiceStaffModel();
            // foreach ($staff as $person) {
            //     $serviceStaffModel->insert(['service_id' => $serviceId, 'user_id' => $person->id]);
            // }
        }
        return $data;
    }

    /**
     * Aktif hizmetleri getir
     */
    public function getActiveServices($branchId = null)
    {
        $builder = $this->select('services.*, service_categories.name as category_name')
                        ->join('service_categories', 'service_categories.id = services.category_id')
                        ->where('services.is_active', 1)
                        ->where('services.deleted_at', null);

        if ($branchId) {
            $builder->where('services.branch_id', $branchId);
        }

        return $builder->orderBy('service_categories.name, services.name')->findAll();
    }

    /**
     * Şubeye göre hizmetleri getir
     */
    public function getServicesByBranch($branchId)
    {
        return $this->select('services.*, service_categories.name as category_name')
                    ->join('service_categories', 'service_categories.id = services.category_id')
                    ->where('services.branch_id', $branchId)
                    ->where('services.deleted_at', null)
                    ->orderBy('service_categories.name, services.name')
                    ->findAll();
    }

    /**
     * Hizmet detayını kategori bilgisiyle getir
     */
    public function getServiceWithCategory($serviceId)
    {
        return $this->select('services.*, service_categories.name as category_name')
                    ->join('service_categories', 'service_categories.id = services.category_id')
                    ->where('services.id', $serviceId)
                    ->first();
    }

    /**
     * Personelin verebileceği hizmetleri getir
     */
    public function getServicesByStaff($userId, $branchId = null)
    {
        $builder = $this->select('services.*, service_categories.name as category_name')
                        ->join('service_categories', 'service_categories.id = services.category_id')
                        ->join('service_staff', 'service_staff.service_id = services.id')
                        ->where('service_staff.user_id', $userId)
                        ->where('services.is_active', 1)
                        ->where('services.deleted_at', null);

        if ($branchId) {
            $builder->where('services.branch_id', $branchId);
        }

        return $builder->orderBy('service_categories.name, services.name')->findAll();
    }

    /**
     * Kategoriye göre hizmetleri getir
     */
    public function getServicesByCategory($categoryId, $branchId = null)
    {
        $builder = $this->select('services.*')
                        ->where('services.category_id', $categoryId)
                        ->where('services.is_active', 1)
                        ->where('services.deleted_at', null);

        if ($branchId) {
            $builder->where('services.branch_id', $branchId);
        }

        return $builder->orderBy('services.name')->findAll();
    }
}