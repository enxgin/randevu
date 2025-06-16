<?php

namespace App\Models;

use CodeIgniter\Model;

class InAppNotificationModel extends Model
{
    protected $table = 'in_app_notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'title',
        'message',
        'type',
        'action_type',
        'action_id',
        'is_read',
        'read_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'title' => 'required|string|max_length[255]',
        'message' => 'required|string',
        'type' => 'permit_empty|in_list[info,success,warning,error]',
        'action_type' => 'permit_empty|string|max_length[50]',
        'action_id' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'Kullanıcı ID zorunludur.',
            'integer' => 'Geçerli bir kullanıcı ID giriniz.'
        ],
        'title' => [
            'required' => 'Başlık zorunludur.',
            'string' => 'Başlık metin olmalıdır.',
            'max_length' => 'Başlık en fazla 255 karakter olabilir.'
        ],
        'message' => [
            'required' => 'Mesaj zorunludur.',
            'string' => 'Mesaj metin olmalıdır.'
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
     * Kullanıcıya bildirim gönder
     */
    public function sendNotification($userId, $title, $message, $type = 'info', $actionType = null, $actionId = null)
    {
        return $this->insert([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'action_type' => $actionType,
            'action_id' => $actionId,
            'is_read' => false
        ]);
    }

    /**
     * Kullanıcının okunmamış bildirimlerini getir
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Kullanıcının tüm bildirimlerini getir
     */
    public function getUserNotifications($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Bildirimi okundu olarak işaretle
     */
    public function markAsRead($notificationId, $userId = null)
    {
        $builder = $this->where('id', $notificationId);
        
        if ($userId) {
            $builder->where('user_id', $userId);
        }
        
        return $builder->set([
            'is_read' => true,
            'read_at' => date('Y-m-d H:i:s')
        ])->update();
    }

    /**
     * Kullanıcının tüm bildirimlerini okundu olarak işaretle
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->set([
                        'is_read' => true,
                        'read_at' => date('Y-m-d H:i:s')
                    ])
                    ->update();
    }

    /**
     * Kullanıcının okunmamış bildirim sayısını getir
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->countAllResults();
    }

    /**
     * Eski bildirimleri temizle (30 günden eski)
     */
    public function cleanOldNotifications($days = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('created_at <', $cutoffDate)
                    ->where('is_read', true)
                    ->delete();
    }

    /**
     * Bildirim türüne göre ikon getir
     */
    public function getTypeIcon($type)
    {
        $icons = [
            'info' => 'fas fa-info-circle',
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'error' => 'fas fa-times-circle'
        ];

        return $icons[$type] ?? 'fas fa-bell';
    }

    /**
     * Bildirim türüne göre renk getir
     */
    public function getTypeColor($type)
    {
        $colors = [
            'info' => 'text-blue-600 bg-blue-100',
            'success' => 'text-green-600 bg-green-100',
            'warning' => 'text-yellow-600 bg-yellow-100',
            'error' => 'text-red-600 bg-red-100'
        ];

        return $colors[$type] ?? 'text-gray-600 bg-gray-100';
    }

    /**
     * Toplu bildirim gönder (şube bazlı)
     */
    public function sendBulkNotification($branchId, $title, $message, $type = 'info', $actionType = null, $actionId = null, $excludeUserId = null)
    {
        $userModel = new \App\Models\UserModel();
        $users = $userModel->where('branch_id', $branchId);
        
        if ($excludeUserId) {
            $users->where('id !=', $excludeUserId);
        }
        
        $users = $users->findAll();
        
        $success = true;
        foreach ($users as $user) {
            if (!$this->sendNotification($user['id'], $title, $message, $type, $actionType, $actionId)) {
                $success = false;
            }
        }
        
        return $success;
    }
}