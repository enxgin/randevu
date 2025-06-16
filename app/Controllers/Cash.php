<?php

namespace App\Controllers;

use App\Models\CashMovementModel;
use App\Models\PaymentModel;
use App\Models\BranchModel;

class Cash extends BaseController
{
    protected $cashMovementModel;
    protected $paymentModel;
    protected $branchModel;

    public function __construct()
    {
        $this->cashMovementModel = new CashMovementModel();
        $this->paymentModel = new PaymentModel();
        $this->branchModel = new BranchModel();
    }

    /**
     * Kasa yönetimi ana sayfa
     */
    public function index()
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return redirect()->to('/auth/unauthorized');
        }

        // Admin tüm şubeleri görebilir
        if ($userRole === 'admin' && $this->request->getGet('branch_id')) {
            $branchId = $this->request->getGet('branch_id');
        }

        $date = $this->request->getGet('date') ?: date('Y-m-d');

        // Güncel kasa bakiyesi
        $currentBalance = $this->cashMovementModel->getCurrentBalance($branchId);

        // Günlük hareketler
        $dailyMovements = $this->cashMovementModel->getDailyMovements($branchId, $date);

        // Günlük özet
        $dailySummary = $this->cashMovementModel->getDailySummary($branchId, $date);

        // Kasa durumu kontrolleri
        $isCashOpened = $this->cashMovementModel->isCashOpenedToday($branchId, $date);
        $isCashClosed = $this->cashMovementModel->isCashClosedToday($branchId, $date);

        // Günlük ödeme özeti
        $paymentSummary = $this->paymentModel->getDailyPaymentSummary($branchId, $date);

        $data = [
            'title' => 'Kasa Yönetimi',
            'currentBalance' => $currentBalance,
            'dailyMovements' => $dailyMovements,
            'dailySummary' => $dailySummary,
            'paymentSummary' => $paymentSummary,
            'isCashOpened' => $isCashOpened,
            'isCashClosed' => $isCashClosed,
            'selectedDate' => $date,
            'userRole' => $userRole
        ];

        return view('cash/index', $data);
    }

    /**
     * Kasa açılışı
     */
    public function open()
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return redirect()->to('/auth/unauthorized');
        }

        // Bugün kasa açılmış mı kontrol et
        if ($this->cashMovementModel->isCashOpenedToday($branchId)) {
            session()->setFlashdata('error', 'Kasa bugün zaten açılmış.');
            return redirect()->to('/cash');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'opening_amount' => 'required|numeric|greater_than_equal_to[0]',
                'notes' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $openingAmount = $this->request->getPost('opening_amount');
            $notes = $this->request->getPost('notes') ?: 'Günlük kasa açılışı';

            try {
                $movementData = [
                    'branch_id' => $branchId,
                    'type' => 'opening',
                    'category' => 'daily_opening',
                    'amount' => $openingAmount,
                    'description' => $notes,
                    'processed_by' => $userId
                ];

                $this->cashMovementModel->addMovement($movementData);

                session()->setFlashdata('success', 'Kasa başarıyla açıldı.');
                return redirect()->to('/cash');

            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Kasa açılırken hata oluştu: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Kasa Açılışı'
        ];

        return view('cash/open', $data);
    }

    /**
     * Kasa kapanışı
     */
    public function close()
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return redirect()->to('/auth/unauthorized');
        }

        // Bugün kasa açılmış mı kontrol et
        if (!$this->cashMovementModel->isCashOpenedToday($branchId)) {
            session()->setFlashdata('error', 'Kasa bugün açılmamış. Önce kasa açılışı yapınız.');
            return redirect()->to('/cash');
        }

        // Bugün kasa kapanmış mı kontrol et
        if ($this->cashMovementModel->isCashClosedToday($branchId)) {
            session()->setFlashdata('error', 'Kasa bugün zaten kapanmış.');
            return redirect()->to('/cash');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'actual_amount' => 'required|numeric|greater_than_equal_to[0]',
                'notes' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $actualAmount = $this->request->getPost('actual_amount');
            $notes = $this->request->getPost('notes') ?: 'Günlük kasa kapanışı';

            // Sistem bakiyesi
            $systemBalance = $this->cashMovementModel->getCurrentBalance($branchId);
            $difference = $actualAmount - $systemBalance;

            $db = \Config\Database::connect();
            $db->transStart();

            try {
                // Fark varsa düzeltme hareketi ekle
                if (abs($difference) > 0.01) { // 1 kuruş tolerans
                    $adjustmentData = [
                        'branch_id' => $branchId,
                        'type' => 'adjustment',
                        'category' => 'cash_count_difference',
                        'amount' => $difference,
                        'description' => 'Kasa sayım farkı: ' . ($difference > 0 ? 'Fazla' : 'Eksik') . ' ' . abs($difference) . ' TL',
                        'processed_by' => $userId
                    ];

                    $this->cashMovementModel->addMovement($adjustmentData);
                }

                // Kapanış hareketi
                $closingData = [
                    'branch_id' => $branchId,
                    'type' => 'closing',
                    'category' => 'daily_closing',
                    'amount' => $actualAmount,
                    'description' => $notes . ($difference != 0 ? ' (Fark: ' . $difference . ' TL)' : ''),
                    'processed_by' => $userId
                ];

                $this->cashMovementModel->addMovement($closingData);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('İşlem tamamlanamadı.');
                }

                session()->setFlashdata('success', 'Kasa başarıyla kapandı.' . 
                    ($difference != 0 ? ' Fark: ' . $difference . ' TL' : ''));
                return redirect()->to('/cash');

            } catch (\Exception $e) {
                $db->transRollback();
                session()->setFlashdata('error', 'Kasa kapanırken hata oluştu: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }

        // Günlük özet bilgileri
        $dailySummary = $this->cashMovementModel->getDailySummary($branchId);
        $currentBalance = $this->cashMovementModel->getCurrentBalance($branchId);

        $data = [
            'title' => 'Kasa Kapanışı',
            'dailySummary' => $dailySummary,
            'currentBalance' => $currentBalance
        ];

        return view('cash/close', $data);
    }

    /**
     * Manuel kasa hareketi ekleme
     */
    public function addMovement()
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return redirect()->to('/auth/unauthorized');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'type' => 'required|in_list[income,expense]',
                'category' => 'required|max_length[100]',
                'amount' => 'required|numeric|greater_than[0]',
                'description' => 'required|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            try {
                $movementData = [
                    'branch_id' => $branchId,
                    'type' => $this->request->getPost('type'),
                    'category' => $this->request->getPost('category'),
                    'amount' => $this->request->getPost('amount'),
                    'description' => $this->request->getPost('description'),
                    'processed_by' => $userId
                ];

                $this->cashMovementModel->addMovement($movementData);

                session()->setFlashdata('success', 'Kasa hareketi başarıyla eklendi.');
                return redirect()->to('/cash');

            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Kasa hareketi eklenirken hata oluştu: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }

        // Kategori önerileri
        $categories = [
            'income' => [
                'additional_income' => 'Ek Gelir',
                'capital_injection' => 'Sermaye Ekleme',
                'loan_received' => 'Alınan Borç',
                'other_income' => 'Diğer Gelir'
            ],
            'expense' => [
                'rent' => 'Kira',
                'utilities' => 'Faturalar',
                'supplies' => 'Malzeme',
                'staff_advance' => 'Personel Avansı',
                'maintenance' => 'Bakım-Onarım',
                'marketing' => 'Pazarlama',
                'other_expense' => 'Diğer Gider'
            ]
        ];

        $data = [
            'title' => 'Manuel Kasa Hareketi',
            'categories' => $categories
        ];

        return view('cash/add_movement', $data);
    }

    /**
     * Kasa hareketleri geçmişi
     */
    public function history()
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return redirect()->to('/auth/unauthorized');
        }

        // Admin tüm şubeleri görebilir
        if ($userRole === 'admin' && $this->request->getGet('branch_id')) {
            $branchId = $this->request->getGet('branch_id');
        }

        // Filtreler
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');
        
        $filters = [
            'type' => $this->request->getGet('type'),
            'category' => $this->request->getGet('category'),
            'description_search' => $this->request->getGet('description_search')
        ];

        $movements = $this->cashMovementModel->getMovementsByDateRange($branchId, $startDate, $endDate, $filters);

        $data = [
            'title' => 'Kasa Hareketleri Geçmişi',
            'movements' => $movements,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => $filters,
            'userRole' => $userRole
        ];

        return view('cash/history', $data);
    }

    /**
     * Kasa raporları
     */
    public function reports()
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager'])) {
            return redirect()->to('/auth/unauthorized');
        }

        // Admin tüm şubeleri görebilir
        if ($userRole === 'admin' && $this->request->getGet('branch_id')) {
            $branchId = $this->request->getGet('branch_id');
        }

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');

        // Kategori bazlı harcamalar
        $expensesByCategory = $this->cashMovementModel->getExpensesByCategory($branchId, $startDate, $endDate);

        // Aylık özet
        $monthlySummary = $this->cashMovementModel->getMonthlySummary($branchId);

        // Günlük özet (bugün)
        $todaySummary = $this->cashMovementModel->getDailySummary($branchId, date('Y-m-d'));

        // Güncel bakiye
        $currentBalance = $this->cashMovementModel->getCurrentBalance($branchId);

        $data = [
            'title' => 'Kasa Raporları',
            'expensesByCategory' => $expensesByCategory,
            'monthlySummary' => $monthlySummary,
            'todaySummary' => $todaySummary,
            'currentBalance' => $currentBalance,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userRole' => $userRole
        ];

        return view('cash/reports', $data);
    }

    /**
     * Kasa hareketi silme
     */
    public function deleteMovement($id)
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');

        // Rol kontrolü - Sadece admin ve manager silebilir
        if (!in_array($userRole, ['admin', 'manager'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Yetkiniz bulunmamaktadır.']);
        }

        $movement = $this->cashMovementModel->find($id);
        if (!$movement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hareket bulunamadı.']);
        }

        // Yetki kontrolü
        if ($userRole !== 'admin' && $movement['branch_id'] != $branchId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bu hareketi silme yetkiniz bulunmamaktadır.']);
        }

        // Sistem hareketleri silinemez (opening, closing, payment referanslı)
        if (in_array($movement['type'], ['opening', 'closing']) || $movement['reference_type'] === 'payment') {
            return $this->response->setJSON(['success' => false, 'message' => 'Sistem hareketleri silinemez.']);
        }

        try {
            $this->cashMovementModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Hareket başarıyla silindi.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hareket silinirken hata oluştu.']);
        }
    }
}