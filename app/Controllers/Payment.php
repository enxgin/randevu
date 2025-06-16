<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\CashMovementModel;
use App\Models\CustomerModel;
use App\Models\AppointmentModel;
use App\Models\UserModel;

class Payment extends BaseController
{
    protected $paymentModel;
    protected $cashMovementModel;
    protected $customerModel;
    protected $appointmentModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->cashMovementModel = new CashMovementModel();
        $this->customerModel = new CustomerModel();
        $this->appointmentModel = new AppointmentModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Ödeme listesi
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

        // Filtreler
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'payment_type' => $this->request->getGet('payment_type'),
            'status' => $this->request->getGet('status'),
            'customer_search' => $this->request->getGet('customer_search')
        ];

        $payments = $this->paymentModel->getPaymentsByBranch($branchId, $filters);

        // Ödeme türü istatistikleri
        $paymentStats = $this->paymentModel->getPaymentTypeStats($branchId, $filters['start_date'], $filters['end_date']);

        $data = [
            'title' => 'Ödeme Yönetimi',
            'payments' => $payments,
            'paymentStats' => $paymentStats,
            'filters' => $filters,
            'userRole' => $userRole
        ];

        return view('payments/index', $data);
    }

    /**
     * Ödeme alma formu
     */
    public function create()
    {
        if (strtolower($this->request->getMethod()) === 'get') {
            return view('payments/create_new');
        }
        return $this->store();
    }

    /**
     * Ödeme kaydetme
     */
    public function store()
    {
        // Rol kontrolü
        $userRole = session()->get('role_name');
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Yetkisiz erişim']);
        }

        // Form verilerini al
        $data = [
            'customer_id' => $this->request->getPost('customer_id'),
            'amount' => $this->request->getPost('amount'),
            'payment_type' => $this->request->getPost('payment_type'),
            'appointment_id' => $this->request->getPost('appointment_id'), // Randevu ID'si eklendi
            'notes' => $this->request->getPost('description'),
            'branch_id' => session()->get('branch_id'),
            'processed_by' => session()->get('user_id'),
            'status' => 'completed'
        ];

        // Validasyon
        if (!$this->validate([
            'customer_id' => 'required|numeric',
            'amount' => 'required|numeric|greater_than[0]',
            'payment_type' => 'required|in_list[cash,credit_card,bank_transfer]'
        ])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $this->db->transStart();

            log_message('debug', '[Payment::store] İşlem başladı. Veriler: ' . json_encode($data));

            // Validation kontrolü
            if (!$this->validate([
                'customer_id' => 'required|numeric',
                'amount' => 'required|numeric|greater_than[0]',
                'payment_type' => 'required|in_list[cash,credit_card,bank_transfer]'
            ])) {
                log_message('error', '[Payment::store] Validasyon hatası: ' . json_encode($this->validator->getErrors()));
                throw new \Exception('Validasyon hatası: ' . json_encode($this->validator->getErrors()));
            }

            // Ödeme kaydını oluştur
            $paymentId = $this->paymentModel->insert($data);
            log_message('debug', '[Payment::store] PaymentModel->insert sonucu: ' . json_encode($paymentId));

            if (!$paymentId) {
                $error = $this->paymentModel->errors();
                log_message('error', '[Payment::store] Ödeme kaydı oluşturulamadı. Model hataları: ' . json_encode($error));
                throw new \Exception('Ödeme kaydı oluşturulamadı: ' . json_encode($error));
            }

            // Son kasa bakiyesini al
            $lastMovement = $this->cashMovementModel
                ->where('branch_id', $data['branch_id'])
                ->orderBy('id', 'DESC')
                ->first();
            
            $currentBalance = $lastMovement ? ($lastMovement['balance_after'] ?? 0) : 0;
            
            // Kasa hareketi oluştur
            $cashMovement = [
                'type' => 'income',
                'amount' => $data['amount'],
                'description' => 'Müşteri ödemesi - #' . $paymentId,
                'reference_type' => 'payment',
                'reference_id' => $paymentId,
                'branch_id' => $data['branch_id'],
                'processed_by' => $data['processed_by'],
                'balance_before' => $currentBalance,
                'balance_after' => $currentBalance + $data['amount']
            ];
            
            log_message('debug', '[Payment::store] Kasa hareketi oluşturuluyor: ' . json_encode($cashMovement));
            
            if (!$this->cashMovementModel->insert($cashMovement)) {
                $error = $this->cashMovementModel->errors();
                log_message('error', '[Payment::store] Kasa hareketi oluşturulamadı. Model hataları: ' . json_encode($error));
                throw new \Exception('Kasa hareketi oluşturulamadı: ' . json_encode($error));
            }

            // Eğer randevu ID'si varsa, randevunun ödeme durumunu güncelle
            if (!empty($data['appointment_id'])) {
                $appointment = $this->appointmentModel->find($data['appointment_id']);
                if ($appointment) {
                    // Randevunun toplam tutarını ve ödenmiş tutarını kontrol et
                    $currentPaidAmount = (float)$appointment['paid_amount'];
                    $totalPrice = (float)$appointment['price'];
                    $newPaidAmount = $currentPaidAmount + (float)$data['amount'];
                    
                    $updateData = [
                        'paid_amount' => $newPaidAmount,
                        'payment_status' => ($newPaidAmount >= $totalPrice) ? 'paid' : 'partial'
                    ];
                    
                    if (!$this->appointmentModel->update($data['appointment_id'], $updateData)) {
                        log_message('error', '[Payment::store] Randevu güncellenemedi: ' . json_encode($this->appointmentModel->errors()));
                        throw new \Exception('Randevu güncellenemedi');
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                log_message('error', '[Payment::store] Transaction başarısız');
                throw new \Exception('Veritabanı işlemi başarısız');
            }

            log_message('info', '[Payment::store] İşlem başarıyla tamamlandı. payment_id: ' . $paymentId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Ödeme başarıyla kaydedildi',
                'payment_id' => $paymentId
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Payment::store] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ödeme kaydedilirken bir hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ödeme detayları
     */
    public function show($id)
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return redirect()->to('/auth/unauthorized');
        }

        $payment = $this->paymentModel->select('payments.*, customers.first_name, customers.last_name, customers.phone,
                                              users.first_name as processed_by_name, users.last_name as processed_by_surname,
                                              appointments.start_time as appointment_date, services.name as service_name')
                                     ->join('customers', 'customers.id = payments.customer_id')
                                     ->join('users', 'users.id = payments.processed_by')
                                     ->join('appointments', 'appointments.id = payments.appointment_id', 'left')
                                     ->join('services', 'services.id = appointments.service_id', 'left')
                                     ->find($id);

        if (!$payment) {
            session()->setFlashdata('error', 'Ödeme bulunamadı.');
            return redirect()->to('/payments');
        }

        // Yetki kontrolü
        if ($userRole !== 'admin' && $payment['branch_id'] != $branchId) {
            return redirect()->to('/auth/unauthorized');
        }

        // Parçalı ödemeler (aynı randevu için)
        $partialPayments = [];
        if ($payment['appointment_id']) {
            $partialPayments = $this->paymentModel->getPartialPayments($payment['appointment_id']);
        }

        $data = [
            'title' => 'Ödeme Detayları',
            'payment' => $payment,
            'partialPayments' => $partialPayments,
            'userRole' => $userRole
        ];

        return view('payments/show', $data);
    }

    /**
     * İade işlemi
     */
    public function refund($id)
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager'])) {
            return redirect()->to('/auth/unauthorized');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'refund_amount' => 'required|numeric|greater_than[0]',
                'refund_reason' => 'required|max_length[255]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $payment = $this->paymentModel->find($id);
            if (!$payment) {
                session()->setFlashdata('error', 'Ödeme bulunamadı.');
                return redirect()->to('/payments');
            }

            // Yetki kontrolü
            if ($userRole !== 'admin' && $payment['branch_id'] != $branchId) {
                return redirect()->to('/auth/unauthorized');
            }

            $refundAmount = $this->request->getPost('refund_amount');
            $refundReason = $this->request->getPost('refund_reason');

            // İade tutarı kontrolü
            if ($refundAmount > $payment['amount']) {
                session()->setFlashdata('error', 'İade tutarı ödeme tutarından fazla olamaz.');
                return redirect()->back()->withInput();
            }

            $db = \Config\Database::connect();
            $db->transStart();

            try {
                // İade işlemini kaydet
                $this->paymentModel->processRefund($id, $refundAmount, $refundReason, $userId);

                // Nakit ödeme ise kasa hareketine ekle
                if ($payment['payment_type'] === 'cash') {
                    $this->cashMovementModel->addMovement([
                        'branch_id' => $payment['branch_id'],
                        'type' => 'expense',
                        'category' => 'refund',
                        'amount' => $refundAmount,
                        'description' => 'Ödeme iadesi - ' . $refundReason,
                        'reference_type' => 'payment',
                        'reference_id' => $id,
                        'processed_by' => $userId
                    ]);
                }

                // Randevu varsa ödeme durumunu güncelle
                if ($payment['appointment_id']) {
                    $appointment = $this->appointmentModel->find($payment['appointment_id']);
                    if ($appointment) {
                        $totalPaid = $this->paymentModel->getTotalPaidForAppointment($payment['appointment_id']);
                        
                        $updateData = ['paid_amount' => $totalPaid];
                        
                        if ($totalPaid >= $appointment['price']) {
                            $updateData['payment_status'] = 'paid';
                        } elseif ($totalPaid > 0) {
                            $updateData['payment_status'] = 'partial';
                        } else {
                            $updateData['payment_status'] = 'pending';
                        }
                        
                        $this->appointmentModel->update($payment['appointment_id'], $updateData);
                    }
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('İşlem tamamlanamadı.');
                }

                session()->setFlashdata('success', 'İade işlemi başarıyla tamamlandı.');
                return redirect()->to('/payments/show/' . $id);

            } catch (\Exception $e) {
                $db->transRollback();
                session()->setFlashdata('error', 'İade işlemi sırasında hata oluştu: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }

        // GET isteği - İade formu
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            session()->setFlashdata('error', 'Ödeme bulunamadı.');
            return redirect()->to('/payments');
        }

        // Yetki kontrolü
        if ($userRole !== 'admin' && $payment['branch_id'] != $branchId) {
            return redirect()->to('/auth/unauthorized');
        }

        $data = [
            'title' => 'Ödeme İadesi',
            'payment' => $payment
        ];

        return view('payments/refund', $data);
    }

    /**
     * Borçlu müşteriler
     */
    public function debtors()
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

        $debtors = $this->paymentModel->getDebtorCustomers($branchId);

        $data = [
            'title' => 'Borçlu Müşteriler',
            'debtors' => $debtors,
            'userRole' => $userRole
        ];

        return view('payments/debtors', $data);
    }

    /**
     * Kredi bakiyesi olan müşteriler
     */
    public function credits()
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

        $creditCustomers = $this->customerModel->getCreditCustomers($branchId);

        $data = [
            'title' => 'Kredi Bakiyesi Olan Müşteriler',
            'creditCustomers' => $creditCustomers,
            'userRole' => $userRole
        ];

        return view('payments/credits', $data);
    }

    /**
     * Ödeme raporları
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

        // Ödeme türü istatistikleri
        $paymentStats = $this->paymentModel->getPaymentTypeStats($branchId, $startDate, $endDate);

        // Günlük ödeme özeti
        $dailySummary = $this->paymentModel->getDailyPaymentSummary($branchId, date('Y-m-d'));

        // Aylık istatistikler
        $monthlyStats = $this->paymentModel->getMonthlyPaymentStats($branchId);

        $data = [
            'title' => 'Ödeme Raporları',
            'paymentStats' => $paymentStats,
            'dailySummary' => $dailySummary,
            'monthlyStats' => $monthlyStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userRole' => $userRole
        ];

        return view('payments/reports', $data);
    }

    /**
     * AJAX müşteri arama
     */
    public function searchCustomers()
    {
        $userRole = session()->get('role_name');
        $branchId = session()->get('branch_id');

        // Rol kontrolü
        if (!in_array($userRole, ['admin', 'manager', 'receptionist'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Yetkisiz erişim']);
        }

        $name = $this->request->getGet('name');
        $phone = $this->request->getGet('phone');
        
        // En az bir arama kriteri olmalı ve minimum 2 karakter
        if ((empty($name) || strlen($name) < 2) && (empty($phone) || strlen($phone) < 2)) {
            return $this->response->setJSON(['success' => true, 'customers' => []]);
        }

        $customerQuery = $this->customerModel->select('id, first_name, last_name, phone, email')
                                           ->where('is_active', 1);

        // Rol bazlı filtreleme
        if ($userRole !== 'admin') {
            $customerQuery->where('branch_id', $branchId);
        }

        // Arama filtresi
        $customerQuery->groupStart();
        
        if (!empty($name) && strlen($name) >= 2) {
            $customerQuery->like('first_name', $name)
                         ->orLike('last_name', $name);
        }
        
        if (!empty($phone) && strlen($phone) >= 2) {
            if (!empty($name) && strlen($name) >= 2) {
                $customerQuery->orLike('phone', $phone);
            } else {
                $customerQuery->like('phone', $phone);
            }
        }
        
        $customerQuery->groupEnd();

        $customers = $customerQuery->orderBy('first_name', 'ASC')
                                  ->limit(10)
                                  ->findAll();

        return $this->response->setJSON(['success' => true, 'customers' => $customers]);
    }
}