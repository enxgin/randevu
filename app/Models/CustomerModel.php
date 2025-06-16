<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Müşteri silindiğinde veritabanından gerçekten silmek yerine deleted_at alanı dolsun.
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'birth_date',
        'notes', // Müşteriye özel notlar için
        'tags',  // Etiketleme sistemi için (JSON veya ayrı tablo düşünülebilir, şimdilik text)
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = []; // Dinamik olarak ayarlanacak
    protected $validationMessages = [
        'phone' => [
            'is_unique' => 'Bu telefon numarası zaten kayıtlı.',
        ],
        'email' => [
            'is_unique' => 'Bu e-posta adresi zaten kayıtlı.',
        ],
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Casts özelliğini kaldırıyoruz, manuel getter/setter kullanacağız.
    // protected array $casts = [
    //     'tags' => '?json-array',
    // ];

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setValidationRulesForInsert'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['setValidationRulesForUpdate'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = ['processTagsAfterFind']; // afterFind callback'ini ekledik
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Veritabanından 'tags' alanı okunduktan sonra JSON string'ini PHP dizisine çevirir.
     */
    protected function processTagsAfterFind(array $eventData): array
    {
        // $eventData['data'] tek bir kayıt (find() sonucu) veya kayıt dizisi (findAll() sonucu) olabilir.
        // $eventData['singleton'] bu durumu ayırt etmemize yardımcı olur.
        
        if (empty($eventData['data'])) {
            return $eventData;
        }

        if ($eventData['singleton']) { // Tek bir kayıt bulundu (find() metodu)
            if (isset($eventData['data']['tags']) && is_string($eventData['data']['tags'])) {
                $decoded_tags = json_decode($eventData['data']['tags'], true);
                $eventData['data']['tags'] = (json_last_error() === JSON_ERROR_NONE && is_array($decoded_tags)) ? $decoded_tags : [];
            } elseif (!isset($eventData['data']['tags']) || $eventData['data']['tags'] === null) {
                $eventData['data']['tags'] = []; // tags alanı yoksa veya null ise boş array ata
            }
        } else { // Birden fazla kayıt bulundu (findAll() metodu)
            if (is_array($eventData['data'])) { // Ekstra kontrol: $eventData['data'] gerçekten bir dizi mi?
                foreach ($eventData['data'] as $key => $row) {
                    if (is_array($row)) { // Ekstra kontrol: $row gerçekten bir dizi mi?
                        if (isset($row['tags']) && is_string($row['tags'])) {
                            $decoded_tags = json_decode($row['tags'], true);
                            $eventData['data'][$key]['tags'] = (json_last_error() === JSON_ERROR_NONE && is_array($decoded_tags)) ? $decoded_tags : [];
                        } elseif (!isset($row['tags']) || $row['tags'] === null) {
                            $eventData['data'][$key]['tags'] = []; // tags alanı yoksa veya null ise boş array ata
                        }
                    }
                }
            }
        }
        
        return $eventData;
    }

    protected function setValidationRulesForInsert(array $eventData): array
    {
        $this->validationRules = [
            'branch_id'  => 'required|integer',
            'first_name' => 'required|string|min_length[2]|max_length[100]',
            'last_name'  => 'required|string|min_length[2]|max_length[100]',
            'phone'      => 'required|string|max_length[20]|is_unique[customers.phone,phone,NULL,deleted_at]',
            'email'      => 'permit_empty|valid_email|max_length[100]|is_unique[customers.email,email,NULL,deleted_at]',
            'birth_date' => 'permit_empty|valid_date',
            // 'tags' alanı için validasyon eklenebilir, örn: 'permit_empty|string'
        ];
        return $eventData; // Callback'ler $eventData'yı döndürmeli
    }

    protected function setValidationRulesForUpdate(array $eventData): array
    {
        $id = $eventData['id'][0] ?? $eventData['id'] ?? null; // update() birden fazla ID alabilir, ilkini alalım.
                                                              // Veya $eventData['id'] doğrudan tek bir değer olabilir.

        if (!$id && isset($eventData['data'][$this->primaryKey])) { // Eğer $id yoksa ama $data içinde primaryKey varsa onu kullan
            $id = $eventData['data'][$this->primaryKey];
        }
        
        // Eğer $id hala null ise, bir sorun var demektir, ama devam edelim.
        // is_unique kuralı ID olmadan çalışmayacaktır.
        // Bu durumun loglanması iyi olabilir.
        if ($id === null) {
            log_message('error', 'CustomerModel: setValidationRulesForUpdate içinde ID bulunamadı. EventData: ' . json_encode($eventData));
        }

        $this->validationRules = [
            'branch_id'  => 'required|integer',
            'first_name' => 'required|string|min_length[2]|max_length[100]',
            'last_name'  => 'required|string|min_length[2]|max_length[100]',
            'phone'      => $id ? "required|string|max_length[20]|is_unique[customers.phone,phone,{$id},deleted_at]" : 'required|string|max_length[20]|is_unique[customers.phone,phone,NULL,deleted_at]',
            'email'      => $id ? "permit_empty|valid_email|max_length[100]|is_unique[customers.email,email,{$id},deleted_at]" : 'permit_empty|valid_email|max_length[100]|is_unique[customers.email,email,NULL,deleted_at]',
            'birth_date' => 'permit_empty|valid_date',
            // 'tags' alanı için validasyon eklenebilir
        ];
        return $eventData; // Callback'ler $eventData'yı döndürmeli
    }

    /**
     * Aktif müşterileri getir
     */
    public function getActiveCustomers($branchId = null)
    {
        $builder = $this->select('id, first_name, last_name, phone, email')
                        ->where('deleted_at', null);

        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        return $builder->orderBy('first_name, last_name')->findAll();
    }

    /**
     * Müşteri arama (AJAX için)
     */
    public function searchCustomers($query, $branchId = null)
    {
        $builder = $this->select('id, first_name, last_name, phone, email')
            ->where('deleted_at', null);

        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        $builder->groupStart()
            ->like('first_name', $query)
            ->orLike('last_name', $query)
            ->orLike('phone', $query)
            ->orLike('email', $query)
            ->groupEnd();

        return $builder->limit(10)->findAll();
    }

    /**
     * Borçlu müşterileri getir
     */
    public function getDebtCustomers($branchId)
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
            WHERE c.branch_id = ? AND c.deleted_at IS NULL
            GROUP BY c.id, c.first_name, c.last_name, c.phone, c.email
            HAVING total_debt > 0
            ORDER BY total_debt DESC
        ", [$branchId, $branchId]);

        return $query->getResultArray();
    }

    /**
     * Detaylı borçlu müşterileri getir
     */
    public function getDebtCustomersDetailed($branchId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT
                c.id,
                c.first_name,
                c.last_name,
                c.phone,
                c.email,
                c.created_at as customer_since,
                COALESCE(SUM(CASE WHEN a.payment_status IN ('pending', 'partial') THEN (a.price - a.paid_amount) ELSE 0 END), 0) as total_debt,
                COUNT(CASE WHEN a.payment_status IN ('pending', 'partial') THEN 1 END) as unpaid_appointments,
                COUNT(a.id) as total_appointments,
                MAX(a.appointment_date) as last_appointment_date
            FROM customers c
            LEFT JOIN appointments a ON c.id = a.customer_id AND a.branch_id = ?
            WHERE c.branch_id = ? AND c.deleted_at IS NULL
            GROUP BY c.id, c.first_name, c.last_name, c.phone, c.email, c.created_at
            HAVING total_debt > 0
            ORDER BY total_debt DESC
        ", [$branchId, $branchId]);

        return $query->getResultArray();
    }

    /**
     * Borç özeti
     */
    public function getDebtSummary($branchId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT
                COUNT(DISTINCT c.id) as total_debt_customers,
                COALESCE(SUM(CASE WHEN a.payment_status IN ('pending', 'partial') THEN (a.price - a.paid_amount) ELSE 0 END), 0) as total_debt_amount,
                COUNT(CASE WHEN a.payment_status IN ('pending', 'partial') THEN 1 END) as total_unpaid_appointments
            FROM customers c
            LEFT JOIN appointments a ON c.id = a.customer_id AND a.branch_id = ?
            WHERE c.branch_id = ? AND c.deleted_at IS NULL
            HAVING total_debt_amount > 0
        ", [$branchId, $branchId]);

        return $query->getRowArray();
    }

    /**
     * Müşteri istatistikleri
     */
    public function getCustomerStats($branchId, $startDate, $endDate)
    {
        return $this->select('
                        COUNT(*) as total_customers,
                        COUNT(CASE WHEN DATE(created_at) >= ? AND DATE(created_at) <= ? THEN 1 END) as new_customers
                    ')
                    ->where('branch_id', $branchId)
                    ->where('deleted_at', null)
                    ->setBinds([$startDate, $endDate])
                    ->first();
    }

    /**
     * Doğum günü olan müşterileri getir
     */
    public function getCustomersByBirthday($monthDay, $branchId = null)
    {
        $builder = $this->select('id, branch_id, first_name, last_name, phone, email, birth_date')
                        ->where('deleted_at', null)
                        ->where('birth_date IS NOT NULL')
                        ->where("DATE_FORMAT(birth_date, '%m-%d')", $monthDay);

        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        return $builder->findAll();
    }

    /**
     * Yaklaşan doğum günleri (gelecek 7 gün)
     */
    public function getUpcomingBirthdays($branchId, $days = 7)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT
                id,
                first_name,
                last_name,
                phone,
                email,
                birth_date,
                CASE
                    WHEN DATE_FORMAT(birth_date, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d') THEN 0
                    WHEN DATE_FORMAT(birth_date, '%m-%d') > DATE_FORMAT(CURDATE(), '%m-%d') THEN
                        DATEDIFF(
                            DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(birth_date, '%m-%d'))),
                            CURDATE()
                        )
                    ELSE
                        DATEDIFF(
                            DATE(CONCAT(YEAR(CURDATE()) + 1, '-', DATE_FORMAT(birth_date, '%m-%d'))),
                            CURDATE()
                        )
                END as days_until_birthday
            FROM customers
            WHERE branch_id = ?
                AND deleted_at IS NULL
                AND birth_date IS NOT NULL
            HAVING days_until_birthday <= ?
            ORDER BY days_until_birthday ASC, first_name ASC
        ", [$branchId, $days]);

        return $query->getResultArray();
    }

    /**
     * Müşteri özet istatistikleri
     */
    public function getCustomerSummaryStats($customerId)
    {
        $db = \Config\Database::connect();
        
        // Randevu istatistikleri
        $appointmentStats = $db->query("
            SELECT
                COUNT(*) as total_appointments,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_appointments,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_appointments,
                COUNT(CASE WHEN status = 'no_show' THEN 1 END) as no_show_appointments,
                MIN(appointment_date) as first_appointment,
                MAX(appointment_date) as last_appointment
            FROM appointments
            WHERE customer_id = ?
        ", [$customerId])->getRowArray();

        // Ödeme istatistikleri
        $paymentStats = $db->query("
            SELECT
                COUNT(*) as total_payments,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as total_paid,
                COALESCE(SUM(CASE WHEN status = 'refunded' THEN refund_amount ELSE 0 END), 0) as total_refunded
            FROM payments
            WHERE customer_id = ?
        ", [$customerId])->getRowArray();

        // Paket istatistikleri
        $packageStats = $db->query("
            SELECT
                COUNT(*) as total_packages,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_packages,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_packages,
                COUNT(CASE WHEN status = 'expired' THEN 1 END) as expired_packages
            FROM customer_packages
            WHERE customer_id = ?
        ", [$customerId])->getRowArray();

        // Mesaj istatistikleri
        $messageStats = $db->query("
            SELECT
                COUNT(*) as total_messages,
                COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent_messages,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_messages,
                COUNT(CASE WHEN message_type = 'sms' THEN 1 END) as sms_messages,
                COUNT(CASE WHEN message_type = 'whatsapp' THEN 1 END) as whatsapp_messages
            FROM sent_messages
            WHERE customer_id = ?
        ", [$customerId])->getRowArray();

        return [
            'appointments' => $appointmentStats ?: [
                'total_appointments' => 0,
                'completed_appointments' => 0,
                'cancelled_appointments' => 0,
                'no_show_appointments' => 0,
                'first_appointment' => null,
                'last_appointment' => null
            ],
            'payments' => $paymentStats ?: [
                'total_payments' => 0,
                'total_paid' => 0,
                'total_refunded' => 0
            ],
            'packages' => $packageStats ?: [
                'total_packages' => 0,
                'active_packages' => 0,
                'completed_packages' => 0,
                'expired_packages' => 0
            ],
            'messages' => $messageStats ?: [
                'total_messages' => 0,
                'sent_messages' => 0,
                'failed_messages' => 0,
                'sms_messages' => 0,
                'whatsapp_messages' => 0
            ]
        ];
    }

    /**
     * Müşteri kredi bakiyesi hesapla
     */
    public function getCustomerCreditBalance($customerId)
    {
        $db = \Config\Database::connect();
        
        // Toplam randevu tutarı
        $appointmentTotal = $db->query("
            SELECT COALESCE(SUM(price), 0) as total
            FROM appointments
            WHERE customer_id = ?
        ", [$customerId])->getRowArray()['total'];
        
        // Toplam ödenen tutar (iadeler düşülmüş)
        $paymentTotal = $db->query("
            SELECT COALESCE(SUM(CASE
                WHEN status = 'completed' THEN amount
                WHEN status = 'refunded' THEN (amount - COALESCE(refund_amount, 0))
                ELSE 0
            END), 0) as total
            FROM payments
            WHERE customer_id = ?
        ", [$customerId])->getRowArray()['total'];
        
        $balance = $paymentTotal - $appointmentTotal;
        
        return [
            'appointment_total' => (float)$appointmentTotal,
            'payment_total' => (float)$paymentTotal,
            'balance' => (float)$balance,
            'has_credit' => $balance > 0,
            'has_debt' => $balance < 0,
            'credit_amount' => $balance > 0 ? $balance : 0,
            'debt_amount' => $balance < 0 ? abs($balance) : 0
        ];
    }

    /**
     * Kredi bakiyesi olan müşterileri getir
     */
    public function getCreditCustomers($branchId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT
                c.id,
                c.first_name,
                c.last_name,
                c.phone,
                c.email,
                COALESCE(SUM(CASE 
                    WHEN a.status IN ('confirmed', 'completed') THEN a.price 
                    ELSE 0 
                END), 0) as total_appointments,
                COALESCE(SUM(CASE
                    WHEN p.status = 'completed' THEN p.amount
                    WHEN p.status = 'refunded' THEN (p.amount - COALESCE(p.refund_amount, 0))
                    ELSE 0
                END), 0) as total_payments,
                (COALESCE(SUM(CASE
                    WHEN p.status = 'completed' THEN p.amount
                    WHEN p.status = 'refunded' THEN (p.amount - COALESCE(p.refund_amount, 0))
                    ELSE 0
                END), 0) - COALESCE(SUM(CASE 
                    WHEN a.status IN ('confirmed', 'completed') THEN a.price 
                    ELSE 0 
                END), 0)) as credit_balance
            FROM customers c
            LEFT JOIN appointments a ON c.id = a.customer_id AND a.branch_id = ?
            LEFT JOIN payments p ON c.id = p.customer_id AND p.branch_id = ?
            WHERE c.branch_id = ? AND c.deleted_at IS NULL
            GROUP BY c.id, c.first_name, c.last_name, c.phone, c.email
            HAVING credit_balance > 0
            ORDER BY credit_balance DESC
        ", [$branchId, $branchId, $branchId]);

        return $query->getResultArray();
    }
}