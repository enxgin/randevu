<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'display_name',
        'category',
        'description',
        'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_active' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[100]|is_unique[permissions.name,id,{id}]',
        'display_name' => 'required|max_length[255]',
        'category' => 'required|max_length[100]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'İzin kodu zorunludur.',
            'max_length' => 'İzin kodu en fazla 100 karakter olabilir.',
            'is_unique' => 'Bu izin kodu zaten kullanılmaktadır.'
        ],
        'display_name' => [
            'required' => 'İzin adı zorunludur.',
            'max_length' => 'İzin adı en fazla 255 karakter olabilir.'
        ],
        'category' => [
            'required' => 'İzin kategorisi zorunludur.',
            'max_length' => 'İzin kategorisi en fazla 100 karakter olabilir.'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = ['checkPermissionUsage'];
    protected $afterDelete = [];

    /**
     * Aktif izinleri getir
     */
    public function getActivePermissions()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Kategoriye göre izinleri getir
     */
    public function getPermissionsByCategory()
    {
        $permissions = $this->where('is_active', true)
                           ->orderBy('category', 'ASC')
                           ->orderBy('display_name', 'ASC')
                           ->findAll();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['category']][] = $permission;
        }
        
        return $grouped;
    }

    /**
     * İzin kategorilerini getir
     */
    public function getCategories()
    {
        return $this->select('category')
                   ->distinct()
                   ->where('is_active', true)
                   ->orderBy('category', 'ASC')
                   ->findColumn('category');
    }

    /**
     * Belirli rollerin sahip olduğu izinleri getir
     */
    public function getPermissionsWithRoles()
    {
        $db = \Config\Database::connect();
        return $db->table('permissions p')
                  ->select('p.*, GROUP_CONCAT(r.display_name) as role_names')
                  ->join('role_permissions rp', 'rp.permission_id = p.id', 'left')
                  ->join('roles r', 'r.id = rp.role_id AND r.is_active = 1', 'left')
                  ->where('p.is_active', true)
                  ->groupBy('p.id')
                  ->orderBy('p.category', 'ASC')
                  ->orderBy('p.display_name', 'ASC')
                  ->get()
                  ->getResultArray();
    }

    /**
     * İzin silmeden önce kullanılıp kullanılmadığını kontrol et
     */
    protected function checkPermissionUsage(array $data)
    {
        $db = \Config\Database::connect();
        $roleCount = $db->table('role_permissions')
                        ->where('permission_id', $data['id'][0])
                        ->countAllResults();
        
        if ($roleCount > 0) {
            throw new \RuntimeException('Bu izin kullanımda olduğu için silinemez. Önce rollerden izni kaldırın.');
        }
        
        return $data;
    }
}