<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'display_name',
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
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[100]|is_unique[roles.name,id,{id}]',
        'display_name' => 'required|max_length[255]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Rol kodu zorunludur.',
            'max_length' => 'Rol kodu en fazla 100 karakter olabilir.',
            'is_unique' => 'Bu rol kodu zaten kullanılmaktadır.'
        ],
        'display_name' => [
            'required' => 'Rol adı zorunludur.',
            'max_length' => 'Rol adı en fazla 255 karakter olabilir.'
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
    protected $beforeDelete = ['checkRoleUsage'];
    protected $afterDelete = [];

    /**
     * Aktif rolleri getir
     */
    public function getActiveRoles()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Rol ile birlikte kullanıcı sayısını getir
     */
    public function getRolesWithUserCount()
    {
        return $this->select('roles.*, COUNT(users.id) as user_count')
                    ->join('users', 'users.role_id = roles.id', 'left')
                    ->groupBy('roles.id')
                    ->findAll();
    }

    /**
     * Rolün izinlerini getir
     */
    public function getRolePermissions($roleId)
    {
        $db = \Config\Database::connect();
        return $db->table('role_permissions rp')
                  ->select('p.*')
                  ->join('permissions p', 'p.id = rp.permission_id')
                  ->where('rp.role_id', $roleId)
                  ->where('p.is_active', true)
                  ->get()
                  ->getResultArray();
    }

    /**
     * Role izin ata
     */
    public function assignPermissions($roleId, $permissionIds)
    {
        $db = \Config\Database::connect();
        
        // Önce mevcut izinleri sil
        $db->table('role_permissions')->where('role_id', $roleId)->delete();
        
        // Yeni izinleri ekle
        if (!empty($permissionIds)) {
            $data = [];
            foreach ($permissionIds as $permissionId) {
                $data[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            $db->table('role_permissions')->insertBatch($data);
        }
        
        return true;
    }

    /**
     * Rol silmeden önce kullanılıp kullanılmadığını kontrol et
     */
    protected function checkRoleUsage(array $data)
    {
        $db = \Config\Database::connect();
        $userCount = $db->table('users')
                        ->where('role_id', $data['id'][0])
                        ->countAllResults();
        
        if ($userCount > 0) {
            throw new \RuntimeException('Bu rol kullanımda olduğu için silinemez. Önce kullanıcıları başka role atayın.');
        }
        
        return $data;
    }
}