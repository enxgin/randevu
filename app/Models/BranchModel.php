<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchModel extends Model
{
    protected $table = 'branches';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'address', 
        'phone',
        'email',
        'working_hours',
        'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'working_hours' => 'json',
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
        'name' => 'required|max_length[255]|is_unique[branches.name,id,{id}]',
        'email' => 'permit_empty|valid_email',
        'phone' => 'permit_empty|max_length[20]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Şube adı zorunludur.',
            'max_length' => 'Şube adı en fazla 255 karakter olabilir.',
            'is_unique' => 'Bu şube adı zaten kullanılmaktadır.'
        ],
        'email' => [
            'valid_email' => 'Geçerli bir e-posta adresi giriniz.'
        ],
        'phone' => [
            'max_length' => 'Telefon numarası en fazla 20 karakter olabilir.'
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
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Aktif şubeleri getir
     */
    public function getActiveBranches()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Şube ile birlikte kullanıcı sayısını getir
     */
    public function getBranchesWithUserCount()
    {
        return $this->select('branches.*, COUNT(users.id) as user_count')
                    ->join('users', 'users.branch_id = branches.id', 'left')
                    ->groupBy('branches.id')
                    ->findAll();
    }
}