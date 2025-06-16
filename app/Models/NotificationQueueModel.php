<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationQueueModel extends Model
{
    protected $table = 'notification_queue';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'trigger_id',
        'customer_id',
        'appointment_id',
        'message_type',
        'recipient_phone',
        'message_content',
        'scheduled_at',
        'status',
        'sent_at',
        'error_message'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'branch_id' => 'required|integer',
        'trigger_id' => 'required|integer',
        'customer_id' => 'required|integer',
        'message_type' => 'required|in_list[sms,whatsapp]',
        'recipient_phone' => 'required|max_length[20]',
        'message_content' => 'required',
        'scheduled_at' => 'required|valid_date'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required' => 'Şube seçimi zorunludur',
            'integer' => 'Geçersiz şube'
        ],
        'trigger_id' => [
            'required' => 'Tetikleyici seçimi zorunludur',
            'integer' => 'Geçersiz tetikleyici'
        ],
        'customer_id' => [
            'required' => 'Müşteri seçimi zorunludur',
            'integer' => 'Geçersiz müşteri'
        ],
        'message_type' => [
            'required' => 'Mesaj türü seçimi zorunludur',
            'in_list' => 'Geçersiz mesaj türü'
        ],
        'recipient_phone' => [
            'required' => 'Alıcı telefon numarası zorunludur',
            'max_length' => 'Telefon numarası en fazla 20 karakter olabilir'
        ],
        'message_content' => [
            'required' => 'Mesaj içeriği zorunludur'
        ],
        'scheduled_at' => [
            'required' => 'Gönderim tarihi zorunludur',
            'valid_date' => 'Geçersiz tarih formatı'
        ]
    ];

    /**
     * Gönderilmeyi bekleyen mesajları getir
     */
    public function getPendingMessages($limit = 50)
    {
        return $this->select('notification_queue.*, customers.first_name, customers.last_name, notification_triggers.trigger_name')
                    ->join('customers', 'customers.id = notification_queue.customer_id', 'left')
                    ->join('notification_triggers', 'notification_triggers.id = notification_queue.trigger_id', 'left')
                    ->where('notification_queue.status', 'pending')
                    ->where('notification_queue.scheduled_at <=', date('Y-m-d H:i:s'))
                    ->orderBy('notification_queue.scheduled_at', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Şubeye göre kuyruk mesajlarını getir
     */
    public function getQueueByBranch($branchId, $filters = [])
    {
        $builder = $this->select('notification_queue.*, customers.first_name, customers.last_name, notification_triggers.trigger_name')
                        ->join('customers', 'customers.id = notification_queue.customer_id', 'left')
                        ->join('notification_triggers', 'notification_triggers.id = notification_queue.trigger_id', 'left')
                        ->where('notification_queue.branch_id', $branchId);

        // Filtreleme
        if (!empty($filters['status'])) {
            $builder->where('notification_queue.status', $filters['status']);
        }

        if (!empty($filters['message_type'])) {
            $builder->where('notification_queue.message_type', $filters['message_type']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('notification_queue.scheduled_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('notification_queue.scheduled_at <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->orderBy('notification_queue.scheduled_at', 'DESC')
                      ->paginate(20);
    }

    /**
     * Mesaj durumunu güncelle
     */
    public function updateMessageStatus($id, $status, $errorMessage = null)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'sent') {
            $data['sent_at'] = date('Y-m-d H:i:s');
        }

        if ($errorMessage) {
            $data['error_message'] = $errorMessage;
        }

        return $this->update($id, $data);
    }

    /**
     * Randevu hatırlatma mesajı kuyruğa ekle
     */
    public function scheduleAppointmentReminder($branchId, $triggerId, $customerId, $appointmentId, $messageType, $phone, $message, $scheduledAt)
    {
        // Aynı randevu için aynı tetikleyici ile zaten mesaj var mı kontrol et
        $existing = $this->where('branch_id', $branchId)
                         ->where('trigger_id', $triggerId)
                         ->where('appointment_id', $appointmentId)
                         ->where('status', 'pending')
                         ->first();

        if ($existing) {
            // Mevcut mesajı güncelle
            return $this->update($existing['id'], [
                'message_content' => $message,
                'scheduled_at' => $scheduledAt,
                'recipient_phone' => $phone
            ]);
        } else {
            // Yeni mesaj ekle
            return $this->insert([
                'branch_id' => $branchId,
                'trigger_id' => $triggerId,
                'customer_id' => $customerId,
                'appointment_id' => $appointmentId,
                'message_type' => $messageType,
                'recipient_phone' => $phone,
                'message_content' => $message,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending'
            ]);
        }
    }

    /**
     * Paket uyarı mesajı kuyruğa ekle
     */
    public function schedulePackageWarning($branchId, $triggerId, $customerId, $messageType, $phone, $message)
    {
        return $this->insert([
            'branch_id' => $branchId,
            'trigger_id' => $triggerId,
            'customer_id' => $customerId,
            'appointment_id' => null,
            'message_type' => $messageType,
            'recipient_phone' => $phone,
            'message_content' => $message,
            'scheduled_at' => date('Y-m-d H:i:s'), // Hemen gönder
            'status' => 'pending'
        ]);
    }

    /**
     * No-show bildirimi kuyruğa ekle
     */
    public function scheduleNoShowNotification($branchId, $triggerId, $customerId, $appointmentId, $messageType, $phone, $message, $scheduledAt)
    {
        return $this->insert([
            'branch_id' => $branchId,
            'trigger_id' => $triggerId,
            'customer_id' => $customerId,
            'appointment_id' => $appointmentId,
            'message_type' => $messageType,
            'recipient_phone' => $phone,
            'message_content' => $message,
            'scheduled_at' => $scheduledAt,
            'status' => 'pending'
        ]);
    }

    /**
     * Doğum günü kutlama mesajı kuyruğa ekle
     */
    public function scheduleBirthdayGreeting($branchId, $triggerId, $customerId, $messageType, $phone, $message, $scheduledAt)
    {
        // Aynı müşteri için aynı gün doğum günü mesajı var mı kontrol et
        $today = date('Y-m-d');
        $existing = $this->where('branch_id', $branchId)
                         ->where('trigger_id', $triggerId)
                         ->where('customer_id', $customerId)
                         ->where('DATE(scheduled_at)', $today)
                         ->where('status !=', 'cancelled')
                         ->first();

        if ($existing) {
            return false; // Zaten var, ekleme
        }

        return $this->insert([
            'branch_id' => $branchId,
            'trigger_id' => $triggerId,
            'customer_id' => $customerId,
            'appointment_id' => null,
            'message_type' => $messageType,
            'recipient_phone' => $phone,
            'message_content' => $message,
            'scheduled_at' => $scheduledAt,
            'status' => 'pending'
        ]);
    }

    /**
     * Randevuya bağlı bekleyen mesajları iptal et
     */
    public function cancelAppointmentMessages($appointmentId)
    {
        return $this->where('appointment_id', $appointmentId)
                    ->where('status', 'pending')
                    ->set(['status' => 'cancelled'])
                    ->update();
    }

    /**
     * Kuyruk istatistikleri
     */
    public function getQueueStats($branchId, $dateFrom = null, $dateTo = null)
    {
        $builder = $this->where('branch_id', $branchId);

        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }

        $stats = $builder->select('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->findAll();

        $result = [
            'pending' => 0,
            'sent' => 0,
            'failed' => 0,
            'cancelled' => 0,
            'total' => 0
        ];

        foreach ($stats as $stat) {
            $result[$stat['status']] = (int)$stat['count'];
            $result['total'] += (int)$stat['count'];
        }

        return $result;
    }

    /**
     * Eski mesajları temizle (30 günden eski)
     */
    public function cleanOldMessages()
    {
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        
        return $this->where('created_at <', $thirtyDaysAgo)
                    ->where('status !=', 'pending')
                    ->delete();
    }
}