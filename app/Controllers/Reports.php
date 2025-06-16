<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\CashMovementModel;
use App\Models\CommissionModel;
use App\Models\CustomerModel;
use App\Models\AppointmentModel;
use App\Models\UserModel;
use App\Models\BranchModel;

class Reports extends BaseController
{
    protected $paymentModel;
    protected $cashMovementModel;
    protected $commissionModel;
    protected $customerModel;
    protected $appointmentModel;
    protected $userModel;
    protected $branchModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->cashMovementModel = new CashMovementModel();
        $this->commissionModel = new CommissionModel();
        $this->customerModel = new CustomerModel();
        $this->appointmentModel = new AppointmentModel();
        $this->userModel = new UserModel();
        $this->branchModel = new BranchModel();
    }

    /**
     * Raporlar ana sayfası
     */
    public function index()
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');

        // Admin için şube listesi
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->findAll();
        }

        $data = [
            'title' => 'Raporlar',
            'userRole' => $userRole,
            'branchId' => $branchId,
            'branches' => $branches
        ];

        return view('reports/index', $data);
    }

    /**
     * Günlük Kasa Raporu
     */
    public function dailyCashReport()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $branchId = session()->get('branch_id');
        $userRole = session()->get('role_name');

        // Admin tüm şubeleri görebilir
        if ($userRole === 'admin' && $this->request->getGet('branch_id')) {
            $branchId = $this->request->getGet('branch_id');
        }

        // Admin için şube listesi
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->findAll();
        }

        // Günlük ödemeler
        $payments = $this->paymentModel->getDailyPayments($branchId, $date);
        
        // Günlük kasa hareketleri
        $cashMovements = $this->cashMovementModel->getDailyCashMovements($branchId, $date);
        
        // Günlük özet
        $summary = $this->paymentModel->getDailyPaymentSummary($branchId, $date);
        $cashSummary = $this->cashMovementModel->getDailyCashSummary($branchId, $date);

        // Borçlu müşteriler
        $debtCustomers = $this->customerModel->getDebtCustomers($branchId);

        $data = [
            'title' => 'Günlük Kasa Raporu',
            'date' => $date,
            'payments' => $payments,
            'cashMovements' => $cashMovements,
            'summary' => $summary,
            'cashSummary' => $cashSummary,
            'debtCustomers' => $debtCustomers,
            'branchId' => $branchId,
            'userRole' => $userRole,
            'branches' => $branches
        ];

        return view('reports/daily_cash', $data);
    }

    /**
     * Detaylı Kasa Geçmişi
     */
    public function cashHistory()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $type = $this->request->getGet('type') ?? '';
        $branchId = session()->get('branch_id');
        $userRole = session()->get('role_name');

        // Admin tüm şubeleri görebilir
        if ($userRole === 'admin' && $this->request->getGet('branch_id')) {
            $branchId = $this->request->getGet('branch_id');
        }

        // Admin için şube listesi
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->findAll();
        }

        // Kasa hareketleri
        $cashMovements = $this->cashMovementModel->getCashHistory($branchId, $startDate, $endDate, $type);
        
        // Ödemeler
        $payments = $this->paymentModel->getPaymentHistory($branchId, $startDate, $endDate);
        
        // Özet
        $summary = $this->cashMovementModel->getCashHistorySummary($branchId, $startDate, $endDate);

        $data = [
            'title' => 'Detaylı Kasa Geçmişi',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'type' => $type,
            'cashMovements' => $cashMovements,
            'payments' => $payments,
            'summary' => $summary,
            'branchId' => $branchId,
            'userRole' => $userRole,
            'branches' => $branches
        ];

        return view('reports/cash_history', $data);
    }

    /**
     * Alacak/Borç Raporu
     */
    public function debtReport()
    {
        $branchId = session()->get('branch_id');
        $userRole = session()->get('role_name');

        // Admin tüm şubeleri görebilir
        if ($userRole === 'admin' && $this->request->getGet('branch_id')) {
            $branchId = $this->request->getGet('branch_id');
        }

        // Admin için şube listesi
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->findAll();
        }

        // Borçlu müşteriler
        $debtCustomers = $this->customerModel->getDebtCustomersDetailed($branchId);
        
        // Borç özeti
        $debtSummary = $this->customerModel->getDebtSummary($branchId);

        $data = [
            'title' => 'Alacak/Borç Raporu',
            'debtCustomers' => $debtCustomers,
            'debtSummary' => $debtSummary,
            'branchId' => $branchId,
            'userRole' => $userRole,
            'branches' => $branches
        ];

        return view('reports/debt_report', $data);
    }

    /**
     * Personel Prim Raporu
     */
    public function staffCommissionReport()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $userId = $this->request->getGet('user_id') ?? null;
        $branchId = session()->get('branch_id');
        $userRole = session()->get('role_name');
        $currentUserId = session()->get('user_id');

        // Admin tüm şubeleri görebilir
        if ($userRole === 'admin' && $this->request->getGet('branch_id')) {
            $branchId = $this->request->getGet('branch_id');
        }

        // Personel sadece kendi primini görebilir
        if ($userRole === 'staff') {
            $userId = $currentUserId;
        }

        // Admin için şube listesi
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->findAll();
        }

        // Personel listesi
        $staff = $this->userModel->getStaffByBranch($branchId);

        // Prim kayıtları
        if ($userId) {
            $commissions = $this->commissionModel->getUserCommissions($userId, $startDate, $endDate);
            $commissionSummary = $this->commissionModel->getUserCommissionSummary($userId, $startDate, $endDate);
        } else {
            $commissions = $this->commissionModel->getBranchCommissions($branchId, $startDate, $endDate);
            $commissionSummary = $this->commissionModel->getBranchCommissionSummary($branchId, $startDate, $endDate);
        }

        // Aylık prim raporu
        $monthlyReport = $this->commissionModel->getMonthlyCommissionReport($branchId, date('Y', strtotime($startDate)), date('m', strtotime($startDate)));

        $data = [
            'title' => 'Personel Prim Raporu',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userId' => $userId,
            'staff' => $staff,
            'commissions' => $commissions,
            'commissionSummary' => $commissionSummary,
            'monthlyReport' => $monthlyReport,
            'branchId' => $branchId,
            'userRole' => $userRole,
            'branches' => $branches
        ];

        return view('reports/staff_commission', $data);
    }

    /**
     * Finansal Dashboard İstatistikleri
     */
    public function financialDashboard()
    {
        $branchId = session()->get('branch_id');
        $userRole = session()->get('role_name');

        // Admin tüm şubeleri görebilir
        if ($userRole === 'admin' && $this->request->getGet('branch_id')) {
            $branchId = $this->request->getGet('branch_id');
        }

        // Admin için şube listesi
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->findAll();
        }

        // Bu ay
        $thisMonth = date('Y-m');
        $thisMonthStart = $thisMonth . '-01';
        $thisMonthEnd = date('Y-m-t');

        // Geçen ay
        $lastMonth = date('Y-m', strtotime('-1 month'));
        $lastMonthStart = $lastMonth . '-01';
        $lastMonthEnd = date('Y-m-t', strtotime($lastMonthStart));

        // Bu ay istatistikleri
        $thisMonthStats = [
            'payments' => $this->paymentModel->getPaymentSummary($branchId, $thisMonthStart, $thisMonthEnd),
            'commissions' => $this->commissionModel->getBranchCommissionSummary($branchId, $thisMonthStart, $thisMonthEnd),
            'appointments' => $this->appointmentModel->getAppointmentStats($branchId, $thisMonthStart, $thisMonthEnd),
            'customers' => $this->customerModel->getCustomerStats($branchId, $thisMonthStart, $thisMonthEnd)
        ];

        // Geçen ay istatistikleri
        $lastMonthStats = [
            'payments' => $this->paymentModel->getPaymentSummary($branchId, $lastMonthStart, $lastMonthEnd),
            'commissions' => $this->commissionModel->getBranchCommissionSummary($branchId, $lastMonthStart, $lastMonthEnd),
            'appointments' => $this->appointmentModel->getAppointmentStats($branchId, $lastMonthStart, $lastMonthEnd),
            'customers' => $this->customerModel->getCustomerStats($branchId, $lastMonthStart, $lastMonthEnd)
        ];

        // Günlük gelir grafiği için veri (son 30 gün)
        $dailyRevenue = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayRevenue = $this->paymentModel->getDailyPaymentSummary($branchId, $date);
            $dailyRevenue[] = [
                'date' => $date,
                'revenue' => $dayRevenue['total_amount'] ?? 0
            ];
        }

        $data = [
            'title' => 'Finansal Dashboard',
            'thisMonthStats' => $thisMonthStats,
            'lastMonthStats' => $lastMonthStats,
            'dailyRevenue' => $dailyRevenue,
            'branchId' => $branchId,
            'userRole' => $userRole,
            'branches' => $branches
        ];

        return view('reports/financial_dashboard', $data);
    }
}