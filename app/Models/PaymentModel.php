<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'customer_id', 
        'appointment_id',
        'amount',
        'payment_type',
        'payment_method_details',
        'transaction_id',
        'status',
        'refund_amount',
        'refund_reason',
        'description',
        'notes',
        'created_by',
        'processed_by',
        'refunded_by',
        'refunded_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'payment_method_details' => '?json',
        'amount' => 'float',
        'refund_amount' => '?float'
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
        'branch_id' => 'required|integer',
        'customer_id' => 'required|integer',
        'amount' => 'required|numeric|greater_than[0]',
        'payment_type' => 'required|in_list[cash,credit_card,bank_transfer,gift_card,package]',
        'status' => 'in_list[pending,completed,refunded,cancelled]',
        'processed_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required' => 'Şube seçimi zorunludur.',
            'integer' => 'Geçerli bir şube seçiniz.'
        ],
        'customer_id' => [
            'required' => 'Müşteri seçimi zorunludur.',
            'integer' => 'Geçerli bir müşteri seçiniz.'
        ],
        'amount' => [
            'required' => 'Ödeme tutarı zorunludur.',
            'numeric' => 'Geçerli bir tutar giriniz.',
            'greater_than' => 'Ödeme tutarı 0\'dan büyük olmalıdır.'
        ],
        'payment_type' => [
            'required' => 'Ödeme türü seçimi zorunludur.',
            'in_list' => 'Geçerli bir ödeme türü seçiniz.'
        ],
        'processed_by' => [
            'required' => 'İşlemi yapan kullanıcı bilgisi zorunludur.',
            'integer' => 'Geçerli bir kullanıcı seçiniz.'
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
     * Şube bazlı ödemeleri getir
     */
    public function getPaymentsByBranch($branchId, $filters = [])
    {
        $builder = $this->select('payments.*, customers.first_name, customers.last_name, customers.phone, 
                                 users.first_name as processed_by_name, users.last_name as processed_by_surname,
                                 appointments.start_time as appointment_date')
                        ->join('customers', 'customers.id = payments.customer_id')
                        ->join('users', 'users.id = payments.processed_by')
                        ->join('appointments', 'appointments.id = payments.appointment_id', 'left')
                        ->where('payments.branch_id', $branchId);

        // Tarih filtresi
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(payments.created_at) >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(payments.created_at) <=', $filters['end_date']);
        }

        // Ödeme türü filtresi
        if (!empty($filters['payment_type'])) {
            $builder->where('payments.payment_type', $filters['payment_type']);
        }

        // Durum filtresi
        if (!empty($filters['status'])) {
            $builder->where('payments.status', $filters['status']);
        }

        // Müşteri arama
        if (!empty($filters['customer_search'])) {
            $builder->groupStart()
                   ->like('customers.first_name', $filters['customer_search'])
                   ->orLike('customers.last_name', $filters['customer_search'])
                   ->orLike('customers.phone', $filters['customer_search'])
                   ->groupEnd();
        }

        return $builder->orderBy('payments.created_at', 'DESC')->findAll();
    }

    /**
     * Müşteri ödemelerini getir
     */
    public function getCustomerPayments($customerId)
    {
        return $this->select('payments.*, users.first_name as processed_by_name, users.last_name as processed_by_surname,
                             appointments.start_time as appointment_date, services.name as service_name')
                    ->join('users', 'users.id = payments.processed_by')
                    ->join('appointments', 'appointments.id = payments.appointment_id', 'left')
                    ->join('services', 'services.id = appointments.service_id', 'left')
                    ->where('payments.customer_id', $customerId)
                    ->orderBy('payments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Günlük ödeme özeti
     */
    public function getDailyPaymentSummary($branchId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $builder = $this->select('payment_type, status, COUNT(*) as count, SUM(amount) as total_amount')
                        ->where('branch_id', $branchId)
                        ->where('DATE(created_at)', $date)
                        ->groupBy(['payment_type', 'status']);

        return $builder->findAll();
    }

    /**
     * Aylık ödeme istatistikleri
     */
    public function getMonthlyPaymentStats($branchId, $year = null, $month = null)
    {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');

        return $this->select('DAY(created_at) as day, COUNT(*) as count, SUM(amount) as total_amount')
                    ->where('branch_id', $branchId)
                    ->where('YEAR(created_at)', $year)
                    ->where('MONTH(created_at)', $month)
                    ->where('status', 'completed')
                    ->groupBy('DAY(created_at)')
                    ->orderBy('day', 'ASC')
                    ->findAll();
    }

    /**
     * Borçlu müşteriler listesi
     */
    public function getDebtorCustomers($branchId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT
                c.id,
                c.first_name,
                c.last_name,
                c.phone,
                c.email,
                COALESCE(SUM(CASE WHEN a.payment_status IN ('pending', 'partial') THEN (a.price - a.paid_amount) ELSE 0 END), 0) as total_debt,
                COUNT(CASE WHEN a.payment_status IN ('pending', 'partial') THEN 1 END) as unpaid_appointments
            FROM customers c
            LEFT JOIN appointments a ON c.id = a.customer_id AND a.branch_id = ?
            WHERE c.branch_id = ?
            GROUP BY c.id, c.first_name, c.last_name, c.phone, c.email
            HAVING total_debt > 0
            ORDER BY total_debt DESC
        ", [$branchId, $branchId]);

        return $query->getResultArray();
    }

    /**
     * Ödeme türü istatistikleri
     */
    public function getPaymentTypeStats($branchId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('payment_type, COUNT(*) as count, SUM(amount) as total_amount')
                        ->where('branch_id', $branchId)
                        ->where('status', 'completed');

        if ($startDate) {
            $builder->where('DATE(created_at) >=', $startDate);
        }
        if ($endDate) {
            $builder->where('DATE(created_at) <=', $endDate);
        }

        return $builder->groupBy('payment_type')
                      ->orderBy('total_amount', 'DESC')
                      ->findAll();
    }

    /**
     * İade işlemi
     */
    public function processRefund($paymentId, $refundAmount, $reason, $refundedBy)
    {
        $payment = $this->find($paymentId);
        if (!$payment) {
            return false;
        }

        $updateData = [
            'status' => 'refunded',
            'refund_amount' => $refundAmount,
            'refund_reason' => $reason,
            'refunded_by' => $refundedBy,
            'refunded_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($paymentId, $updateData);
    }

    /**
     * Parçalı ödeme kayıtları
     */
    public function getPartialPayments($appointmentId)
    {
        return $this->select('payments.*, users.first_name as processed_by_name, users.last_name as processed_by_surname')
                    ->join('users', 'users.id = payments.processed_by')
                    ->where('payments.appointment_id', $appointmentId)
                    ->orderBy('payments.created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Toplam ödenen tutar (randevu bazlı)
     */
    public function getTotalPaidForAppointment($appointmentId)
    {
        $result = $this->select('SUM(CASE
                                    WHEN status = "completed" THEN amount
                                    WHEN status = "refunded" THEN (amount - COALESCE(refund_amount, 0))
                                    ELSE 0
                                END) as total_paid')
                       ->where('appointment_id', $appointmentId)
                       ->first();

        return $result ? (float)$result['total_paid'] : 0;
    }

    /**
     * Günlük ödemeleri getir
     */
    public function getDailyPayments($branchId, $date)
    {
        return $this->select('payments.*, customers.first_name, customers.last_name, customers.phone,
                             users.first_name as processed_by_name, users.last_name as processed_by_surname,
                             appointments.start_time as appointment_date, services.name as service_name')
                    ->join('customers', 'customers.id = payments.customer_id')
                    ->join('users', 'users.id = payments.processed_by')
                    ->join('appointments', 'appointments.id = payments.appointment_id', 'left')
                    ->join('services', 'services.id = appointments.service_id', 'left')
                    ->where('payments.branch_id', $branchId)
                    ->where('DATE(payments.created_at)', $date)
                    ->orderBy('payments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ödeme geçmişi
     */
    public function getPaymentHistory($branchId, $startDate, $endDate)
    {
        return $this->select('payments.*, customers.first_name, customers.last_name, customers.phone,
                             users.first_name as processed_by_name, users.last_name as processed_by_surname,
                             appointments.start_time as appointment_date, services.name as service_name')
                    ->join('customers', 'customers.id = payments.customer_id')
                    ->join('users', 'users.id = payments.processed_by')
                    ->join('appointments', 'appointments.id = payments.appointment_id', 'left')
                    ->join('services', 'services.id = appointments.service_id', 'left')
                    ->where('payments.branch_id', $branchId)
                    ->where('DATE(payments.created_at) >=', $startDate)
                    ->where('DATE(payments.created_at) <=', $endDate)
                    ->orderBy('payments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ödeme özeti
     */
    public function getPaymentSummary($branchId, $startDate, $endDate)
    {
        return $this->select('
                        COUNT(*) as total_payments,
                        SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_amount,
                        SUM(CASE WHEN payment_type = "cash" AND status = "completed" THEN amount ELSE 0 END) as cash_amount,
                        SUM(CASE WHEN payment_type = "credit_card" AND status = "completed" THEN amount ELSE 0 END) as card_amount,
                        SUM(CASE WHEN payment_type = "bank_transfer" AND status = "completed" THEN amount ELSE 0 END) as transfer_amount,
                        SUM(CASE WHEN status = "refunded" THEN refund_amount ELSE 0 END) as total_refunds
                    ')
                    ->where('branch_id', $branchId)
                    ->where('DATE(created_at) >=', $startDate)
                    ->where('DATE(created_at) <=', $endDate)
                    ->first();
    }
}