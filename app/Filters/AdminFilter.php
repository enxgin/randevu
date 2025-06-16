<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    /**
     * Admin rolü kontrolü
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Bu sayfaya erişmek için giriş yapmanız gerekiyor.');
            return redirect()->to('/login');
        }

        // Kullanıcı Admin veya Manager değilse yetkisiz erişim sayfasına yönlendir
        $allowedRoles = ['admin', 'manager'];
        if (!in_array(session()->get('role_name'), $allowedRoles)) {
            session()->setFlashdata('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            return redirect()->to('/unauthorized');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // After metodu boş bırakılabilir
    }
}