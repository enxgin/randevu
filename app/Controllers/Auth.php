<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserSettingModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $userSettingModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userSettingModel = new UserSettingModel();
    }

    /**
     * Giriş sayfası
     */
    public function login()
    {
        // Eğer kullanıcı zaten giriş yapmışsa dashboard'a yönlendir
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Giriş Yap'
        ];

        if ($this->request->getMethod() === 'POST') {
            return $this->processLogin();
        }

        return view('auth/login', $data);
    }

    /**
     * Giriş işlemini gerçekleştir
     */
    private function processLogin()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ], [
            'email' => [
                'required' => 'E-posta adresi gereklidir.',
                'valid_email' => 'Geçerli bir e-posta adresi giriniz.'
            ],
            'password' => [
                'required' => 'Şifre gereklidir.',
                'min_length' => 'Şifre en az 6 karakter olmalıdır.'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            session()->setFlashdata('error', 'Girilen bilgileri kontrol ediniz.');
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Kullanıcıyı veritabanından bul
        $user = $this->userModel->getUserWithBranchAndRole($email);

        if (!$user) {
            session()->setFlashdata('error', 'E-posta adresi veya şifre hatalı.');
            return redirect()->back()->withInput();
        }

        // Şifre kontrolü
        if (!password_verify($password, $user['password'])) {
            session()->setFlashdata('error', 'E-posta adresi veya şifre hatalı.');
            return redirect()->back()->withInput();
        }

        // Kullanıcı aktif mi kontrol et
        if (!$user['is_active']) {
            session()->setFlashdata('error', 'Hesabınız aktif değil. Yöneticinizle iletişime geçiniz.');
            return redirect()->back()->withInput();
        }

        // Kullanıcının tema ayarını al
        $userSettings = $this->userSettingModel->getUserSettingsWithDefaults($user['id']);
        
        // Mevcut session'da tema ayarı varsa onu koru, yoksa veritabanından al
        $currentTheme = session()->get('theme_mode');
        $themeMode = $currentTheme ?: $userSettings['theme_mode'];
        
        // Başarılı giriş - Session verilerini kaydet
        $sessionData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'],
            'branch_id' => $user['branch_id'],
            'branch_name' => $user['branch_name'],
            'theme_mode' => $themeMode,
            'is_logged_in' => true
        ];

        session()->set($sessionData);
        
        // Son giriş tarihini güncelle
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Başarıyla giriş yaptınız. Hoş geldiniz!');

        // Kullanıcının rolüne göre yönlendirme
        if ($user['role_name'] === 'admin') {
            return redirect()->to('/admin/dashboard');
        } else {
            return redirect()->to('/dashboard');
        }
    }

    /**
     * Ana sayfa yönlendirme
     */
    public function redirectHome()
    {
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }
        
        // Giriş yapmışsa dashboard'a yönlendir
        return redirect()->to('/dashboard');
    }

    /**
     * Çıkış işlemi
     */
    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('success', 'Başarıyla çıkış yaptınız.');
        return redirect()->to('/login');
    }

    /**
     * Yetkisiz erişim sayfası
     */
    public function unauthorized()
    {
        $data = [
            'title' => 'Yetkisiz Erişim'
        ];

        return view('auth/unauthorized', $data);
    }
}