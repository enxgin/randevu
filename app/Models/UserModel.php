<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'role_id', 
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'avatar',
        'working_hours',
        'commission_rate',
        'is_active',
        'last_login'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        // Geçici olarak kapatıldı - DataCaster hatası giderilene kadar
        // 'working_hours' => 'json',
        // 'commission_rate' => 'float',
        // 'is_active' => 'boolean'
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
        'branch_id' => 'required|integer|is_not_unique[branches.id]',
        'role_id' => 'required|integer|is_not_unique[roles.id]',
        'username' => 'required|min_length[3]|max_length[100]|alpha_numeric_punct|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'first_name' => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/]',
        'last_name' => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/]',
        'phone' => 'permit_empty|min_length[10]|max_length[20]|regex_match[/^[0-9\+\-\s\(\)]+$/]',
        'commission_rate' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required' => 'Şube seçimi zorunludur.',
            'is_not_unique' => 'Seçilen şube mevcut değil.'
        ],
        'role_id' => [
            'required' => 'Rol seçimi zorunludur.',
            'is_not_unique' => 'Seçilen rol mevcut değil.'
        ],
        'username' => [
            'required' => 'Kullanıcı adı zorunludur.',
            'min_length' => 'Kullanıcı adı en az 3 karakter olmalıdır.',
            'max_length' => 'Kullanıcı adı en fazla 100 karakter olabilir.',
            'alpha_numeric_punct' => 'Kullanıcı adı sadece harf, rakam ve noktalama işaretleri içerebilir.',
            'is_unique' => 'Bu kullanıcı adı zaten kullanılıyor.'
        ],
        'email' => [
            'required' => 'E-posta adresi zorunludur.',
            'valid_email' => 'Geçerli bir e-posta adresi giriniz.',
            'is_unique' => 'Bu e-posta adresi zaten kullanılıyor.'
        ],
        'password' => [
            'required' => 'Şifre zorunludur.',
            'min_length' => 'Şifre en az 6 karakter olmalıdır.'
        ],
        'first_name' => [
            'required' => 'Ad zorunludur.',
            'min_length' => 'Ad en az 2 karakter olmalıdır.',
            'regex_match' => 'Ad sadece harf ve boşluk içerebilir.'
        ],
        'last_name' => [
            'required' => 'Soyad zorunludur.',
            'min_length' => 'Soyad en az 2 karakter olmalıdır.',
            'regex_match' => 'Soyad sadece harf ve boşluk içerebilir.'
        ],
        'phone' => [
            'regex_match' => 'Geçerli bir telefon numarası giriniz.'
        ],
        'commission_rate' => [
            'numeric' => 'Prim oranı sayısal değer olmalıdır.',
            'greater_than_equal_to' => 'Prim oranı 0 veya daha büyük olmalıdır.',
            'less_than_equal_to' => 'Prim oranı 100\'den küçük veya eşit olmalıdır.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['hashPassword'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Şifre hash'leme callback'i
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        // Eğer şifre değişmemişse hash'leme
        if (isset($data['id']) && !empty($data['id'])) {
            $currentUser = $this->find($data['id']);
            if ($currentUser && $currentUser['password'] === $data['data']['password']) {
                return $data;
            }
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    /**
     * Şube ile birlikte kullanıcıları getir
     */
    public function getUsersWithBranch()
    {
        return $this->select('users.*, branches.name as branch_name, roles.name as role_name')
                    ->join('branches', 'branches.id = users.branch_id')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.deleted_at', null)
                    ->orderBy('users.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Detaylı kullanıcı listesi (şube ve rol bilgileri ile)
     */
    public function getUsersWithDetails($branchId = null)
    {
        $builder = $this->select('users.*, branches.name as branch_name, roles.display_name as role_display_name, roles.name as role_name')
                        ->join('branches', 'branches.id = users.branch_id')
                        ->join('roles', 'roles.id = users.role_id')
                        ->where('users.deleted_at', null);

        if ($branchId) {
            $builder->where('users.branch_id', $branchId);
        }

        return $builder->orderBy('users.created_at', 'DESC')->findAll();
    }

    /**
     * Belirli şubeye ait kullanıcıları getir
     */
    public function getUsersByBranch($branchId)
    {
        return $this->select('users.*, branches.name as branch_name, roles.name as role_name')
                    ->join('branches', 'branches.id = users.branch_id')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.branch_id', $branchId)
                    ->where('users.deleted_at', null)
                    ->orderBy('users.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Kullanıcı adı veya e-posta ile kullanıcı bul
     */
    public function findByCredentials($login)
    {
        return $this->select('users.*, branches.name as branch_name, roles.name as role_name')
                    ->join('branches', 'branches.id = users.branch_id')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.deleted_at', null)
                    ->where('users.is_active', 1)
                    ->groupStart()
                        ->where('users.username', $login)
                        ->orWhere('users.email', $login)
                    ->groupEnd()
                    ->first();
    }

    /**
     * Şifre doğrulama
     */
    public function verifyPassword($plainPassword, $hashedPassword)
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    /**
     * Son giriş zamanını güncelle
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Kullanıcı detayı (şube ve rol bilgileri ile)
     */
    public function getUserDetail($id)
    {
        return $this->select('users.*, branches.name as branch_name, roles.name as role_name')
                    ->join('branches', 'branches.id = users.branch_id')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.id', $id)
                    ->where('users.deleted_at', null)
                    ->first();
    }

    /**
     * E-posta ile kullanıcı ve ilişkili şube/rol bilgilerini getir
     */
    public function getUserWithBranchAndRole($email)
    {
        return $this->select('users.id, users.branch_id, users.role_id, users.username, users.email, users.password, users.first_name, users.last_name, users.phone, users.avatar, users.working_hours, users.commission_rate, users.is_active, users.last_login, users.created_at, users.updated_at, branches.name as branch_name, roles.name as role_name, roles.display_name as role_display_name')
                    ->join('branches', 'branches.id = users.branch_id')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.email', $email)
                    ->where('users.deleted_at', null)
                    ->first();
    }

    /**
     * Aktif kullanıcı sayısı
     */
    public function getActiveUserCount()
    {
        return $this->where('is_active', 1)
                    ->where('deleted_at', null)
                    ->countAllResults();
    }

    /**
     * Belirli şubedeki aktif kullanıcı sayısı
     */
    public function getActiveUserCountByBranch($branchId)
    {
        return $this->where('branch_id', $branchId)
                    ->where('is_active', 1)
                    ->where('deleted_at', null)
                    ->countAllResults();
    }

    /**
     * Personel kullanıcılarını getir
     */
    public function getStaffUsers($branchId = null)
    {
        $builder = $this->select('users.*, roles.name as role_name')
                        ->join('roles', 'roles.id = users.role_id')
                        ->where('users.is_active', 1)
                        ->where('users.deleted_at', null)
                        ->whereIn('roles.name', ['staff', 'personnel', 'manager', 'receptionist']);

        if ($branchId) {
            $builder->where('users.branch_id', $branchId);
        }

        return $builder->orderBy('users.first_name, users.last_name')->findAll();
    }

    /**
     * Belirli hizmeti verebilen personelleri getir
     */
    public function getServiceStaff($serviceId, $branchId = null)
    {
        $builder = $this->select('users.id, users.first_name, users.last_name, users.phone, users.branch_id')
                        ->join('service_staff', 'service_staff.user_id = users.id')
                        ->where('service_staff.service_id', $serviceId)
                        ->where('users.is_active', 1)
                        ->where('users.deleted_at', null);

        if ($branchId) {
            $builder->where('users.branch_id', $branchId);
        }

        return $builder->orderBy('users.first_name, users.last_name')->findAll();
    }

    /**
     * Şubeye göre personelleri getir
     */
    public function getStaffByBranch($branchId)
    {
        return $this->select('users.*, roles.name as role_name, roles.display_name as role_display_name')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.branch_id', $branchId)
                    ->where('users.is_active', 1)
                    ->where('users.deleted_at', null)
                    ->whereIn('roles.name', ['staff', 'personnel', 'manager', 'receptionist'])
                    ->orderBy('users.first_name, users.last_name')
                    ->findAll();
    }

    /**
     * Belirli paketteki hizmetleri verebilen personelleri getir
     */
    public function getPackageStaff($packageId, $branchId = null)
    {
        $builder = $this->select('users.id, users.first_name, users.last_name, users.phone, users.branch_id')
                        ->join('service_staff', 'service_staff.user_id = users.id')
                        ->join('package_services', 'package_services.service_id = service_staff.service_id')
                        ->where('package_services.package_id', $packageId)
                        ->where('users.is_active', 1)
                        ->where('users.deleted_at', null)
                        ->groupBy('users.id'); // Aynı personelin birden fazla kez gelmemesi için

        if ($branchId) {
            $builder->where('users.branch_id', $branchId);
        }

        return $builder->orderBy('users.first_name, users.last_name')->findAll();
    }
}