<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserSettingModel;
use App\Models\InAppNotificationModel;

class Profile extends BaseController
{
    protected $userModel;
    protected $userSettingModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userSettingModel = new UserSettingModel();
        $this->notificationModel = new InAppNotificationModel();
    }

    /**
     * Profil sayfası
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->getUserDetail($userId);
        
        if (!$user) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Profil Ayarları',
            'pageTitle' => 'Profil Ayarları',
            'breadcrumb' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Profil Ayarları']
            ],
            'user' => $user
        ];

        return view('profile/index', $data);
    }

    /**
     * Profil güncelleme
     */
    public function update()
    {
        $userId = session()->get('user_id');
        
        $rules = [
            'first_name' => 'required|string|min_length[2]|max_length[100]',
            'last_name' => 'required|string|min_length[2]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'phone' => "required|string|max_length[20]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone')
        ];

        if ($this->userModel->update($userId, $data)) {
            // Session'daki kullanıcı bilgilerini güncelle
            session()->set([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email']
            ]);

            return redirect()->to('/profile')->with('success', 'Profil bilgileriniz başarıyla güncellendi.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Profil güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Şifre değiştirme sayfası
     */
    public function changePassword()
    {
        $data = [
            'title' => 'Şifre Değiştir',
            'pageTitle' => 'Şifre Değiştir',
            'breadcrumb' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Profil Ayarları', 'url' => '/profile'],
                ['title' => 'Şifre Değiştir']
            ]
        ];

        return view('profile/change_password', $data);
    }

    /**
     * Şifre güncelleme
     */
    public function updatePassword()
    {
        $userId = session()->get('user_id');
        
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->find($userId);
        
        // Mevcut şifreyi kontrol et
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Mevcut şifreniz yanlış.');
        }

        // Yeni şifreyi güncelle
        $newPassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        
        if ($this->userModel->update($userId, ['password' => $newPassword])) {
            return redirect()->to('/profile')->with('success', 'Şifreniz başarıyla değiştirildi.');
        } else {
            return redirect()->back()->with('error', 'Şifre değiştirilirken bir hata oluştu.');
        }
    }

    /**
     * Genel ayarlar sayfası
     */
    public function settings()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return redirect()->to('/login');
        }
        
        $userSettings = $this->userSettingModel->getUserSettingsWithDefaults($userId);

        $data = [
            'title' => 'Genel Ayarlar',
            'pageTitle' => 'Genel Ayarlar',
            'breadcrumb' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Genel Ayarlar']
            ],
            'settings' => $userSettings
        ];

        return view('profile/settings', $data);
    }

    /**
     * Ayarları güncelle
     */
    public function updateSettings()
    {
        $userId = session()->get('user_id');
        
        $settings = [
            'theme_mode' => $this->request->getPost('theme_mode') ?: 'light',
            'notifications_enabled' => $this->request->getPost('notifications_enabled') ? '1' : '0',
            'notification_sound' => $this->request->getPost('notification_sound') ? '1' : '0',
            'notification_desktop' => $this->request->getPost('notification_desktop') ? '1' : '0',
            'notification_email' => $this->request->getPost('notification_email') ? '1' : '0'
        ];

        if ($this->userSettingModel->setUserSettings($userId, $settings)) {
            // Session'a tema ayarını kalıcı olarak kaydet
            session()->set('theme_mode', $settings['theme_mode']);
            
            // Test sayfasından geliyorsa test sayfasına yönlendir
            $referer = $this->request->getServer('HTTP_REFERER');
            if (strpos($referer, 'test-theme') !== false) {
                return redirect()->to('/test-theme')->with('success', 'Tema başarıyla değiştirildi.');
            }
            
            return redirect()->to('/profile/settings')->with('success', 'Ayarlarınız başarıyla güncellendi.');
        } else {
            return redirect()->back()->with('error', 'Ayarlar güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Bildirimler sayfası
     */
    public function notifications()
    {
        $userId = session()->get('user_id');
        $notifications = $this->notificationModel->getUserNotifications($userId, 50);

        $data = [
            'title' => 'Bildirimler',
            'pageTitle' => 'Bildirimler',
            'breadcrumb' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Bildirimler']
            ],
            'notifications' => $notifications
        ];

        return view('profile/notifications', $data);
    }

    /**
     * Bildirimi okundu olarak işaretle (AJAX)
     */
    public function markNotificationRead()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Geçersiz istek']);
        }

        $userId = session()->get('user_id');
        $notificationId = $this->request->getPost('notification_id');

        if ($this->notificationModel->markAsRead($notificationId, $userId)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Bildirim güncellenemedi']);
        }
    }

    /**
     * Tüm bildirimleri okundu olarak işaretle (AJAX)
     */
    public function markAllNotificationsRead()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Geçersiz istek']);
        }

        $userId = session()->get('user_id');

        if ($this->notificationModel->markAllAsRead($userId)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Bildirimler güncellenemedi']);
        }
    }

    /**
     * Okunmamış bildirim sayısını getir (AJAX)
     */
    public function getUnreadCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Geçersiz istek']);
        }

        $userId = session()->get('user_id');
        $count = $this->notificationModel->getUnreadCount($userId);

        return $this->response->setJSON(['success' => true, 'count' => $count]);
    }

    /**
     * Son bildirimleri getir (AJAX)
     */
    public function getRecentNotifications()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Geçersiz istek']);
        }

        $userId = session()->get('user_id');
        $notifications = $this->notificationModel->getUnreadNotifications($userId, 5);

        return $this->response->setJSON(['success' => true, 'notifications' => $notifications]);
    }

    /**
     * Test bildirimi gönder (AJAX)
     */
    public function sendTestNotification()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Geçersiz istek']);
        }

        $userId = session()->get('user_id');
        
        // Test bildirimi oluştur
        $notificationData = [
            'user_id' => $userId,
            'title' => 'Test Bildirimi',
            'message' => 'Bu bir test bildirimidir. Bildirim sisteminiz düzgün çalışıyor!',
            'type' => 'info',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->notificationModel->insert($notificationData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Test bildirimi başarıyla gönderildi!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test bildirimi gönderilemedi.'
            ]);
        }
    }
}