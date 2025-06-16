<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\BranchModel;
use App\Models\UserModel;
use App\Models\CustomerModel;
use App\Models\ServiceModel;
use App\Models\CustomerPackageModel;
use App\Models\CommissionModel;
use App\Models\PaymentModel;
use App\Models\InAppNotificationModel;
use App\Libraries\NotificationTriggerService;

class Calendar extends BaseController
{
    protected $appointmentModel;
    protected $branchModel;
    protected $userModel;
    protected $customerModel;
    protected $serviceModel;
    protected $customerPackageModel;
    protected $commissionModel;
    protected $paymentModel;
    protected $notificationModel;
    protected $triggerService;
    protected $db;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->branchModel = new BranchModel();
        $this->userModel = new UserModel();
        $this->customerModel = new CustomerModel();
        $this->serviceModel = new ServiceModel();
        $this->customerPackageModel = new CustomerPackageModel();
        $this->commissionModel = new CommissionModel();
        $this->paymentModel = new PaymentModel();
        $this->notificationModel = new InAppNotificationModel();
        $this->triggerService = new NotificationTriggerService();
        $this->db = \Config\Database::connect();
    }

    /**
     * Ana takvim sayfası
     */
    public function index()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // Rol bazlı yetkilendirme
        $branches = [];
        $staff = [];

        if ($userRole === 'admin') {
            // Admin tüm şubeleri ve personelleri görebilir
            $branches = $this->branchModel->getActiveBranches();
            $staff = $this->userModel->getStaffUsers();
        } elseif ($userRole === 'manager') {
            // Yönetici sadece kendi şubesini görebilir
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
            $staff = $this->userModel->getStaffUsers($userBranchId);
        } elseif ($userRole === 'receptionist') {
            // Danışma kendi şubesini görebilir
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
            $staff = $this->userModel->getStaffUsers($userBranchId);
        } else {
            // Personel sadece kendini görebilir
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
            $staff = $this->userModel->where('id', $userId)->findAll();
        }

        $data = [
            'title' => 'Randevu Takvimi',
            'pageTitle' => 'Randevu Takvimi',
            'branches' => $branches,
            'staff' => $staff,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId,
            'userId' => $userId
        ];

        return view('calendar/index', $data);
    }

    /**
     * Takvim eventlerini JSON formatında döndür (AJAX)
     */
    public function getEvents()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // GET parametrelerini al
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $branchId = $this->request->getGet('branch_id');
        $staffId = $this->request->getGet('staff_id');

        // Rol bazlı filtreleme
        if ($userRole === 'admin') {
            // Admin istediği şube/personeli görebilir
            $filterBranchId = $branchId;
            $filterStaffId = $staffId;
        } elseif ($userRole === 'manager' || $userRole === 'receptionist') {
            // Yönetici/Danışma sadece kendi şubesini görebilir
            $filterBranchId = $userBranchId;
            $filterStaffId = $staffId;
        } else {
            // Personel sadece kendini görebilir
            $filterBranchId = $userBranchId;
            $filterStaffId = $userId;
        }

        $events = $this->appointmentModel->getCalendarEvents(
            $filterBranchId,
            $filterStaffId,
            $start,
            $end
        );

        return $this->response->setJSON($events);
    }

    /**
     * Randevu oluşturma sayfası
     */
    public function create()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Personel randevu oluşturamaz
        if ($userRole === 'staff') {
            return redirect()->to('/calendar')->with('error', 'Randevu oluşturma yetkiniz bulunmamaktadır.');
        }

        if ($this->request->getMethod() === 'POST') {
            $startTime = $this->request->getPost('start_time');
            $duration = $this->request->getPost('duration');
            $customerPackageId = $this->request->getPost('customer_package_id');
            $serviceId = $this->request->getPost('service_id');
            
            // Bitiş saatini hesapla
            $endTime = date('H:i:s', strtotime($startTime . ' + ' . $duration . ' minutes'));
            
            // Paket seçildiğinde service_id'yi paketten al
            if ($customerPackageId && !$serviceId) {
                $customerPackage = $this->customerPackageModel->find($customerPackageId);
                if ($customerPackage) {
                    // Paketin ilk hizmetini al (varsayılan olarak)
                    $packageServices = $this->db->table('package_services')
                        ->where('package_id', $customerPackage['package_id'])
                        ->get()->getResultArray();
                    if (!empty($packageServices)) {
                        $serviceId = $packageServices[0]['service_id'];
                    }
                }
            }
            
            $data = [
                'branch_id' => $this->request->getPost('branch_id') ?: $userBranchId,
                'customer_id' => $this->request->getPost('customer_id'),
                'service_id' => $serviceId,
                'staff_id' => $this->request->getPost('staff_id'),
                'customer_package_id' => $customerPackageId ?: null,
                'appointment_date' => $this->request->getPost('appointment_date'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => $duration,
                'type' => 'one_time',
                'price' => $this->request->getPost('price'),
                'paid_amount' => 0,
                'payment_status' => 'pending',
                'notes' => $this->request->getPost('notes'),
                'created_by' => session()->get('user_id'),
                'status' => 'confirmed'
            ];

            // Çakışma kontrolü
            $endTime = date('H:i:s', strtotime($data['start_time'] . ' + ' . $data['duration'] . ' minutes'));
            if ($this->appointmentModel->checkConflict($data['staff_id'], $data['appointment_date'], $data['start_time'], $endTime)) {
                session()->setFlashdata('error', 'Seçilen tarih ve saatte personel müsait değil.');
                session()->setFlashdata('formData', $this->request->getPost());
            } else {
                if ($this->appointmentModel->save($data)) {
                    $appointmentId = $this->appointmentModel->getInsertID();
                    
                    // Randevu hatırlatma mesajlarını planla
                    try {
                        $this->triggerService->scheduleAppointmentReminders($appointmentId);
                    } catch (\Exception $e) {
                        log_message('error', 'Randevu hatırlatma planlama hatası: ' . $e->getMessage());
                    }
                    
                    // In-app bildirim oluştur
                    $this->createAppointmentNotification($appointmentId, 'created');
                    
                    session()->setFlashdata('success', 'Randevu başarıyla oluşturuldu.');
                    return redirect()->to('/calendar');
                } else {
                    session()->setFlashdata('errors', $this->appointmentModel->errors());
                    session()->setFlashdata('formData', $this->request->getPost());
                }
            }
        }

        // Form verileri
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        // URL parametrelerinden tarih ve saat bilgisini al
        $defaultDate = $this->request->getGet('date') ?: date('Y-m-d');
        $defaultTime = $this->request->getGet('time') ?: '09:00';
        
        // Form verilerini hazırla
        $formData = session()->getFlashdata('formData') ?: [];
        if (empty($formData)) {
            $formData['appointment_date'] = $defaultDate;
            $formData['start_time'] = $defaultTime;
        }
        
        $data = [
            'title' => 'Yeni Randevu Oluştur',
            'pageTitle' => 'Yeni Randevu Oluştur',
            'branches' => $branches,
            'customers' => $this->customerModel->getActiveCustomers($userBranchId),
            'services' => $this->serviceModel->getActiveServices($userBranchId),
            'staff' => $this->userModel->getStaffUsers($userBranchId),
            'formData' => $formData,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('calendar/create', $data);
    }

    /**
     * Randevu düzenleme
     */
    public function edit($id)
    {
        $appointment = $this->appointmentModel->getAppointmentDetail($id);
        if (!$appointment) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Randevu bulunamadı');
        }

        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        // Yetki kontrolü
        if ($userRole === 'staff' && $appointment['staff_id'] != $userId) {
            return redirect()->to('/calendar')->with('error', 'Bu randevuyu düzenleme yetkiniz bulunmamaktadır.');
        }

        if ($userRole !== 'admin' && $appointment['branch_id'] != $userBranchId) {
            return redirect()->to('/calendar')->with('error', 'Bu randevuya erişim yetkiniz bulunmamaktadır.');
        }

        if ($this->request->getMethod() === 'POST') {
            $startTime = $this->request->getPost('start_time');
            $duration = $this->request->getPost('duration');
            
            // Bitiş saatini hesapla
            $endTime = date('H:i:s', strtotime($startTime . ' + ' . $duration . ' minutes'));
            
            $data = [
                'customer_id' => $this->request->getPost('customer_id'),
                'service_id' => $this->request->getPost('service_id'),
                'staff_id' => $this->request->getPost('staff_id'),
                'appointment_date' => $this->request->getPost('appointment_date'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => $duration,
                'price' => $this->request->getPost('price'),
                'notes' => $this->request->getPost('notes'),
                'status' => $this->request->getPost('status')
            ];

            // Çakışma kontrolü (mevcut randevu hariç)
            $endTime = date('H:i:s', strtotime($data['start_time'] . ' + ' . $data['duration'] . ' minutes'));
            if ($this->appointmentModel->checkConflict($data['staff_id'], $data['appointment_date'], $data['start_time'], $endTime, $id)) {
                session()->setFlashdata('error', 'Seçilen tarih ve saatte personel müsait değil.');
                session()->setFlashdata('formData', $this->request->getPost());
            } else {
                if ($this->appointmentModel->update($id, $data)) {
                    // Randevu hatırlatma mesajlarını yeniden planla
                    try {
                        $this->triggerService->rescheduleAppointmentReminders($id);
                    } catch (\Exception $e) {
                        log_message('error', 'Randevu hatırlatma yeniden planlama hatası: ' . $e->getMessage());
                    }
                    
                    // In-app bildirim oluştur
                    $this->createAppointmentNotification($id, 'updated');
                    
                    session()->setFlashdata('success', 'Randevu başarıyla güncellendi.');
                    return redirect()->to('/calendar');
                } else {
                    session()->setFlashdata('errors', $this->appointmentModel->errors());
                    session()->setFlashdata('formData', $this->request->getPost());
                }
            }
        }

        $data = [
            'title' => 'Randevu Düzenle',
            'pageTitle' => 'Randevu Düzenle',
            'appointment' => $appointment,
            'customers' => $this->customerModel->getActiveCustomers($appointment['branch_id']),
            'services' => $this->serviceModel->getActiveServices($appointment['branch_id']),
            'staff' => $this->userModel->getStaffUsers($appointment['branch_id']),
            'formData' => session()->getFlashdata('formData') ?? $appointment,
            'userRole' => $userRole
        ];

        return view('calendar/edit', $data);
    }

    /**
     * Randevu durumu güncelleme (AJAX)
     */
    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        $appointment = $this->appointmentModel->getAppointmentDetail($id);
        if (!$appointment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Randevu bulunamadı']);
        }

        // Yetki kontrolü
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');
        $userId = session()->get('user_id');

        if ($userRole === 'staff' && $appointment['staff_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bu randevuyu güncelleme yetkiniz bulunmamaktadır.']);
        }

        if ($userRole !== 'admin' && $appointment['branch_id'] != $userBranchId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bu randevuya erişim yetkiniz bulunmamaktadır.']);
        }

        // Eski durumu sakla
        $oldStatus = $appointment['status'];

        if ($this->appointmentModel->updateStatus($id, $status, $notes)) {
            $message = 'Randevu durumu güncellendi';
            
            // Eğer durum "completed" olarak değiştirildi ise
            if ($status === 'completed' && $oldStatus !== 'completed') {
                
                // 1. Paket kullanımı varsa otomatik düşüm yap
                if (!empty($appointment['customer_package_id'])) {
                    $packageDeductionResult = $this->processPackageDeduction($appointment);
                    if ($packageDeductionResult['success']) {
                        $message .= '. ' . $packageDeductionResult['message'];
                    } else {
                        $message .= '. Uyarı: ' . $packageDeductionResult['message'];
                    }
                }
                
                // 2. Otomatik prim hesaplama
                $commissionResult = $this->processCommissionCalculation($appointment);
                if ($commissionResult['success']) {
                    $message .= '. ' . $commissionResult['message'];
                } else {
                    $message .= '. Prim Uyarısı: ' . $commissionResult['message'];
                }
                
                // 3. Paket uyarısı kontrolü
                try {
                    $this->triggerService->checkPackageWarning($id);
                } catch (\Exception $e) {
                    log_message('error', 'Paket uyarısı kontrolü hatası: ' . $e->getMessage());
                }
            }
            
            // Eğer durum "no_show" olarak değiştirildi ise
            if ($status === 'no_show' && $oldStatus !== 'no_show') {
                try {
                    $this->triggerService->scheduleNoShowNotification($id);
                } catch (\Exception $e) {
                    log_message('error', 'No-show bildirimi planlama hatası: ' . $e->getMessage());
                }
            }
            
            // Eğer durum "completed"dan başka bir duruma değiştirildi ise prim iptal et
            if ($oldStatus === 'completed' && $status !== 'completed') {
                $this->cancelCommission($appointment);
                $message .= '. Prim kaydı iptal edildi';
            }
            
            // In-app bildirim oluştur
            $this->createAppointmentNotification($id, 'status_changed', $status);
            
            return $this->response->setJSON(['success' => true, 'message' => $message]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Güncelleme sırasında bir hata oluştu']);
        }
    }

    /**
     * Paket düşümü işlemi
     */
    private function processPackageDeduction($appointment)
    {
        try {
            // Müşteri paket bilgisini al
            $customerPackage = $this->customerPackageModel->find($appointment['customer_package_id']);
            if (!$customerPackage) {
                return ['success' => false, 'message' => 'Müşteri paketi bulunamadı'];
            }

            // Paket durumu kontrolü
            if ($customerPackage['status'] !== 'active') {
                return ['success' => false, 'message' => 'Paket aktif değil'];
            }

            // Paket geçerlilik kontrolü
            if (strtotime($customerPackage['expiry_date']) < time()) {
                return ['success' => false, 'message' => 'Paket süresi dolmuş'];
            }

            // Hizmet bilgisini al
            $service = $this->serviceModel->find($appointment['service_id']);
            if (!$service) {
                return ['success' => false, 'message' => 'Hizmet bilgisi bulunamadı'];
            }

            // Paket türüne göre düşüm yap
            if ($customerPackage['remaining_sessions'] !== null) {
                // Seans bazlı paket
                if ($customerPackage['remaining_sessions'] <= 0) {
                    return ['success' => false, 'message' => 'Pakette kalan seans bulunmuyor'];
                }
                
                $result = $this->customerPackageModel->usePackage($appointment['customer_package_id'], 1, 0);
                if ($result) {
                    $remainingSessions = $customerPackage['remaining_sessions'] - 1;
                    $message = "Paketten 1 seans düşüldü (Kalan: {$remainingSessions} seans)";
                    
                    // Son seans uyarısı
                    if ($remainingSessions <= 1) {
                        $message .= " - Paket bitmek üzere!";
                    }
                    
                    return ['success' => true, 'message' => $message];
                } else {
                    return ['success' => false, 'message' => 'Paket düşümü yapılamadı'];
                }
            } elseif ($customerPackage['remaining_minutes'] !== null) {
                // Dakika bazlı paket
                $serviceDuration = $service['duration'];
                if ($customerPackage['remaining_minutes'] < $serviceDuration) {
                    return ['success' => false, 'message' => 'Pakette yeterli dakika bulunmuyor'];
                }
                
                $result = $this->customerPackageModel->usePackage($appointment['customer_package_id'], 0, $serviceDuration);
                if ($result) {
                    $remainingMinutes = $customerPackage['remaining_minutes'] - $serviceDuration;
                    $message = "Paketten {$serviceDuration} dakika düşüldü (Kalan: {$remainingMinutes} dakika)";
                    
                    // Son dakikalar uyarısı
                    if ($remainingMinutes <= 60) {
                        $message .= " - Paket bitmek üzere!";
                    }
                    
                    return ['success' => true, 'message' => $message];
                } else {
                    return ['success' => false, 'message' => 'Paket düşümü yapılamadı'];
                }
            } else {
                return ['success' => false, 'message' => 'Geçersiz paket türü'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Paket düşümü hatası: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Paket düşümü sırasında hata oluştu'];
        }
    }

    /**
     * Otomatik prim hesaplama motoru
     */
    private function processCommissionCalculation($appointment)
    {
        try {
            // Hizmet bilgisini al
            $service = $this->serviceModel->find($appointment['service_id']);
            if (!$service) {
                return ['success' => false, 'message' => 'Hizmet bilgisi bulunamadı'];
            }

            // Ödeme bilgisini al (eğer varsa)
            $payment = null;
            if (!empty($appointment['payment_id'])) {
                $payment = $this->paymentModel->find($appointment['payment_id']);
            }

            // Hizmet tutarını belirle (ödeme varsa ödeme tutarı, yoksa hizmet fiyatı)
            $serviceAmount = $payment ? $payment['amount'] : $appointment['price'];
            
            // Paketli hizmet mi kontrol et
            $isPackageService = !empty($appointment['customer_package_id']);

            // Prim hesapla ve kaydet
            $commissionId = $this->commissionModel->createCommissionForAppointment(
                $appointment['id'],
                $appointment['staff_id'],
                $appointment['service_id'],
                $serviceAmount,
                $appointment['branch_id'],
                $isPackageService,
                $appointment['payment_id'] ?? null
            );

            if ($commissionId) {
                $commissionAmount = $this->commissionModel->find($commissionId)['commission_amount'];
                return [
                    'success' => true,
                    'message' => "Prim hesaplandı: ₺" . number_format($commissionAmount, 2)
                ];
            } else {
                return ['success' => false, 'message' => 'Prim kuralı bulunamadı'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Prim hesaplama hatası: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Prim hesaplama sırasında hata oluştu'];
        }
    }

    /**
     * Prim iptal etme
     */
    private function cancelCommission($appointment)
    {
        try {
            // Randevuya ait prim kaydını bul ve iptal et
            $commission = $this->commissionModel->getCommissionByAppointment($appointment['id']);
            if ($commission) {
                $this->commissionModel->updateCommissionStatus(
                    $commission['id'],
                    'cancelled',
                    'Randevu durumu değiştirildiği için iptal edildi'
                );
            }
        } catch (\Exception $e) {
            log_message('error', 'Prim iptal hatası: ' . $e->getMessage());
        }
    }

    /**
     * Randevu zamanı/personeli güncelleme (AJAX - Sürükle-bırak)
     */
    public function updateAppointmentDragDrop()
    {
        $id = $this->request->getPost('id');
        $newDate = $this->request->getPost('appointment_date');
        $newStartTime = $this->request->getPost('start_time');
        $newDuration = $this->request->getPost('duration');
        $newStaffId = $this->request->getPost('staff_id'); // Personel değişikliği için

        $appointment = $this->appointmentModel->find($id);
        if (!$appointment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Randevu bulunamadı']);
        }

        // Yetki kontrolü
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($userRole === 'staff') {
            return $this->response->setJSON(['success' => false, 'message' => 'Randevu düzenleme yetkiniz bulunmamaktadır.']);
        }

        if ($userRole !== 'admin' && $appointment['branch_id'] != $userBranchId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bu randevuya erişim yetkiniz bulunmamaktadır.']);
        }

        // Bitiş saatini hesapla
        $newEndTime = date('H:i:s', strtotime($newStartTime . ' + ' . $newDuration . ' minutes'));
        
        // Personel değişikliği varsa kontrol et
        $targetStaffId = $newStaffId ?: $appointment['staff_id'];
        
        // Çakışma kontrolü (mevcut randevu hariç)
        if ($this->appointmentModel->checkConflict($targetStaffId, $newDate, $newStartTime, $newEndTime, $id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Seçilen tarih ve saatte personel müsait değil. Çakışma tespit edildi.',
                'conflict' => true
            ]);
        }

        // Güncelleme verilerini hazırla
        $updateData = [
            'appointment_date' => $newDate,
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
            'duration' => $newDuration
        ];

        // Personel değişikliği varsa ekle
        if ($newStaffId && $newStaffId != $appointment['staff_id']) {
            $updateData['staff_id'] = $newStaffId;
        }

        if ($this->appointmentModel->update($id, $updateData)) {
            // Randevu hatırlatma mesajlarını yeniden planla
            try {
                $this->triggerService->rescheduleAppointmentReminders($id);
            } catch (\Exception $e) {
                log_message('error', 'Randevu hatırlatma yeniden planlama hatası: ' . $e->getMessage());
            }
            
            // In-app bildirim oluştur
            $this->createAppointmentNotification($id, 'updated');
            
            $message = 'Randevu güncellendi';
            if ($newStaffId && $newStaffId != $appointment['staff_id']) {
                $newStaff = $this->userModel->find($newStaffId);
                $message .= ' ve ' . $newStaff['first_name'] . ' ' . $newStaff['last_name'] . ' personeline atandı';
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'appointment' => $this->appointmentModel->getAppointmentDetail($id)
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Güncelleme sırasında bir hata oluştu']);
        }
    }

    /**
     * Randevu kopyalama (AJAX)
     */
    public function copyAppointment()
    {
        $id = $this->request->getPost('id');
        $newDate = $this->request->getPost('new_date');
        $newTime = $this->request->getPost('new_time');

        $appointment = $this->appointmentModel->find($id);
        if (!$appointment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Randevu bulunamadı']);
        }

        // Yetki kontrolü
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($userRole === 'staff') {
            return $this->response->setJSON(['success' => false, 'message' => 'Randevu kopyalama yetkiniz bulunmamaktadır.']);
        }

        if ($userRole !== 'admin' && $appointment['branch_id'] != $userBranchId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bu randevuya erişim yetkiniz bulunmamaktadır.']);
        }

        // Yeni randevu verilerini hazırla
        $newAppointment = $appointment;
        unset($newAppointment['id']);
        $newAppointment['appointment_date'] = $newDate;
        $newAppointment['start_time'] = $newTime;
        $newAppointment['end_time'] = date('H:i:s', strtotime($newTime . ' + ' . $appointment['duration'] . ' minutes'));
        $newAppointment['created_by'] = session()->get('user_id');
        $newAppointment['status'] = 'confirmed';

        // Çakışma kontrolü
        if ($this->appointmentModel->checkConflict(
            $newAppointment['staff_id'],
            $newAppointment['appointment_date'],
            $newAppointment['start_time'],
            $newAppointment['end_time']
        )) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Seçilen tarih ve saatte personel müsait değil.'
            ]);
        }

        if ($this->appointmentModel->save($newAppointment)) {
            $newAppointmentId = $this->appointmentModel->getInsertID();
            
            // Yeni randevu için hatırlatma mesajlarını planla
            try {
                $this->triggerService->scheduleAppointmentReminders($newAppointmentId);
            } catch (\Exception $e) {
                log_message('error', 'Kopyalanan randevu hatırlatma planlama hatası: ' . $e->getMessage());
            }
            
            // In-app bildirim oluştur
            $this->createAppointmentNotification($newAppointmentId, 'created');
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Randevu başarıyla kopyalandı',
                'new_id' => $newAppointmentId
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Kopyalama sırasında bir hata oluştu']);
        }
    }

    /**
     * Toplu randevu işlemleri (AJAX)
     */
    public function bulkUpdate()
    {
        $appointmentIds = $this->request->getPost('appointment_ids');
        $action = $this->request->getPost('action');
        $value = $this->request->getPost('value');

        if (!$appointmentIds || !is_array($appointmentIds) || empty($appointmentIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Randevu seçilmedi']);
        }

        // Yetki kontrolü
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($userRole === 'staff') {
            return $this->response->setJSON(['success' => false, 'message' => 'Toplu işlem yetkiniz bulunmamaktadır.']);
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($appointmentIds as $appointmentId) {
            $appointment = $this->appointmentModel->find($appointmentId);
            if (!$appointment) {
                $errorCount++;
                $errors[] = "Randevu #$appointmentId bulunamadı";
                continue;
            }

            // Şube kontrolü
            if ($userRole !== 'admin' && $appointment['branch_id'] != $userBranchId) {
                $errorCount++;
                $errors[] = "Randevu #$appointmentId için yetkiniz bulunmamaktadır";
                continue;
            }

            // İşlem türüne göre güncelleme
            $updateData = [];
            switch ($action) {
                case 'status':
                    $updateData['status'] = $value;
                    break;
                case 'staff':
                    $updateData['staff_id'] = $value;
                    break;
                case 'delete':
                    if ($this->appointmentModel->delete($appointmentId)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Randevu #$appointmentId silinemedi";
                    }
                    continue 2;
                default:
                    $errorCount++;
                    $errors[] = "Geçersiz işlem türü";
                    continue 2;
            }

            if ($this->appointmentModel->update($appointmentId, $updateData)) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Randevu #$appointmentId güncellenemedi";
            }
        }

        return $this->response->setJSON([
            'success' => $successCount > 0,
            'message' => "$successCount randevu başarıyla işlendi" . ($errorCount > 0 ? ", $errorCount hatada hata oluştu" : ""),
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors
        ]);
    }

    /**
     * Randevu silme (AJAX)
     */
    public function delete($id)
    {
        $appointment = $this->appointmentModel->find($id);
        if (!$appointment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Randevu bulunamadı']);
        }

        // Yetki kontrolü
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($userRole === 'staff') {
            return $this->response->setJSON(['success' => false, 'message' => 'Randevu silme yetkiniz bulunmamaktadır.']);
        }

        if ($userRole !== 'admin' && $appointment['branch_id'] != $userBranchId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bu randevuya erişim yetkiniz bulunmamaktadır.']);
        }

        try {
            // Randevuya bağlı bekleyen mesajları iptal et
            try {
                $this->triggerService->cancelAppointmentReminders($id);
            } catch (\Exception $e) {
                log_message('error', 'Randevu mesajları iptal hatası: ' . $e->getMessage());
            }
            
            if ($this->appointmentModel->delete($id)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Randevu başarıyla silindi']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Silme işlemi başarısız']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Personel müsaitlik kontrolü (AJAX)
     */
    public function checkAvailability()
    {
        $staffId = $this->request->getPost('staff_id');
        $date = $this->request->getPost('date');
        $startTime = $this->request->getPost('start_time');
        $duration = $this->request->getPost('duration');
        $excludeId = $this->request->getPost('exclude_id');

        $endTime = date('H:i:s', strtotime($startTime . ' + ' . $duration . ' minutes'));
        $isAvailable = $this->appointmentModel->isStaffAvailable($staffId, $date, $startTime, $endTime, $excludeId);

        return $this->response->setJSON([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Personel müsait' : 'Personel bu saatte müsait değil'
        ]);
    }

    /**
     * Hizmete göre personel listesi (AJAX)
     */
    public function getServiceStaff()
    {
        $serviceId = $this->request->getGet('service_id');
        $branchId = $this->request->getGet('branch_id');

        $staff = $this->userModel->getServiceStaff($serviceId, $branchId);

        return $this->response->setJSON($staff);
    }

    /**
     * Müşteri arama (AJAX)
     */
    public function searchCustomers()
    {
        $query = $this->request->getGet('q');
        $branchId = $this->request->getGet('branch_id');
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Rol bazlı şube kontrolü
        if ($userRole !== 'admin') {
            $branchId = $userBranchId;
        }

        if (strlen($query) < 2) {
            return $this->response->setJSON([]);
        }

        $customers = $this->customerModel->searchCustomers($query, $branchId);
        
        return $this->response->setJSON($customers);
    }

    /**
     * Hızlı müşteri ekleme (AJAX)
     */
    public function quickAddCustomer()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Personel müşteri ekleyemez
        if ($userRole === 'staff') {
            return $this->response->setJSON(['success' => false, 'message' => 'Müşteri ekleme yetkiniz bulunmamaktadır.']);
        }

        $data = [
            'branch_id' => $this->request->getPost('branch_id') ?: $userBranchId,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'status' => 'active'
        ];

        if ($this->customerModel->save($data)) {
            $customerId = $this->customerModel->getInsertID();
            $customer = $this->customerModel->find($customerId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Müşteri başarıyla eklendi.',
                'customer' => $customer
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Müşteri eklenirken hata oluştu.',
                'errors' => $this->customerModel->errors()
            ]);
        }
    }

    /**
     * Müşterinin mevcut paketlerini getir (AJAX)
     */
    public function getCustomerPackages()
    {
        $customerId = $this->request->getGet('customer_id');
        $serviceId = $this->request->getGet('service_id');

        if (!$customerId) {
            return $this->response->setJSON([]);
        }

        if ($serviceId) {
            // Belirli hizmet için kullanılabilir paketler
            $packages = $this->customerPackageModel->getAvailablePackagesForService($customerId, $serviceId);
        } else {
            // Tüm aktif paketler
            $packages = $this->customerPackageModel->getActivePackages($customerId);
        }

        return $this->response->setJSON($packages);
    }

    /**
     * Akıllı personel önerisi (AJAX)
     */
    public function getSuggestedStaff()
    {
        $serviceId = $this->request->getGet('service_id');
        $packageId = $this->request->getGet('package_id');
        $branchId = $this->request->getGet('branch_id');
        $date = $this->request->getGet('date');
        $time = $this->request->getGet('time');
        $duration = $this->request->getGet('duration');

        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Rol bazlı şube kontrolü
        if ($userRole !== 'admin') {
            $branchId = $userBranchId;
        }

        $staff = [];

        if ($serviceId) {
            // Normal hizmet seçildi
            $staff = $this->userModel->getServiceStaff($serviceId, $branchId);
        } elseif ($packageId) {
            // Paket seçildi - paketin içindeki hizmetlere göre personel getir
            $staff = $this->userModel->getPackageStaff($packageId, $branchId);
        } else {
            // Hiçbir şey seçilmedi - tüm personelleri getir
            $staff = $this->userModel->getStaffUsers($branchId);
        }

        // Müsaitlik durumlarını kontrol et
        if ($date && $time && $duration) {
            $endTime = date('H:i:s', strtotime($time . ' + ' . $duration . ' minutes'));
            
            foreach ($staff as &$person) {
                $person['available'] = $this->appointmentModel->isStaffAvailable(
                    $person['id'],
                    $date,
                    $time,
                    $endTime
                );
            }

            // Müsait olanları önce sırala
            usort($staff, function($a, $b) {
                if ($a['available'] && !$b['available']) return -1;
                if (!$a['available'] && $b['available']) return 1;
                return 0;
            });
        }

        return $this->response->setJSON($staff);
    }

    /**
     * Uygun zaman aralıklarını getir (AJAX)
     */
    public function getAvailableTimeSlots()
    {
        $staffId = $this->request->getGet('staff_id');
        $date = $this->request->getGet('date');
        $duration = $this->request->getGet('duration');

        if (!$staffId || !$date || !$duration) {
            return $this->response->setJSON([]);
        }

        $timeSlots = $this->appointmentModel->getAvailableTimeSlots($staffId, $date, $duration);

        return $this->response->setJSON($timeSlots);
    }

    /**
     * Tekrar eden randevu oluşturma
     */
    public function createRecurring()
    {
        $userRole = session()->get('role_name');
        
        // Personel tekrar eden randevu oluşturamaz
        if ($userRole === 'staff') {
            return $this->response->setJSON(['success' => false, 'message' => 'Tekrar eden randevu oluşturma yetkiniz bulunmamaktadır.']);
        }

        $baseData = [
            'branch_id' => $this->request->getPost('branch_id'),
            'customer_id' => $this->request->getPost('customer_id'),
            'service_id' => $this->request->getPost('service_id'),
            'staff_id' => $this->request->getPost('staff_id'),
            'start_time' => $this->request->getPost('start_time'),
            'duration' => $this->request->getPost('duration'),
            'price' => $this->request->getPost('price'),
            'notes' => $this->request->getPost('notes'),
            'created_by' => session()->get('user_id'),
            'status' => 'confirmed',
            'type' => 'recurring'
        ];

        $recurringType = $this->request->getPost('recurring_type'); // weekly, biweekly, monthly
        $recurringCount = (int)$this->request->getPost('recurring_count'); // kaç kez tekrarlanacak
        $startDate = $this->request->getPost('start_date');

        $createdAppointments = [];
        $errors = [];

        for ($i = 0; $i < $recurringCount; $i++) {
            $appointmentDate = $this->calculateRecurringDate($startDate, $recurringType, $i);
            
            $appointmentData = $baseData;
            $appointmentData['appointment_date'] = $appointmentDate;
            $appointmentData['end_time'] = date('H:i:s', strtotime($baseData['start_time'] . ' + ' . $baseData['duration'] . ' minutes'));

            // Çakışma kontrolü
            if (!$this->appointmentModel->checkConflict(
                $appointmentData['staff_id'],
                $appointmentData['appointment_date'],
                $appointmentData['start_time'],
                $appointmentData['end_time']
            )) {
                if ($this->appointmentModel->save($appointmentData)) {
                    $appointmentId = $this->appointmentModel->getInsertID();
                    $createdAppointments[] = $appointmentDate;
                    
                    // Her randevu için hatırlatma mesajlarını planla
                    try {
                        $this->triggerService->scheduleAppointmentReminders($appointmentId);
                    } catch (\Exception $e) {
                        log_message('error', 'Tekrar eden randevu hatırlatma planlama hatası: ' . $e->getMessage());
                    }
                    
                    // İlk randevu için bildirim oluştur (diğerleri için spam olmasın)
                    if ($i === 0) {
                        $this->createAppointmentNotification($appointmentId, 'created');
                    }
                } else {
                    $errors[] = $appointmentDate . ' - ' . implode(', ', $this->appointmentModel->errors());
                }
            } else {
                $errors[] = $appointmentDate . ' - Personel müsait değil';
            }
        }

        return $this->response->setJSON([
            'success' => count($createdAppointments) > 0,
            'created_count' => count($createdAppointments),
            'total_count' => $recurringCount,
            'created_appointments' => $createdAppointments,
            'errors' => $errors
        ]);
    }

    /**
     * Tekrar eden randevu tarihini hesapla
     */
    private function calculateRecurringDate($startDate, $type, $iteration)
    {
        $date = new \DateTime($startDate);
        
        switch ($type) {
            case 'weekly':
                $date->add(new \DateInterval('P' . ($iteration * 7) . 'D'));
                break;
            case 'biweekly':
                $date->add(new \DateInterval('P' . ($iteration * 14) . 'D'));
                break;
            case 'monthly':
                $date->add(new \DateInterval('P' . $iteration . 'M'));
                break;
        }
        
        return $date->format('Y-m-d');
    }

    /**
     * Randevu işlemleri için in-app bildirim oluştur
     */
    private function createAppointmentNotification($appointmentId, $action, $status = null)
    {
        try {
            // Randevu detaylarını al
            $appointment = $this->appointmentModel->getAppointmentDetail($appointmentId);
            if (!$appointment) {
                return false;
            }

            // Müşteri ve personel bilgilerini al
            $customer = $this->customerModel->find($appointment['customer_id']);
            $staff = $this->userModel->find($appointment['staff_id']);
            $service = $this->serviceModel->find($appointment['service_id']);

            if (!$customer || !$staff || !$service) {
                return false;
            }

            $customerName = $customer['first_name'] . ' ' . $customer['last_name'];
            $staffName = $staff['first_name'] . ' ' . $staff['last_name'];
            $serviceName = $service['name'];
            $appointmentDate = date('d.m.Y', strtotime($appointment['appointment_date']));
            $appointmentTime = date('H:i', strtotime($appointment['start_time']));

            // Bildirim içeriğini hazırla
            $title = '';
            $message = '';
            $type = 'info';

            switch ($action) {
                case 'created':
                    $title = 'Yeni Randevu Oluşturuldu';
                    $message = "{$customerName} için {$appointmentDate} {$appointmentTime} tarihinde {$serviceName} randevusu oluşturuldu. Personel: {$staffName}";
                    $type = 'success';
                    break;

                case 'updated':
                    $title = 'Randevu Güncellendi';
                    $message = "{$customerName} müşterisinin {$serviceName} randevusu güncellendi. Yeni tarih: {$appointmentDate} {$appointmentTime}";
                    $type = 'info';
                    break;

                case 'status_changed':
                    $statusTexts = [
                        'pending' => 'Onay Bekliyor',
                        'confirmed' => 'Onaylandı',
                        'completed' => 'Tamamlandı',
                        'cancelled' => 'İptal Edildi',
                        'no_show' => 'Gelmedi'
                    ];
                    $statusText = $statusTexts[$status] ?? $status;
                    $title = 'Randevu Durumu Değişti';
                    $message = "{$customerName} müşterisinin {$serviceName} randevusu '{$statusText}' olarak işaretlendi.";
                    $type = $status === 'completed' ? 'success' : ($status === 'cancelled' || $status === 'no_show' ? 'warning' : 'info');
                    break;

                default:
                    return false;
            }

            // Şube personeline bildirim gönder (randevu oluşturan hariç)
            $currentUserId = session()->get('user_id');
            $this->notificationModel->sendBulkNotification(
                $appointment['branch_id'],
                $title,
                $message,
                $type,
                'appointment',
                $appointmentId,
                $currentUserId
            );

            return true;

        } catch (\Exception $e) {
            log_message('error', 'Randevu bildirimi oluşturma hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Müşterinin bekleyen ödemeli randevularını getir
     */
    public function getPendingPayments($customerId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $appointments = $this->appointmentModel
            ->select('appointments.*, services.name as service_name')
            ->join('services', 'services.id = appointments.service_id')
            ->where([
                'appointments.customer_id' => $customerId,
                'appointments.payment_status' => ['pending', 'partial']
            ])
            ->where('appointments.price > appointments.paid_amount')
            ->findAll();

        return $this->response->setJSON($appointments);
    }
}