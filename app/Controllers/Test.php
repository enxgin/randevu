<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class Test extends BaseController
{
    public function checkSession()
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();
        
        echo "<h2>Session Verileri:</h2>";
        echo "<pre>";
        print_r(session()->get());
        echo "</pre>";
        
        echo "<h2>Roller:</h2>";
        echo "<pre>";
        print_r($roleModel->findAll());
        echo "</pre>";
        
        echo "<h2>Admin Kullanıcısı:</h2>";
        $admin = $userModel->getUserWithBranchAndRole('admin@beautypro.com');
        echo "<pre>";
        print_r($admin);
        echo "</pre>";
    }
}