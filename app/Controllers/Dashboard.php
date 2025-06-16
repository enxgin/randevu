<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\CustomerModel;
use App\Models\PaymentModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $appointmentModel;
    protected $customerModel;
    protected $paymentModel;
    protected $userModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->customerModel = new CustomerModel();
        $this->paymentModel = new PaymentModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Kullanıcı bilgilerini session'dan al
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // Bugünün tarihi
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        // Rol bazlı veri filtreleme
        $branchFilter = null;
        $staffFilter = null;

        switch ($userRole) {
            case 'admin':
                // Admin tüm şubeleri görebilir
                $branchFilter = null;
                break;
            case 'manager':
                // Yönetici sadece kendi şubesini görebilir
                $branchFilter = $userBranchId;
                break;
            case 'receptionist':
                // Danışma sadece kendi şubesini görebilir
                $branchFilter = $userBranchId;
                break;
            case 'staff':
                // Personel sadece kendi randevularını görebilir
                $branchFilter = $userBranchId;
                $staffFilter = $userId;
                break;
        }

        // Dashboard istatistiklerini hesapla
        $stats = $this->calculateDashboardStats($branchFilter, $staffFilter, $today, $thisMonth);

        // Son randevuları getir
        $recentAppointments = $this->getRecentAppointments($branchFilter, $staffFilter, 5);

        // Haftalık performans verilerini getir (son 7 gün)
        $weeklyPerformance = $this->getWeeklyPerformance($branchFilter, $staffFilter);

        $data = [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'breadcrumb' => [
                ['title' => 'Ana Sayfa']
            ],
            'stats' => $stats,
            'recentAppointments' => $recentAppointments,
            'weeklyPerformance' => $weeklyPerformance,
            'userRole' => $userRole
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Dashboard istatistiklerini hesapla
     */
    private function calculateDashboardStats($branchFilter, $staffFilter, $today, $thisMonth)
    {
        $stats = [];

        // Bugünkü randevular
        $todayAppointmentsBuilder = $this->appointmentModel
            ->where('appointment_date', $today)
            ->whereNotIn('status', ['cancelled']);

        if ($branchFilter) {
            $todayAppointmentsBuilder->where('branch_id', $branchFilter);
        }
        if ($staffFilter) {
            $todayAppointmentsBuilder->where('staff_id', $staffFilter);
        }

        $stats['todayAppointments'] = $todayAppointmentsBuilder->countAllResults();

        // Toplam müşteri sayısı
        $totalCustomersBuilder = $this->customerModel->where('deleted_at', null);
        if ($branchFilter) {
            $totalCustomersBuilder->where('branch_id', $branchFilter);
        }
        $stats['totalCustomers'] = $totalCustomersBuilder->countAllResults();

        // Günlük ciro (bugün tamamlanan randevuların toplam tutarı)
        $dailyRevenueBuilder = $this->appointmentModel
            ->selectSum('price', 'total_revenue')
            ->where('appointment_date', $today)
            ->where('status', 'completed');

        if ($branchFilter) {
            $dailyRevenueBuilder->where('branch_id', $branchFilter);
        }
        if ($staffFilter) {
            $dailyRevenueBuilder->where('staff_id', $staffFilter);
        }

        $dailyRevenueResult = $dailyRevenueBuilder->first();
        $stats['dailyRevenue'] = $dailyRevenueResult['total_revenue'] ?? 0;

        // Bekleyen ödemeler (ödenmemiş randevular)
        $db = \Config\Database::connect();
        $pendingQuery = "
            SELECT COALESCE(SUM(price - paid_amount), 0) as total_pending
            FROM appointments
            WHERE payment_status IN ('pending', 'partial')
            AND status = 'completed'
            AND deleted_at IS NULL
        ";
        
        $pendingParams = [];
        if ($branchFilter) {
            $pendingQuery .= " AND branch_id = ?";
            $pendingParams[] = $branchFilter;
        }
        if ($staffFilter) {
            $pendingQuery .= " AND staff_id = ?";
            $pendingParams[] = $staffFilter;
        }

        $pendingResult = $db->query($pendingQuery, $pendingParams)->getRowArray();
        $stats['pendingPayments'] = $pendingResult['total_pending'] ?? 0;

        return $stats;
    }

    /**
     * Son randevuları getir
     */
    private function getRecentAppointments($branchFilter, $staffFilter, $limit = 5)
    {
        $builder = $this->appointmentModel
            ->select('
                appointments.*,
                customers.first_name as customer_first_name,
                customers.last_name as customer_last_name,
                services.name as service_name,
                staff.first_name as staff_first_name,
                staff.last_name as staff_last_name
            ')
            ->join('customers', 'customers.id = appointments.customer_id')
            ->join('services', 'services.id = appointments.service_id')
            ->join('users staff', 'staff.id = appointments.staff_id')
            ->orderBy('appointments.appointment_date DESC, appointments.start_time DESC')
            ->limit($limit);

        if ($branchFilter) {
            $builder->where('appointments.branch_id', $branchFilter);
        }
        if ($staffFilter) {
            $builder->where('appointments.staff_id', $staffFilter);
        }

        return $builder->findAll();
    }

    /**
     * Haftalık performans verilerini getir
     */
    private function getWeeklyPerformance($branchFilter, $staffFilter)
    {
        $weeklyData = [];
        
        // Son 7 günün verilerini al
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            
            $builder = $this->appointmentModel
                ->selectSum('price', 'daily_revenue')
                ->selectCount('id', 'daily_appointments')
                ->where('appointment_date', $date)
                ->where('status', 'completed');

            if ($branchFilter) {
                $builder->where('branch_id', $branchFilter);
            }
            if ($staffFilter) {
                $builder->where('staff_id', $staffFilter);
            }

            $result = $builder->first();
            
            $weeklyData[] = [
                'date' => $date,
                'day_name' => date('l', strtotime($date)),
                'day_short' => date('D', strtotime($date)),
                'revenue' => $result['daily_revenue'] ?? 0,
                'appointments' => $result['daily_appointments'] ?? 0
            ];
        }

        return $weeklyData;
    }
}