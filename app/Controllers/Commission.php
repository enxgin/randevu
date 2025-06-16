<?php

namespace App\Controllers;

use App\Models\CommissionModel;
use App\Models\CommissionRuleModel;
use App\Models\UserModel;
use App\Models\ServiceModel;
use App\Models\BranchModel;

class Commission extends BaseController
{
    protected $commissionModel;
    protected $commissionRuleModel;
    protected $userModel;
    protected $serviceModel;
    protected $branchModel;

    public function __construct()
    {
        $this->commissionModel = new CommissionModel();
        $this->commissionRuleModel = new CommissionRuleModel();
        $this->userModel = new UserModel();
        $this->serviceModel = new ServiceModel();
        $this->branchModel = new BranchModel();
    }

    /**
     * Prim kuralları listesi
     */
    public function rules()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Yetki kontrolü
        if (!in_array($userRole, ['admin', 'manager'])) {
            return redirect()->to('/auth/unauthorized');
        }

        // Admin tüm şubeleri, yönetici sadece kendi şubesini görebilir
        $branchId = ($userRole === 'admin') ? null : $userBranchId;
        
        $data = [
            'title' => 'Prim Kuralları',
            'rules' => $this->commissionRuleModel->getAllRulesWithDetails($branchId),
            'branches' => ($userRole === 'admin') ? $this->branchModel->findAll() : [$this->branchModel->find($userBranchId)],
            'userRole' => $userRole
        ];

        return view('commissions/rules/index', $data);
    }

    /**
     * Yeni prim kuralı oluşturma formu
     */
    public function createRule()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Yetki kontrolü
        if (!in_array($userRole, ['admin', 'manager'])) {
            return redirect()->to('/auth/unauthorized');
        }

        $data = [
            'title' => 'Yeni Prim Kuralı',
            'branches' => ($userRole === 'admin') ? $this->branchModel->findAll() : [$this->branchModel->find($userBranchId)],
            'users' => $this->userModel->getUsersByBranch($userBranchId),
            'services' => $this->serviceModel->getServicesByBranch($userBranchId),
            'ruleTypes' => $this->commissionRuleModel->getRuleTypeLabels(),
            'commissionTypes' => $this->commissionRuleModel->getCommissionTypeLabels(),
            'userRole' => $userRole
        ];

        return view('commissions/rules/create', $data);
    }

    /**
     * Prim kuralı kaydetme
     */
    public function storeRule()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Yetki kontrolü
        if (!in_array($userRole, ['admin', 'manager'])) {
            return redirect()->to('/auth/unauthorized');
        }

        $rules = [
            'branch_id' => 'required|is_natural_no_zero',
            'rule_type' => 'required|in_list[general,service_specific,user_specific]',
            'commission_type' => 'required|in_list[percentage,fixed_amount]',
            'commission_value' => 'required|numeric|greater_than[0]',
            'is_package_rule' => 'required|in_list[0,1]',
            'is_active' => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Yönetici sadece kendi şubesi için kural oluşturabilir
        if ($userRole === 'manager') {
            $data['branch_id'] = $userBranchId;
        }

        // Kural tipine göre alanları temizle
        if ($data['rule_type'] === 'general') {
            $data['user_id'] = null;
            $data['service_id'] = null;
        } elseif ($data['rule_type'] === 'service_specific') {
            $data['user_id'] = null;
        }

        if ($this->commissionRuleModel->insert($data)) {
            return redirect()->to('/commissions/rules')->with('success', 'Prim kuralı başarıyla oluşturuldu.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Prim kuralı oluşturulurken bir hata oluştu.');
        }
    }

    /**
     * Prim kuralı düzenleme formu
     */
    public function editRule($id)
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Yetki kontrolü
        if (!in_array($userRole, ['admin', 'manager'])) {
            return redirect()->to('/auth/unauthorized');
        }

        $rule = $this->commissionRuleModel->find($id);
        if (!$rule) {
            return redirect()->to('/commissions/rules')->with('error', 'Prim kuralı bulunamadı.');
        }

        // Yönetici sadece kendi şubesinin kurallarını düzenleyebilir
        if ($userRole === 'manager' && $rule['branch_id'] != $userBranchId) {
            return redirect()->to('/auth/unauthorized');
        }

        $data = [
            'title' => 'Prim Kuralı Düzenle',
            'rule' => $rule,
            'branches' => ($userRole === 'admin') ? $this->branchModel->findAll() : [$this->branchModel->find($userBranchId)],
            'users' => $this->userModel->getUsersByBranch($rule['branch_id']),
            'services' => $this->serviceModel->getServicesByBranch($rule['branch_id']),
            'ruleTypes' => $this->commissionRuleModel->getRuleTypeLabels(),
            'commissionTypes' => $this->commissionRuleModel->getCommissionTypeLabels(),
            'userRole' => $userRole
        ];

        return view('commissions/rules/edit', $data);
    }

    /**
     * Prim kuralı güncelleme
     */
    public function updateRule($id)
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Yetki kontrolü
        if (!in_array($userRole, ['admin', 'manager'])) {
            return redirect()->to('/auth/unauthorized');
        }

        $rule = $this->commissionRuleModel->find($id);
        if (!$rule) {
            return redirect()->to('/commissions/rules')->with('error', 'Prim kuralı bulunamadı.');
        }

        // Yönetici sadece kendi şubesinin kurallarını güncelleyebilir
        if ($userRole === 'manager' && $rule['branch_id'] != $userBranchId) {
            return redirect()->to('/auth/unauthorized');
        }

        $rules = [
            'branch_id' => 'required|is_natural_no_zero',
            'rule_type' => 'required|in_list[general,service_specific,user_specific]',
            'commission_type' => 'required|in_list[percentage,fixed_amount]',
            'commission_value' => 'required|numeric|greater_than[0]',
            'is_package_rule' => 'required|in_list[0,1]',
            'is_active' => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Yönetici sadece kendi şubesi için kural güncelleyebilir
        if ($userRole === 'manager') {
            $data['branch_id'] = $userBranchId;
        }

        // Kural tipine göre alanları temizle
        if ($data['rule_type'] === 'general') {
            $data['user_id'] = null;
            $data['service_id'] = null;
        } elseif ($data['rule_type'] === 'service_specific') {
            $data['user_id'] = null;
        }

        if ($this->commissionRuleModel->update($id, $data)) {
            return redirect()->to('/commissions/rules')->with('success', 'Prim kuralı başarıyla güncellendi.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Prim kuralı güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Prim kuralı silme
     */
    public function deleteRule($id)
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Yetki kontrolü
        if (!in_array($userRole, ['admin', 'manager'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Yetkiniz bulunmamaktadır.']);
        }

        $rule = $this->commissionRuleModel->find($id);
        if (!$rule) {
            return $this->response->setJSON(['success' => false, 'message' => 'Prim kuralı bulunamadı.']);
        }

        // Yönetici sadece kendi şubesinin kurallarını silebilir
        if ($userRole === 'manager' && $rule['branch_id'] != $userBranchId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Yetkiniz bulunmamaktadır.']);
        }

        if ($this->commissionRuleModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Prim kuralı başarıyla silindi.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Prim kuralı silinirken bir hata oluştu.']);
        }
    }

    /**
     * Prim raporları
     */
    public function reports()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // Personel sadece kendi prim raporunu görebilir
        if ($userRole === 'staff') {
            return $this->staffReport();
        }

        // Yetki kontrolü
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return redirect()->to('/auth/unauthorized');
        }

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');
        $selectedUserId = $this->request->getGet('user_id');
        $status = $this->request->getGet('status');

        // Admin tüm şubeleri, yönetici sadece kendi şubesini görebilir
        $branchId = ($userRole === 'admin') ? null : $userBranchId;

        $data = [
            'title' => 'Prim Raporları',
            'commissions' => $this->commissionModel->getBranchCommissions($branchId, $startDate, $endDate, $status),
            'summary' => $this->commissionModel->getBranchCommissionSummary($branchId, $startDate, $endDate),
            'users' => $this->userModel->getUsersByBranch($userBranchId),
            'branches' => ($userRole === 'admin') ? $this->branchModel->findAll() : [$this->branchModel->find($userBranchId)],
            'statusLabels' => $this->commissionModel->getStatusLabels(),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $selectedUserId,
                'status' => $status
            ],
            'userRole' => $userRole
        ];

        return view('commissions/reports/index', $data);
    }

    /**
     * Personel prim raporu
     */
    public function staffReport()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('role_name');

        // Sadece personel erişebilir
        if ($userRole !== 'staff') {
            return redirect()->to('/commissions/reports');
        }

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');
        $status = $this->request->getGet('status');

        $data = [
            'title' => 'Prim Raporum',
            'commissions' => $this->commissionModel->getUserCommissions($userId, $startDate, $endDate, $status),
            'summary' => $this->commissionModel->getUserCommissionSummary($userId, $startDate, $endDate),
            'statusLabels' => $this->commissionModel->getStatusLabels(),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status
            ]
        ];

        return view('commissions/reports/staff', $data);
    }

    /**
     * AJAX - Şubeye göre personelleri getir
     */
    public function getUsersByBranch($branchId)
    {
        $users = $this->userModel->getUsersByBranch($branchId);
        return $this->response->setJSON($users);
    }

    /**
     * AJAX - Şubeye göre hizmetleri getir
     */
    public function getServicesByBranch($branchId)
    {
        $services = $this->serviceModel->getServicesByBranch($branchId);
        return $this->response->setJSON($services);
    }
}