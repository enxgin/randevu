<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionRuleModel extends Model
{
    protected $table = 'commission_rules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'user_id',
        'service_id',
        'rule_type',
        'commission_type',
        'commission_value',
        'is_package_rule',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'branch_id' => 'required|is_natural_no_zero',
        'rule_type' => 'required|in_list[general,service_specific,user_specific]',
        'commission_type' => 'required|in_list[percentage,fixed_amount]',
        'commission_value' => 'required|numeric|greater_than[0]',
        'is_package_rule' => 'required|in_list[0,1]',
        'is_active' => 'required|in_list[0,1]'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required' => 'Şube seçimi zorunludur.',
            'is_natural_no_zero' => 'Geçerli bir şube seçiniz.'
        ],
        'rule_type' => [
            'required' => 'Kural tipi seçimi zorunludur.',
            'in_list' => 'Geçerli bir kural tipi seçiniz.'
        ],
        'commission_type' => [
            'required' => 'Prim tipi seçimi zorunludur.',
            'in_list' => 'Geçerli bir prim tipi seçiniz.'
        ],
        'commission_value' => [
            'required' => 'Prim değeri zorunludur.',
            'numeric' => 'Prim değeri sayısal olmalıdır.',
            'greater_than' => 'Prim değeri 0\'dan büyük olmalıdır.'
        ],
        'is_package_rule' => [
            'required' => 'Paket kuralı durumu zorunludur.',
            'in_list' => 'Geçerli bir paket kuralı durumu seçiniz.'
        ],
        'is_active' => [
            'required' => 'Aktiflik durumu zorunludur.',
            'in_list' => 'Geçerli bir aktiflik durumu seçiniz.'
        ]
    ];

    /**
     * Şubeye göre aktif prim kurallarını getir
     */
    public function getActiveRulesByBranch($branchId)
    {
        return $this->select('commission_rules.*, users.first_name, users.last_name, services.name as service_name, branches.name as branch_name')
                    ->join('users', 'users.id = commission_rules.user_id', 'left')
                    ->join('services', 'services.id = commission_rules.service_id', 'left')
                    ->join('branches', 'branches.id = commission_rules.branch_id', 'left')
                    ->where('commission_rules.branch_id', $branchId)
                    ->where('commission_rules.is_active', 1)
                    ->orderBy('commission_rules.rule_type', 'ASC')
                    ->orderBy('commission_rules.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Personele göre aktif prim kurallarını getir
     */
    public function getActiveRulesByUser($userId, $branchId = null)
    {
        $builder = $this->select('commission_rules.*, services.name as service_name')
                        ->join('services', 'services.id = commission_rules.service_id', 'left')
                        ->where('commission_rules.is_active', 1)
                        ->where('(commission_rules.user_id = ' . $userId . ' OR commission_rules.rule_type = "general")')
                        ->orderBy('commission_rules.rule_type', 'DESC'); // user_specific önce gelsin

        if ($branchId) {
            $builder->where('commission_rules.branch_id', $branchId);
        }

        return $builder->findAll();
    }

    /**
     * Hizmete göre prim kuralını getir
     */
    public function getRuleForService($serviceId, $userId, $branchId, $isPackage = false)
    {
        // Önce kullanıcıya özel kural ara
        $userRule = $this->where('branch_id', $branchId)
                         ->where('user_id', $userId)
                         ->where('service_id', $serviceId)
                         ->where('is_package_rule', $isPackage ? 1 : 0)
                         ->where('is_active', 1)
                         ->where('rule_type', 'user_specific')
                         ->first();

        if ($userRule) {
            return $userRule;
        }

        // Sonra hizmete özel genel kural ara
        $serviceRule = $this->where('branch_id', $branchId)
                            ->where('service_id', $serviceId)
                            ->where('is_package_rule', $isPackage ? 1 : 0)
                            ->where('is_active', 1)
                            ->where('rule_type', 'service_specific')
                            ->whereNull('user_id')
                            ->first();

        if ($serviceRule) {
            return $serviceRule;
        }

        // Son olarak genel kural ara
        $generalRule = $this->where('branch_id', $branchId)
                            ->where('is_package_rule', $isPackage ? 1 : 0)
                            ->where('is_active', 1)
                            ->where('rule_type', 'general')
                            ->whereNull('user_id')
                            ->whereNull('service_id')
                            ->first();

        return $generalRule;
    }

    /**
     * Prim hesapla
     */
    public function calculateCommission($serviceAmount, $rule)
    {
        if (!$rule) {
            return 0;
        }

        if ($rule['commission_type'] === 'percentage') {
            return ($serviceAmount * $rule['commission_value']) / 100;
        } else {
            return $rule['commission_value'];
        }
    }

    /**
     * Tüm kuralları listele (admin için)
     */
    public function getAllRulesWithDetails($branchId = null)
    {
        $builder = $this->select('commission_rules.*, users.first_name, users.last_name, services.name as service_name, branches.name as branch_name')
                        ->join('users', 'users.id = commission_rules.user_id', 'left')
                        ->join('services', 'services.id = commission_rules.service_id', 'left')
                        ->join('branches', 'branches.id = commission_rules.branch_id', 'left');

        if ($branchId) {
            $builder->where('commission_rules.branch_id', $branchId);
        }

        return $builder->orderBy('commission_rules.branch_id', 'ASC')
                       ->orderBy('commission_rules.rule_type', 'ASC')
                       ->orderBy('commission_rules.created_at', 'DESC')
                       ->findAll();
    }

    /**
     * Kural tipi açıklamaları
     */
    public function getRuleTypeLabels()
    {
        return [
            'general' => 'Genel Kural',
            'service_specific' => 'Hizmete Özel',
            'user_specific' => 'Personele Özel'
        ];
    }

    /**
     * Prim tipi açıklamaları
     */
    public function getCommissionTypeLabels()
    {
        return [
            'percentage' => 'Yüzdesel (%)',
            'fixed_amount' => 'Sabit Tutar (₺)'
        ];
    }
}