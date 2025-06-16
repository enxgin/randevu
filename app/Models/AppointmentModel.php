<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table            = 'appointments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'customer_id',
        'service_id',
        'staff_id',
        'customer_package_id',
        'appointment_date',
        'start_time',
        'end_time',
        'duration',
        'status',
        'type',
        'recurrence_pattern',
        'price',
        'paid_amount',
        'payment_status',
        'notes',
        'service_notes',
        'created_by',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'branch_id'        => 'required|integer|is_not_unique[branches.id]',
        'customer_id'      => 'required|integer|is_not_unique[customers.id]',
        'service_id'       => 'required|integer|is_not_unique[services.id]',
        'staff_id'         => 'required|integer|is_not_unique[users.id]',
        'appointment_date' => 'required|valid_date',
        'start_time'       => 'required',
        'duration'         => 'required|integer|greater_than[0]',
        'status'           => 'permit_empty|in_list[pending,confirmed,completed,cancelled,no_show]',
        'type'             => 'permit_empty|in_list[one_time,recurring]',
        'price'            => 'required|numeric|greater_than_equal_to[0]',
        'paid_amount'      => 'permit_empty|numeric|greater_than_equal_to[0]',
        'payment_status'   => 'permit_empty|in_list[pending,partial,paid,refunded]'
    ];

    protected $validationMessages = [
        'branch_id' => [
            'required'      => 'Şube seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz şube seçimi.'
        ],
        'customer_id' => [
            'required'      => 'Müşteri seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz müşteri seçimi.'
        ],
        'service_id' => [
            'required'      => 'Hizmet seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz hizmet seçimi.'
        ],
        'staff_id' => [
            'required'      => 'Personel seçimi zorunludur.',
            'is_not_unique' => 'Geçersiz personel seçimi.'
        ],
        'appointment_date' => [
            'required'   => 'Randevu tarihi zorunludur.',
            'valid_date' => 'Geçerli bir tarih giriniz.'
        ],
        'start_time' => [
            'required' => 'Başlangıç saati zorunludur.'
        ],
        'end_time' => [
            'required' => 'Bitiş saati zorunludur.'
        ],
        'duration' => [
            'required'     => 'Süre zorunludur.',
            'integer'      => 'Süre geçerli bir sayı olmalıdır.',
            'greater_than' => 'Süre 0\'dan büyük olmalıdır.'
        ],
        'price' => [
            'required'              => 'Fiyat zorunludur.',
            'numeric'               => 'Fiyat geçerli bir sayı olmalıdır.',
            'greater_than_equal_to' => 'Fiyat 0 veya daha büyük olmalıdır.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setDefaults'];
    protected $beforeUpdate   = ['setDefaults'];
    protected $afterInsert    = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Varsayılan değerleri ayarla
     */
    protected function setDefaults(array $data)
    {
        // Bitiş saatini hesapla (eğer yoksa)
        if (isset($data['data']['start_time']) && isset($data['data']['duration']) && !isset($data['data']['end_time'])) {
            $startTime = new \DateTime($data['data']['start_time']);
            $endTime = clone $startTime;
            $endTime->add(new \DateInterval('PT' . $data['data']['duration'] . 'M'));
            $data['data']['end_time'] = $endTime->format('H:i:s');
        }
        
        // Varsayılan değerleri ayarla
        if (!isset($data['data']['type'])) {
            $data['data']['type'] = 'one_time';
        }
        
        if (!isset($data['data']['payment_status'])) {
            $data['data']['payment_status'] = 'pending';
        }
        
        if (!isset($data['data']['paid_amount'])) {
            $data['data']['paid_amount'] = 0;
        }
        
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'confirmed';
        }
        
        return $data;
    }

    /**
     * Takvim için randevuları getir (FullCalendar formatında)
     */
    public function getCalendarEvents($branchId = null, $staffId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
            appointments.id,
            appointments.appointment_date,
            appointments.start_time,
            appointments.end_time,
            appointments.duration,
            appointments.status,
            appointments.notes,
            appointments.price,
            appointments.payment_status,
            customers.first_name as customer_first_name,
            customers.last_name as customer_last_name,
            customers.phone as customer_phone,
            services.name as service_name,
            services.duration as service_duration,
            staff.first_name as staff_first_name,
            staff.last_name as staff_last_name,
            branches.name as branch_name
        ')
        ->join('customers', 'customers.id = appointments.customer_id')
        ->join('services', 'services.id = appointments.service_id')
        ->join('users staff', 'staff.id = appointments.staff_id')
        ->join('branches', 'branches.id = appointments.branch_id');

        // Şube filtresi
        if ($branchId) {
            $builder->where('appointments.branch_id', $branchId);
        }

        // Personel filtresi
        if ($staffId) {
            $builder->where('appointments.staff_id', $staffId);
        }

        // Tarih aralığı filtresi
        if ($startDate) {
            $builder->where('appointments.appointment_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('appointments.appointment_date <=', $endDate);
        }

        $appointments = $builder->orderBy('appointments.appointment_date, appointments.start_time')->findAll();

        // FullCalendar formatına çevir
        $events = [];
        foreach ($appointments as $appointment) {
            $events[] = [
                'id' => $appointment['id'],
                'title' => $appointment['customer_first_name'] . ' ' . $appointment['customer_last_name'] . ' - ' . $appointment['service_name'],
                'start' => $appointment['appointment_date'] . 'T' . $appointment['start_time'],
                'end' => $appointment['appointment_date'] . 'T' . $appointment['end_time'],
                'backgroundColor' => $this->getStatusColor($appointment['status']),
                'borderColor' => $this->getStatusColor($appointment['status']),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'customer_name' => $appointment['customer_first_name'] . ' ' . $appointment['customer_last_name'],
                    'customer_phone' => $appointment['customer_phone'],
                    'service_name' => $appointment['service_name'],
                    'staff_name' => $appointment['staff_first_name'] . ' ' . $appointment['staff_last_name'],
                    'branch_name' => $appointment['branch_name'],
                    'status' => $appointment['status'],
                    'duration' => $appointment['duration'],
                    'price' => $appointment['price'],
                    'payment_status' => $appointment['payment_status'],
                    'notes' => $appointment['notes']
                ]
            ];
        }

        return $events;
    }

    /**
     * Durum rengini getir
     */
    public function getStatusColor($status)
    {
        $colors = [
            'pending'   => '#f59e0b', // amber
            'confirmed' => '#3b82f6', // blue
            'completed' => '#10b981', // green
            'cancelled' => '#ef4444', // red
            'no_show'   => '#6b7280'  // gray
        ];

        return $colors[$status] ?? '#6b7280';
    }

    /**
     * Durum etiketini getir
     */
    public function getStatusLabel($status)
    {
        $labels = [
            'pending'   => 'Onay Bekliyor',
            'confirmed' => 'Onaylandı',
            'completed' => 'Tamamlandı',
            'cancelled' => 'İptal Edildi',
            'no_show'   => 'Gelmedi'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Ödeme durumu etiketini getir
     */
    public function getPaymentStatusLabel($status)
    {
        $labels = [
            'pending'  => 'Ödeme Bekliyor',
            'partial'  => 'Kısmi Ödendi',
            'paid'     => 'Ödendi',
            'refunded' => 'İade Edildi'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Personelin belirli tarih ve saatte müsait olup olmadığını kontrol et
     */
    public function isStaffAvailable($staffId, $date, $startTime, $endTime, $excludeAppointmentId = null)
    {
        $builder = $this->where('staff_id', $staffId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->groupStart()
                ->where('start_time <', $endTime)
                ->where('end_time >', $startTime)
            ->groupEnd();

        if ($excludeAppointmentId) {
            $builder->where('id !=', $excludeAppointmentId);
        }

        return $builder->countAllResults() === 0;
    }

    /**
     * Randevu çakışması kontrolü
     */
    public function checkConflict($staffId, $date, $startTime, $endTime, $excludeAppointmentId = null)
    {
        return !$this->isStaffAvailable($staffId, $date, $startTime, $endTime, $excludeAppointmentId);
    }

    /**
     * Randevu durumunu güncelle
     */
    public function updateStatus($appointmentId, $status, $notes = null)
    {
        $data = ['status' => $status];
        
        switch ($status) {
            case 'confirmed':
                $data['confirmed_at'] = date('Y-m-d H:i:s');
                break;
            case 'completed':
                $data['completed_at'] = date('Y-m-d H:i:s');
                if ($notes) {
                    $data['service_notes'] = $notes;
                }
                break;
            case 'cancelled':
                $data['cancelled_at'] = date('Y-m-d H:i:s');
                if ($notes) {
                    $data['cancellation_reason'] = $notes;
                }
                break;
        }

        return $this->update($appointmentId, $data);
    }

    /**
     * Randevu detaylarını getir
     */
    public function getAppointmentDetail($id)
    {
        return $this->select('
            appointments.*,
            customers.first_name as customer_first_name,
            customers.last_name as customer_last_name,
            customers.phone as customer_phone,
            customers.email as customer_email,
            services.name as service_name,
            services.duration as service_duration,
            services.price as service_price,
            staff.first_name as staff_first_name,
            staff.last_name as staff_last_name,
            branches.name as branch_name,
            creator.first_name as creator_first_name,
            creator.last_name as creator_last_name
        ')
        ->join('customers', 'customers.id = appointments.customer_id')
        ->join('services', 'services.id = appointments.service_id')
        ->join('users staff', 'staff.id = appointments.staff_id')
        ->join('branches', 'branches.id = appointments.branch_id')
        ->join('users creator', 'creator.id = appointments.created_by', 'left')
        ->find($id);
    }

    /**
     * Müşterinin randevu geçmişini getir
     */
    public function getCustomerAppointments($customerId, $limit = null)
    {
        $builder = $this->select('
            appointments.*,
            services.name as service_name,
            staff.first_name as staff_first_name,
            staff.last_name as staff_last_name
        ')
        ->join('services', 'services.id = appointments.service_id')
        ->join('users staff', 'staff.id = appointments.staff_id')
        ->where('appointments.customer_id', $customerId)
        ->orderBy('appointments.appointment_date DESC, appointments.start_time DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Personelin randevularını getir
     */
    public function getStaffAppointments($staffId, $date = null)
    {
        $builder = $this->select('
            appointments.*,
            customers.first_name as customer_first_name,
            customers.last_name as customer_last_name,
            customers.phone as customer_phone,
            services.name as service_name
        ')
        ->join('customers', 'customers.id = appointments.customer_id')
        ->join('services', 'services.id = appointments.service_id')
        ->where('appointments.staff_id', $staffId);

        if ($date) {
            $builder->where('appointments.appointment_date', $date);
        }

        return $builder->orderBy('appointments.appointment_date, appointments.start_time')->findAll();
    }

    /**
     * Randevu istatistikleri
     */
    public function getAppointmentStats($branchId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->selectCount('id', 'total');
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }
        
        if ($startDate) {
            $builder->where('appointment_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('appointment_date <=', $endDate);
        }
        
        $total = $builder->get()->getRow()->total;

        // Durum bazlı istatistikler
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $stats = ['total' => $total];
        
        foreach ($statuses as $status) {
            $builder = $this->selectCount('id', 'count')->where('status', $status);
            
            if ($branchId) {
                $builder->where('branch_id', $branchId);
            }
            
            if ($startDate) {
                $builder->where('appointment_date >=', $startDate);
            }
            
            if ($endDate) {
                $builder->where('appointment_date <=', $endDate);
            }
            
            $stats[$status] = $builder->get()->getRow()->count;
        }

        return $stats;
    }

    /**
     * Belirli tarih için uygun zaman aralıklarını getir
     */
    public function getAvailableTimeSlots($staffId, $date, $duration)
    {
        // Çalışma saatleri (09:00 - 18:00 arası, 30 dakika aralıklarla)
        $workStart = '09:00';
        $workEnd = '18:00';
        $slotInterval = 30; // dakika

        // Mevcut randevuları getir
        $existingAppointments = $this->select('start_time, end_time')
            ->where('staff_id', $staffId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('start_time')
            ->findAll();

        $availableSlots = [];
        $currentTime = strtotime($workStart);
        $endTime = strtotime($workEnd);

        while ($currentTime < $endTime) {
            $slotStart = date('H:i', $currentTime);
            $slotEnd = date('H:i', $currentTime + ($duration * 60));

            // Çalışma saatleri içinde mi kontrol et
            if (strtotime($slotEnd) <= $endTime) {
                $isAvailable = true;

                // Mevcut randevularla çakışma kontrolü
                foreach ($existingAppointments as $appointment) {
                    if ($slotStart < $appointment['end_time'] && $slotEnd > $appointment['start_time']) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable) {
                    $availableSlots[] = [
                        'time' => $slotStart,
                        'label' => $slotStart . ' - ' . $slotEnd
                    ];
                }
            }

            $currentTime += ($slotInterval * 60);
        }

        return $availableSlots;
    }
}