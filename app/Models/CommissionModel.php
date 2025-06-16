<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table = 'commissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch_id',
        'user_id',
        'appointment_id',
        'payment_id',
        'service_id',
        'commission_rule_id',
        'service_amount',
        'commission_amount',
        'commission_type',
        'commission_rate',
        'is_package_service',
        'status',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'branch_id' => 'required|is_natural_no_zero',
        'user_id' => 'required|is_natural_no_zero',
        'appointment_id' => 'required|is_natural_no_zero',
        'service_id' => 'required|is_natural_no_zero',
        'service_amount' => 'required|numeric|greater_than[0]',
        'commission_amount' => 'required|numeric|greater_than_equal_to[0]',
        'commission_type' => 'required|in_list[percentage,fixed_amount]',
        'is_package_service' => 'required|in_list[0,1]',
        'status' => 'required|in_list[pending,paid,cancelled,refunded]'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required' => 'Şube seçimi zorunludur.',
            'is_natural_no_zero' => 'Geçerli bir şube seçiniz.'
        ],
        'user_id' => [
            'required' => 'Personel seçimi zorunludur.',
            'is_natural_no_zero' => 'Geçerli bir personel seçiniz.'
        ],
        'appointment_id' => [
            'required' => 'Randevu seçimi zorunludur.',
            'is_natural_no_zero' => 'Geçerli bir randevu seçiniz.'
        ],
        'service_id' => [
            'required' => 'Hizmet seçimi zorunludur.',
            'is_natural_no_zero' => 'Geçerli bir hizmet seçiniz.'
        ],
        'service_amount' => [
            'required' => 'Hizmet tutarı zorunludur.',
            'numeric' => 'Hizmet tutarı sayısal olmalıdır.',
            'greater_than' => 'Hizmet tutarı 0\'dan büyük olmalıdır.'
        ],
        'commission_amount' => [
            'required' => 'Prim tutarı zorunludur.',
            'numeric' => 'Prim tutarı sayısal olmalıdır.',
            'greater_than_equal_to' => 'Prim tutarı 0 veya daha büyük olmalıdır.'
        ],
        'commission_type' => [
            'required' => 'Prim tipi seçimi zorunludur.',
            'in_list' => 'Geçerli bir prim tipi seçiniz.'
        ],
        'is_package_service' => [
            'required' => 'Paket hizmeti durumu zorunludur.',
            'in_list' => 'Geçerli bir paket hizmeti durumu seçiniz.'
        ],
        'status' => [
            'required' => 'Durum seçimi zorunludur.',
            'in_list' => 'Geçerli bir durum seçiniz.'
        ]
    ];

    /**
     * Randevu için prim kaydı oluştur
     */
    public function createCommissionForAppointment($appointmentId, $userId, $serviceId, $serviceAmount, $branchId, $isPackage = false, $paymentId = null)
    {
        $commissionRuleModel = new CommissionRuleModel();
        $rule = $commissionRuleModel->getRuleForService($serviceId, $userId, $branchId, $isPackage);

        if (!$rule) {
            return false; // Kural bulunamadı
        }

        $commissionAmount = $commissionRuleModel->calculateCommission($serviceAmount, $rule);

        $data = [
            'branch_id' => $branchId,
            'user_id' => $userId,
            'appointment_id' => $appointmentId,
            'payment_id' => $paymentId,
            'service_id' => $serviceId,
            'commission_rule_id' => $rule['id'],
            'service_amount' => $serviceAmount,
            'commission_amount' => $commissionAmount,
            'commission_type' => $rule['commission_type'],
            'commission_rate' => $rule['commission_value'],
            'is_package_service' => $isPackage ? 1 : 0,
            'status' => 'pending'
        ];

        return $this->insert($data);
    }

    /**
     * Personelin belirli tarih aralığındaki primlerini getir
     */
    public function getUserCommissions($userId, $startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->select('commissions.*, appointments.appointment_date, appointments.start_time, services.name as service_name, customers.first_name as customer_first_name, customers.last_name as customer_last_name')
                        ->join('appointments', 'appointments.id = commissions.appointment_id', 'left')
                        ->join('services', 'services.id = commissions.service_id', 'left')
                        ->join('customers', 'customers.id = appointments.customer_id', 'left')
                        ->where('commissions.user_id', $userId);

        if ($startDate) {
            $builder->where('appointments.appointment_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('appointments.appointment_date <=', $endDate);
        }

        if ($status) {
            $builder->where('commissions.status', $status);
        }

        return $builder->orderBy('appointments.appointment_date', 'DESC')
                       ->orderBy('appointments.start_time', 'DESC')
                       ->findAll();
    }

    /**
     * Şubenin belirli tarih aralığındaki primlerini getir
     */
    public function getBranchCommissions($branchId, $startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->select('commissions.*, appointments.appointment_date, appointments.start_time, services.name as service_name, customers.first_name as customer_first_name, customers.last_name as customer_last_name, users.first_name as staff_first_name, users.last_name as staff_last_name')
                        ->join('appointments', 'appointments.id = commissions.appointment_id', 'left')
                        ->join('services', 'services.id = commissions.service_id', 'left')
                        ->join('customers', 'customers.id = appointments.customer_id', 'left')
                        ->join('users', 'users.id = commissions.user_id', 'left')
                        ->where('commissions.branch_id', $branchId);

        if ($startDate) {
            $builder->where('appointments.appointment_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('appointments.appointment_date <=', $endDate);
        }

        if ($status) {
            $builder->where('commissions.status', $status);
        }

        return $builder->orderBy('appointments.appointment_date', 'DESC')
                       ->orderBy('appointments.start_time', 'DESC')
                       ->findAll();
    }

    /**
     * Personelin prim özetini getir
     */
    public function getUserCommissionSummary($userId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
                        COUNT(*) as total_services,
                        SUM(commissions.service_amount) as total_service_amount,
                        SUM(commissions.commission_amount) as total_commission_amount,
                        SUM(CASE WHEN commissions.is_package_service = 1 THEN commissions.commission_amount ELSE 0 END) as package_commission,
                        SUM(CASE WHEN commissions.is_package_service = 0 THEN commissions.commission_amount ELSE 0 END) as regular_commission,
                        SUM(CASE WHEN commissions.status = "pending" THEN commissions.commission_amount ELSE 0 END) as pending_commission,
                        SUM(CASE WHEN commissions.status = "paid" THEN commissions.commission_amount ELSE 0 END) as paid_commission
                    ')
                    ->join('appointments', 'appointments.id = commissions.appointment_id', 'left')
                    ->where('commissions.user_id', $userId);

        if ($startDate) {
            $builder->where('appointments.appointment_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('appointments.appointment_date <=', $endDate);
        }

        return $builder->first();
    }

    /**
     * Şubenin prim özetini getir
     */
    public function getBranchCommissionSummary($branchId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
                        COUNT(*) as total_services,
                        COUNT(DISTINCT commissions.user_id) as total_staff,
                        SUM(commissions.service_amount) as total_service_amount,
                        SUM(commissions.commission_amount) as total_commission_amount,
                        SUM(CASE WHEN commissions.is_package_service = 1 THEN commissions.commission_amount ELSE 0 END) as package_commission,
                        SUM(CASE WHEN commissions.is_package_service = 0 THEN commissions.commission_amount ELSE 0 END) as regular_commission,
                        SUM(CASE WHEN commissions.status = "pending" THEN commissions.commission_amount ELSE 0 END) as pending_commission,
                        SUM(CASE WHEN commissions.status = "paid" THEN commissions.commission_amount ELSE 0 END) as paid_commission
                    ')
                    ->join('appointments', 'appointments.id = commissions.appointment_id', 'left')
                    ->where('commissions.branch_id', $branchId);

        if ($startDate) {
            $builder->where('appointments.appointment_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('appointments.appointment_date <=', $endDate);
        }

        return $builder->first();
    }

    /**
     * Randevuya göre prim kaydını getir
     */
    public function getCommissionByAppointment($appointmentId)
    {
        return $this->where('appointment_id', $appointmentId)->first();
    }

    /**
     * Prim durumunu güncelle
     */
    public function updateCommissionStatus($commissionId, $status, $notes = null)
    {
        $data = ['status' => $status];
        if ($notes) {
            $data['notes'] = $notes;
        }

        return $this->update($commissionId, $data);
    }

    /**
     * İade durumunda prim iptal et
     */
    public function refundCommission($appointmentId, $notes = 'Hizmet iadesi nedeniyle prim iptal edildi')
    {
        return $this->where('appointment_id', $appointmentId)
                    ->set(['status' => 'refunded', 'notes' => $notes])
                    ->update();
    }

    /**
     * Durum açıklamaları
     */
    public function getStatusLabels()
    {
        return [
            'pending' => 'Beklemede',
            'paid' => 'Ödendi',
            'cancelled' => 'İptal Edildi',
            'refunded' => 'İade Edildi'
        ];
    }

    /**
     * Personel bazlı aylık prim raporu
     */
    public function getMonthlyCommissionReport($branchId, $year, $month)
    {
        return $this->select('
                        users.first_name,
                        users.last_name,
                        COUNT(*) as total_services,
                        SUM(commissions.service_amount) as total_service_amount,
                        SUM(commissions.commission_amount) as total_commission_amount,
                        SUM(CASE WHEN commissions.is_package_service = 1 THEN commissions.commission_amount ELSE 0 END) as package_commission,
                        SUM(CASE WHEN commissions.is_package_service = 0 THEN commissions.commission_amount ELSE 0 END) as regular_commission
                    ')
                    ->join('appointments', 'appointments.id = commissions.appointment_id', 'left')
                    ->join('users', 'users.id = commissions.user_id', 'left')
                    ->where('commissions.branch_id', $branchId)
                    ->where('YEAR(appointments.appointment_date)', $year)
                    ->where('MONTH(appointments.appointment_date)', $month)
                    ->where('commissions.status !=', 'cancelled')
                    ->groupBy('commissions.user_id')
                    ->orderBy('total_commission_amount', 'DESC')
                    ->findAll();
    }
}