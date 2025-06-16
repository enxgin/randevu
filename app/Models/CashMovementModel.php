<?php

namespace App\Models;

use CodeIgniter\Model;

class CashMovementModel extends Model
{
    protected $table = 'cash_movements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'type',
        'category',
        'amount',
        'description',
        'reference_type',
        'reference_id',
        'balance_before',
        'balance_after',
        'processed_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'amount' => 'float',
        'balance_before' => '?float',
        'balance_after' => '?float'
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
        'type' => 'required|in_list[opening,closing,income,expense,adjustment]',
        'amount' => 'required|numeric',
        'description' => 'required|max_length[500]',
        'balance_before' => 'required|numeric',
        'balance_after' => 'required|numeric',
        'processed_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required' => 'Şube seçimi zorunludur.',
            'integer' => 'Geçerli bir şube seçiniz.'
        ],
        'type' => [
            'required' => 'Hareket türü seçimi zorunludur.',
            'in_list' => 'Geçerli bir hareket türü seçiniz.'
        ],
        'amount' => [
            'required' => 'Tutar zorunludur.',
            'numeric' => 'Geçerli bir tutar giriniz.'
        ],
        'description' => [
            'required' => 'Açıklama zorunludur.',
            'max_length' => 'Açıklama en fazla 500 karakter olabilir.'
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
     * Güncel kasa bakiyesini getir
     */
    public function getCurrentBalance($branchId)
    {
        $lastMovement = $this->where('branch_id', $branchId)
                             ->orderBy('created_at', 'DESC')
                             ->orderBy('id', 'DESC')
                             ->first();

        return $lastMovement ? (float)$lastMovement['balance_after'] : 0.00;
    }

    /**
     * Kasa hareketi ekle ve bakiyeyi güncelle
     */
    public function addMovement($data)
    {
        // Mevcut bakiyeyi al
        $currentBalance = $this->getCurrentBalance($data['branch_id']);
        
        // Yeni bakiyeyi hesapla
        $newBalance = $currentBalance;
        switch ($data['type']) {
            case 'opening':
                $newBalance = $data['amount']; // Açılış bakiyesi direkt set edilir
                break;
            case 'income':
                $newBalance += $data['amount'];
                break;
            case 'expense':
                $newBalance -= $data['amount'];
                break;
            case 'adjustment':
                $newBalance = $data['amount']; // Düzeltme direkt set edilir
                break;
            case 'closing':
                // Kapanış için bakiye değişmez, sadece kayıt tutulur
                break;
        }

        // Hareket verilerini hazırla
        $movementData = array_merge($data, [
            'balance_before' => $currentBalance,
            'balance_after' => $newBalance
        ]);

        return $this->insert($movementData);
    }

    /**
     * Günlük kasa hareketlerini getir
     */
    public function getDailyMovements($branchId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        return $this->select('cash_movements.*, users.first_name, users.last_name')
                    ->join('users', 'users.id = cash_movements.processed_by')
                    ->where('cash_movements.branch_id', $branchId)
                    ->where('DATE(cash_movements.created_at)', $date)
                    ->orderBy('cash_movements.created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Tarih aralığında kasa hareketlerini getir
     */
    public function getMovementsByDateRange($branchId, $startDate, $endDate, $filters = [])
    {
        $builder = $this->select('cash_movements.*, users.first_name, users.last_name')
                        ->join('users', 'users.id = cash_movements.processed_by')
                        ->where('cash_movements.branch_id', $branchId)
                        ->where('DATE(cash_movements.created_at) >=', $startDate)
                        ->where('DATE(cash_movements.created_at) <=', $endDate);

        // Hareket türü filtresi
        if (!empty($filters['type'])) {
            $builder->where('cash_movements.type', $filters['type']);
        }

        // Kategori filtresi
        if (!empty($filters['category'])) {
            $builder->where('cash_movements.category', $filters['category']);
        }

        // Açıklama arama
        if (!empty($filters['description_search'])) {
            $builder->like('cash_movements.description', $filters['description_search']);
        }

        return $builder->orderBy('cash_movements.created_at', 'DESC')->findAll();
    }

    /**
     * Günlük kasa özeti
     */
    public function getDailySummary($branchId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $movements = $this->where('branch_id', $branchId)
                          ->where('DATE(created_at)', $date)
                          ->findAll();

        $summary = [
            'opening_balance' => 0,
            'closing_balance' => 0,
            'total_income' => 0,
            'total_expense' => 0,
            'total_adjustments' => 0,
            'net_change' => 0,
            'movement_count' => count($movements)
        ];

        foreach ($movements as $movement) {
            switch ($movement['type']) {
                case 'opening':
                    $summary['opening_balance'] = (float)$movement['amount'];
                    break;
                case 'closing':
                    $summary['closing_balance'] = (float)$movement['balance_after'];
                    break;
                case 'income':
                    $summary['total_income'] += (float)$movement['amount'];
                    break;
                case 'expense':
                    $summary['total_expense'] += (float)$movement['amount'];
                    break;
                case 'adjustment':
                    $summary['total_adjustments'] += (float)$movement['amount'];
                    break;
            }
        }

        $summary['net_change'] = $summary['total_income'] - $summary['total_expense'];

        return $summary;
    }

    /**
     * Aylık kasa istatistikleri
     */
    public function getMonthlySummary($branchId, $year = null, $month = null)
    {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');

        return $this->select('
                        DAY(created_at) as day,
                        SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as daily_income,
                        SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as daily_expense,
                        COUNT(*) as movement_count
                    ')
                    ->where('branch_id', $branchId)
                    ->where('YEAR(created_at)', $year)
                    ->where('MONTH(created_at)', $month)
                    ->whereIn('type', ['income', 'expense'])
                    ->groupBy('DAY(created_at)')
                    ->orderBy('day', 'ASC')
                    ->findAll();
    }

    /**
     * Kasa açılış kontrolü
     */
    public function isCashOpenedToday($branchId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $opening = $this->where('branch_id', $branchId)
                        ->where('type', 'opening')
                        ->where('DATE(created_at)', $date)
                        ->first();

        return $opening !== null;
    }

    /**
     * Kasa kapanış kontrolü
     */
    public function isCashClosedToday($branchId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $closing = $this->where('branch_id', $branchId)
                        ->where('type', 'closing')
                        ->where('DATE(created_at)', $date)
                        ->first();

        return $closing !== null;
    }

    /**
     * Kategori bazlı harcama raporu
     */
    public function getExpensesByCategory($branchId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('category, SUM(amount) as total_amount, COUNT(*) as count')
                        ->where('branch_id', $branchId)
                        ->where('type', 'expense')
                        ->where('category IS NOT NULL');

        if ($startDate) {
            $builder->where('DATE(created_at) >=', $startDate);
        }
        if ($endDate) {
            $builder->where('DATE(created_at) <=', $endDate);
        }

        return $builder->groupBy('category')
                      ->orderBy('total_amount', 'DESC')
                      ->findAll();
    }

    /**
     * Ödeme referanslı kasa hareketi ekle
     */
    public function addPaymentMovement($branchId, $paymentId, $amount, $paymentType, $processedBy)
    {
        $description = "Ödeme alındı - " . $this->getPaymentTypeText($paymentType);
        
        return $this->addMovement([
            'branch_id' => $branchId,
            'type' => 'income',
            'category' => 'payment',
            'amount' => $amount,
            'description' => $description,
            'reference_type' => 'payment',
            'reference_id' => $paymentId,
            'processed_by' => $processedBy
        ]);
    }

    /**
     * Ödeme türü metni
     */
    private function getPaymentTypeText($paymentType)
    {
        $types = [
            'cash' => 'Nakit',
            'credit_card' => 'Kredi Kartı',
            'bank_transfer' => 'Havale/EFT',
            'gift_card' => 'Hediye Çeki',
            'package' => 'Paket Kullanımı'
        ];

        return $types[$paymentType] ?? $paymentType;
    }

    /**
     * Günlük kasa hareketlerini getir (Reports için)
     */
    public function getDailyCashMovements($branchId, $date)
    {
        return $this->select('cash_movements.*, users.first_name, users.last_name')
                    ->join('users', 'users.id = cash_movements.processed_by')
                    ->where('cash_movements.branch_id', $branchId)
                    ->where('DATE(cash_movements.created_at)', $date)
                    ->orderBy('cash_movements.created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Günlük kasa özeti (Reports için)
     */
    public function getDailyCashSummary($branchId, $date)
    {
        return $this->select('
                        SUM(CASE WHEN type = "opening" THEN amount ELSE 0 END) as opening_balance,
                        SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                        SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense,
                        SUM(CASE WHEN type = "closing" THEN balance_after ELSE 0 END) as closing_balance,
                        COUNT(*) as total_movements
                    ')
                    ->where('branch_id', $branchId)
                    ->where('DATE(created_at)', $date)
                    ->first();
    }

    /**
     * Kasa geçmişi (Reports için)
     */
    public function getCashHistory($branchId, $startDate, $endDate, $type = null)
    {
        $builder = $this->select('cash_movements.*, users.first_name, users.last_name')
                        ->join('users', 'users.id = cash_movements.processed_by')
                        ->where('cash_movements.branch_id', $branchId)
                        ->where('DATE(cash_movements.created_at) >=', $startDate)
                        ->where('DATE(cash_movements.created_at) <=', $endDate);

        if ($type) {
            $builder->where('cash_movements.type', $type);
        }

        return $builder->orderBy('cash_movements.created_at', 'DESC')->findAll();
    }

    /**
     * Kasa geçmişi özeti (Reports için)
     */
    public function getCashHistorySummary($branchId, $startDate, $endDate)
    {
        return $this->select('
                        SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                        SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense,
                        COUNT(CASE WHEN type = "income" THEN 1 END) as income_count,
                        COUNT(CASE WHEN type = "expense" THEN 1 END) as expense_count,
                        COUNT(*) as total_movements
                    ')
                    ->where('branch_id', $branchId)
                    ->where('DATE(created_at) >=', $startDate)
                    ->where('DATE(created_at) <=', $endDate)
                    ->first();
    }
}