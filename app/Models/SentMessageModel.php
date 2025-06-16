<?php

namespace App\Models;

use CodeIgniter\Model;

class SentMessageModel extends Model
{
    protected $table = 'sent_messages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'customer_id',
        'appointment_id',
        'message_type',
        'trigger_type',
        'recipient_phone',
        'message_content',
        'status',
        'provider_response',
        'sent_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
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
        'message_type' => 'required|in_list[sms,whatsapp]',
        'trigger_type' => 'required|max_length[100]',
        'recipient_phone' => 'required|max_length[20]',
        'message_content' => 'required',
        'status' => 'permit_empty|in_list[pending,sent,failed,delivered]'
    ];
    protected $validationMessages = [];
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
     * Müşteri bazlı mesaj geçmişi
     */
    public function getCustomerMessages($customerId, $limit = 50)
    {
        return $this->select('sent_messages.*, customers.first_name, customers.last_name')
                   ->join('customers', 'customers.id = sent_messages.customer_id')
                   ->where('sent_messages.customer_id', $customerId)
                   ->orderBy('sent_messages.created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Şube bazlı mesaj geçmişi
     */
    public function getBranchMessages($branchId, $filters = [])
    {
        $builder = $this->select('sent_messages.*, customers.first_name, customers.last_name, customers.phone')
                       ->join('customers', 'customers.id = sent_messages.customer_id')
                       ->where('sent_messages.branch_id', $branchId);

        // Filtreleme
        if (!empty($filters['status'])) {
            $builder->where('sent_messages.status', $filters['status']);
        }

        if (!empty($filters['message_type'])) {
            $builder->where('sent_messages.message_type', $filters['message_type']);
        }

        if (!empty($filters['trigger_type'])) {
            $builder->where('sent_messages.trigger_type', $filters['trigger_type']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('sent_messages.created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('sent_messages.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->orderBy('sent_messages.created_at', 'DESC')
                      ->paginate(20);
    }

    /**
     * Mesaj durumunu güncelle
     */
    public function updateMessageStatus($messageId, $status, $providerResponse = null)
    {
        $data = [
            'status' => $status
        ];

        if ($status === 'sent') {
            $data['sent_at'] = date('Y-m-d H:i:s');
        }

        if ($providerResponse) {
            $data['provider_response'] = is_array($providerResponse) ? json_encode($providerResponse) : $providerResponse;
        }

        return $this->update($messageId, $data);
    }

    /**
     * Bekleyen mesajları getir
     */
    public function getPendingMessages($limit = 100)
    {
        return $this->select('sent_messages.*, customers.first_name, customers.last_name, customers.phone')
                   ->join('customers', 'customers.id = sent_messages.customer_id')
                   ->where('sent_messages.status', 'pending')
                   ->orderBy('sent_messages.created_at', 'ASC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Mesaj istatistikleri
     */
    public function getMessageStats($branchId, $dateFrom = null, $dateTo = null)
    {
        $builder = $this->where('branch_id', $branchId);

        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }

        $stats = [
            'total' => $builder->countAllResults(false),
            'sent' => $builder->where('status', 'sent')->countAllResults(false),
            'failed' => $builder->where('status', 'failed')->countAllResults(false),
            'pending' => $builder->where('status', 'pending')->countAllResults(false),
            'sms' => $builder->where('message_type', 'sms')->countAllResults(false),
            'whatsapp' => $builder->where('message_type', 'whatsapp')->countAllResults(false)
        ];

        return $stats;
    }

    /**
     * Günlük mesaj sayısı
     */
    public function getDailyMessageCount($branchId, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        return $this->where('branch_id', $branchId)
                   ->where('DATE(created_at)', $date)
                   ->countAllResults();
    }

    /**
     * Randevu bazlı mesaj kontrolü (tekrar gönderim önleme)
     */
    public function hasAppointmentMessage($appointmentId, $triggerType, $messageType = 'sms')
    {
        return $this->where('appointment_id', $appointmentId)
                   ->where('trigger_type', $triggerType)
                   ->where('message_type', $messageType)
                   ->where('status !=', 'failed')
                   ->first() !== null;
    }

    /**
     * Müşteri bazlı mesaj kontrolü (doğum günü gibi)
     */
    public function hasCustomerMessage($customerId, $triggerType, $date = null, $messageType = 'sms')
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        return $this->where('customer_id', $customerId)
                   ->where('trigger_type', $triggerType)
                   ->where('message_type', $messageType)
                   ->where('DATE(created_at)', $date)
                   ->where('status !=', 'failed')
                   ->first() !== null;
    }
}