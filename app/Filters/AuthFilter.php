<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Kullanıcının giriş yapıp yapmadığını kontrol eder
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // After işlemi gerekmiyor
    }
}