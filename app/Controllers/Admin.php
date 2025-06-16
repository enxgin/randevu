<?php

namespace App\Controllers;

use App\Models\BranchModel;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\UserModel;
use App\Models\CustomerModel; // Müşteri modelini ekledik
use App\Models\ServiceCategoryModel;
use App\Models\ServiceModel;
use App\Models\ServiceStaffModel;
use App\Models\PackageModel;
use App\Models\PackageServiceModel;
use App\Models\CustomerPackageModel;
use App\Models\PaymentModel;
use App\Models\SentMessageModel;
use App\Models\InAppNotificationModel;

class Admin extends BaseController
{
    protected $branchModel;
    protected $roleModel;
    protected $permissionModel;
    protected $userModel;
    protected $customerModel; // Müşteri modelini tanımladık
    protected $serviceCategoryModel;
    protected $serviceModel;
    protected $serviceStaffModel;
    protected $packageModel;
    protected $packageServiceModel;
    protected $customerPackageModel;
    protected $paymentModel;
    protected $sentMessageModel;
    protected $inAppNotificationModel;

    public function __construct()
    {
        // Admin yetkisi kontrolü (şimdilik basit, sonraki adımlarda geliştirilecek)
        // if (!session()->get('role') || session()->get('role') !== 'admin') {
        //     throw new \CodeIgniter\Exceptions\PageNotFoundException('Erişim reddedildi');
        // }
        
        $this->branchModel = new BranchModel();
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->userModel = new UserModel();
        $this->customerModel = new CustomerModel(); // Müşteri modelini başlattık
        $this->serviceCategoryModel = new ServiceCategoryModel();
        $this->serviceModel = new ServiceModel();
        $this->serviceStaffModel = new ServiceStaffModel();
        $this->packageModel = new PackageModel();
        $this->packageServiceModel = new PackageServiceModel();
        $this->customerPackageModel = new CustomerPackageModel();
        $this->paymentModel = new PaymentModel();
        $this->sentMessageModel = new SentMessageModel();
        $this->inAppNotificationModel = new InAppNotificationModel();
    }

    public function test()
    {
        return 'Admin controller çalışıyor!';
    }

    public function index()
    {
        $data = [
            'title' => 'Admin Paneli',
            'pageTitle' => 'Admin Paneli',
            'stats' => [
                'branches' => $this->branchModel->countAllResults(),
                'roles' => $this->roleModel->countAllResults(),
                'permissions' => $this->permissionModel->countAllResults(),
                'users' => $this->userModel->countAllResults()
            ]
        ];

        return view('admin/dashboard', $data);
    }

    // ===================== ŞUBE YÖNETİMİ =====================

    public function branches()
    {
        // Sadece admin erişebilir
        if (session()->get('role_name') !== 'admin') {
            session()->setFlashdata('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Şube Yönetimi',
            'pageTitle' => 'Şube Yönetimi',
            'branches' => $this->branchModel->getBranchesWithUserCount()
        ];

        return view('admin/branches/index', $data);
    }

    public function createBranch()
    {
        if ($this->request->getMethod() === 'POST') {
            // Çalışma saatleri JSON formatında
            $workingHours = [];
            $days = ['pazartesi', 'sali', 'carsamba', 'persembe', 'cuma', 'cumartesi', 'pazar'];
            foreach ($days as $day) {
                $isWorking = $this->request->getPost($day . '_working');
                $startTime = $this->request->getPost($day . '_start');
                $endTime = $this->request->getPost($day . '_end');
                
                $workingHours[$day] = [
                    'is_working' => $isWorking ? true : false,
                    'start_time' => $isWorking ? $startTime : null,
                    'end_time' => $isWorking ? $endTime : null
                ];
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'address' => $this->request->getPost('address'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'working_hours' => $workingHours,
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            if ($this->branchModel->save($data)) {
                session()->setFlashdata('success', 'Şube başarıyla oluşturuldu.');
                return redirect()->to('/admin/branches');
            } else {
                session()->setFlashdata('errors', $this->branchModel->errors());
            }
        }

        $data = [
            'title' => 'Yeni Şube Oluştur',
            'pageTitle' => 'Yeni Şube Oluştur'
        ];

        return view('admin/branches/create', $data);
    }

    public function editBranch($id)
    {
        $branch = $this->branchModel->find($id);
        if (!$branch) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Şube bulunamadı');
        }

        if ($this->request->getMethod() === 'POST') {
            // Çalışma saatleri JSON formatında
            $workingHours = [];
            $days = ['pazartesi', 'sali', 'carsamba', 'persembe', 'cuma', 'cumartesi', 'pazar'];
            foreach ($days as $day) {
                $isWorking = $this->request->getPost($day . '_working');
                $startTime = $this->request->getPost($day . '_start');
                $endTime = $this->request->getPost($day . '_end');
                
                $workingHours[$day] = [
                    'is_working' => $isWorking ? true : false,
                    'start_time' => $isWorking ? $startTime : null,
                    'end_time' => $isWorking ? $endTime : null
                ];
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'address' => $this->request->getPost('address'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'working_hours' => $workingHours,
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Güncelleme için özel validation kuralları
            $this->branchModel->setValidationRules([
                'name' => "required|max_length[255]|is_unique[branches.name,id,{$id}]",
                'email' => 'permit_empty|valid_email',
                'phone' => 'permit_empty|max_length[20]'
            ]);

            if ($this->branchModel->update($id, $data)) {
                session()->setFlashdata('success', 'Şube başarıyla güncellendi.');
                return redirect()->to('/admin/branches');
            } else {
                session()->setFlashdata('errors', $this->branchModel->errors());
            }
        }

        $data = [
            'title' => 'Şube Düzenle',
            'pageTitle' => 'Şube Düzenle',
            'branch' => $branch
        ];

        return view('admin/branches/edit', $data);
    }

    public function deleteBranch($id)
    {
        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return $this->response->setJSON(['success' => false, 'message' => 'Şube bulunamadı']);
        }

        try {
            $this->branchModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Şube başarıyla silindi']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===================== ROL YÖNETİMİ =====================

    public function roles()
    {
        // Sadece admin erişebilir
        if (session()->get('role_name') !== 'admin') {
            session()->setFlashdata('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Rol Yönetimi',
            'pageTitle' => 'Rol Yönetimi',
            'roles' => $this->roleModel->getRolesWithUserCount()
        ];

        return view('admin/roles/index', $data);
    }

    public function createRole()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'display_name' => $this->request->getPost('display_name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            if ($this->roleModel->save($data)) {
                $roleId = $this->roleModel->getInsertID();
                $permissions = $this->request->getPost('permissions') ?? [];
                $this->roleModel->assignPermissions($roleId, $permissions);
                
                session()->setFlashdata('success', 'Rol başarıyla oluşturuldu.');
                return redirect()->to('/admin/roles');
            } else {
                session()->setFlashdata('errors', $this->roleModel->errors());
            }
        }

        $data = [
            'title' => 'Yeni Rol Oluştur',
            'pageTitle' => 'Yeni Rol Oluştur',
            'permissions' => $this->permissionModel->getPermissionsByCategory()
        ];

        return view('admin/roles/create', $data);
    }

    public function editRole($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Rol bulunamadı');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'display_name' => $this->request->getPost('display_name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Güncelleme için özel validation kuralları
            $this->roleModel->setValidationRules([
                'name' => "required|max_length[100]|is_unique[roles.name,id,{$id}]",
                'display_name' => 'required|max_length[255]'
            ]);

            if ($this->roleModel->update($id, $data)) {
                $permissions = $this->request->getPost('permissions') ?? [];
                $this->roleModel->assignPermissions($id, $permissions);
                
                session()->setFlashdata('success', 'Rol başarıyla güncellendi.');
                return redirect()->to('/admin/roles');
            } else {
                session()->setFlashdata('errors', $this->roleModel->errors());
            }
        }

        $data = [
            'title' => 'Rol Düzenle',
            'pageTitle' => 'Rol Düzenle',
            'role' => $role,
            'permissions' => $this->permissionModel->getPermissionsByCategory(),
            'rolePermissions' => array_column($this->roleModel->getRolePermissions($id), 'id')
        ];

        return view('admin/roles/edit', $data);
    }

    public function deleteRole($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rol bulunamadı']);
        }

        try {
            $this->roleModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Rol başarıyla silindi']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===================== İZİN YÖNETİMİ =====================

    public function permissions()
    {
        // Sadece admin erişebilir
        if (session()->get('role_name') !== 'admin') {
            session()->setFlashdata('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'İzin Yönetimi',
            'pageTitle' => 'İzin Yönetimi',
            'permissions' => $this->permissionModel->getPermissionsWithRoles(),
            'categories' => $this->permissionModel->getCategories()
        ];

        return view('admin/permissions/index', $data);
    }

    public function createPermission()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'display_name' => $this->request->getPost('display_name'),
                'category' => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            if ($this->permissionModel->save($data)) {
                session()->setFlashdata('success', 'İzin başarıyla oluşturuldu.');
                return redirect()->to('/admin/permissions');
            } else {
                session()->setFlashdata('errors', $this->permissionModel->errors());
            }
        }

        $data = [
            'title' => 'Yeni İzin Oluştur',
            'pageTitle' => 'Yeni İzin Oluştur',
            'categories' => $this->permissionModel->getCategories()
        ];

        return view('admin/permissions/create', $data);
    }

    public function editPermission($id)
    {
        $permission = $this->permissionModel->find($id);
        if (!$permission) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('İzin bulunamadı');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'display_name' => $this->request->getPost('display_name'),
                'category' => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            if ($this->permissionModel->update($id, $data)) {
                session()->setFlashdata('success', 'İzin başarıyla güncellendi.');
                return redirect()->to('/admin/permissions');
            } else {
                session()->setFlashdata('errors', $this->permissionModel->errors());
            }
        }

        $data = [
            'title' => 'İzin Düzenle',
            'pageTitle' => 'İzin Düzenle',
            'permission' => $permission,
            'categories' => $this->permissionModel->getCategories()
        ];

        return view('admin/permissions/edit', $data);
    }

    public function deletePermission($id)
    {
        $permission = $this->permissionModel->find($id);
        if (!$permission) {
            return $this->response->setJSON(['success' => false, 'message' => 'İzin bulunamadı']);
        }

        try {
            $this->permissionModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'İzin başarıyla silindi']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===================== KULLANICI YÖNETİMİ =====================

    public function users()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Kullanıcı listesi (rol bazlı filtreleme)
        if ($userRole === 'admin') {
            $users = $this->userModel->getUsersWithDetails();
        } else {
            // Manager sadece kendi şubesindeki kullanıcıları görebilir
            $users = $this->userModel->getUsersWithDetails($userBranchId);
        }

        $data = [
            'title' => 'Kullanıcı Yönetimi',
            'pageTitle' => 'Kullanıcı Yönetimi',
            'users' => $users,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/users/index', $data);
    }

    public function createUser()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'branch_id' => $this->request->getPost('branch_id'),
                'role_id' => $this->request->getPost('role_id'),
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'phone' => $this->request->getPost('phone'),
                'commission_rate' => $this->request->getPost('commission_rate'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Manager sadece kendi şubesine kullanıcı ekleyebilir
            if ($userRole === 'manager' && $data['branch_id'] != $userBranchId) {
                session()->setFlashdata('error', 'Sadece kendi şubenize kullanıcı ekleyebilirsiniz.');
                return redirect()->back()->withInput();
            }

            // Çalışma saatleri JSON formatında
            $workingHours = [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                $start = $this->request->getPost($day . '_start');
                $end = $this->request->getPost($day . '_end');
                $isWorking = $this->request->getPost($day . '_working');
                
                $workingHours[$day] = [
                    'is_working' => $isWorking ? true : false,
                    'start_time' => $start,
                    'end_time' => $end
                ];
            }
            $data['working_hours'] = json_encode($workingHours);

            if ($this->userModel->save($data)) {
                session()->setFlashdata('success', 'Kullanıcı başarıyla oluşturuldu.');
                return redirect()->to('/admin/users');
            } else {
                session()->setFlashdata('errors', $this->userModel->errors());
            }
        }

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        $data = [
            'title' => 'Yeni Kullanıcı Oluştur',
            'pageTitle' => 'Yeni Kullanıcı Oluştur',
            'branches' => $branches,
            'roles' => $this->roleModel->getActiveRoles(),
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/users/create', $data);
    }

    public function editUser($id)
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kullanıcı bulunamadı');
        }

        // Manager sadece kendi şubesindeki kullanıcıları düzenleyebilir
        if ($userRole === 'manager' && $user['branch_id'] != $userBranchId) {
            session()->setFlashdata('error', 'Sadece kendi şubenizdeki kullanıcıları düzenleyebilirsiniz.');
            return redirect()->to('/admin/users');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'branch_id' => $this->request->getPost('branch_id'),
                'role_id' => $this->request->getPost('role_id'),
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'phone' => $this->request->getPost('phone'),
                'commission_rate' => $this->request->getPost('commission_rate'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Manager sadece kendi şubesine kullanıcı atayabilir
            if ($userRole === 'manager' && $data['branch_id'] != $userBranchId) {
                session()->setFlashdata('error', 'Kullanıcıyı sadece kendi şubenize atayabilirsiniz.');
                return redirect()->back()->withInput();
            }

            // Şifre değiştiriliyorsa ekle
            $newPassword = $this->request->getPost('password');
            if (!empty($newPassword)) {
                $data['password'] = $newPassword;
            }

            // Çalışma saatleri JSON formatında
            $workingHours = [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                $start = $this->request->getPost($day . '_start');
                $end = $this->request->getPost($day . '_end');
                $isWorking = $this->request->getPost($day . '_working');
                
                $workingHours[$day] = [
                    'is_working' => $isWorking ? true : false,
                    'start_time' => $start,
                    'end_time' => $end
                ];
            }
            $data['working_hours'] = json_encode($workingHours);

            // Kullanıcı güncelleme için özel doğrulama kuralları
            $validationRules = [
                'branch_id' => 'required|integer|is_not_unique[branches.id]',
                'role_id' => 'required|integer|is_not_unique[roles.id]',
                'username' => "required|min_length[3]|max_length[100]|alpha_numeric_punct|is_unique[users.username,id,{$id}]",
                'email' => "required|valid_email|max_length[255]|is_unique[users.email,id,{$id}]",
                'first_name' => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/]',
                'last_name' => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/]',
                'phone' => 'permit_empty|min_length[10]|max_length[20]|regex_match[/^[0-9\+\-\s\(\)]+$/]',
                'commission_rate' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
                'is_active' => 'permit_empty|in_list[0,1]'
            ];

            // Eğer şifre alanı boş değilse, şifre doğrulama kuralını ekle
            if (!empty($newPassword)) {
                $validationRules['password'] = 'required|min_length[6]';
            } else {
                // Şifre boşsa, modeldeki 'required' kuralını geçersiz kılmak için permit_empty kullan
                // Ancak $data dizisinden password'ü çıkarmak daha güvenli olabilir eğer model bunu zorunlu kılıyorsa
                // Şimdilik, modeldeki hashPassword callback'i boş şifreleri zaten atladığı için
                // ve $data['password'] sadece yeni şifre varsa ayarlandığı için bu yeterli olabilir.
                // Eğer modelde 'password' => 'required' ise ve boş gelirse hata verir.
                // Bu yüzden, $this->userModel->validationRules['password'] = 'permit_empty|min_length[6]' gibi bir atama
                // veya $data'dan password'ü çıkarmak gerekebilir.
                // Modeldeki hashPassword zaten boşsa işlem yapmıyor, bu yüzden $data'da olmaması sorun değil.
                // Eğer $data['password'] set edilmemişse, modelin beforeUpdate'i bunu atlayacaktır.
            }
            
            // UserModel'in genel kurallarını kullanmak yerine bu özel kuralları set et
            // Ancak bu, modelin diğer kurallarını (örn: custom messages) geçersiz kılabilir.
            // Daha iyi bir yaklaşım, modelin $validationRules dizisini doğrudan değiştirmek yerine
            // $this->validator->setRules($validationRules) kullanmak ve sonra $this->validator->run($data) yapmak olabilir.
            // Ancak CodeIgniter Model'in update metodu kendi içinde validation'ı çalıştırır.
            // Bu yüzden, modelin kurallarını geçici olarak değiştirmek bir yol olabilir.
            $originalRules = $this->userModel->getValidationRules(); // Orijinal kuralları sakla
            $this->userModel->setValidationRules($validationRules);

            // Geçici çözüm: Modelin validationRules özelliğini doğrudan değiştirelim.
            // Bu, production için ideal olmayabilir ama test için işe yarayabilir.
            // Daha kalıcı bir çözüm için, modelin update metodunu override etmek veya
            // controller'da ayrı bir validation adımı eklemek gerekebilir.

            if ($this->userModel->update($id, $data)) {
                $this->userModel->setValidationRules($originalRules); // Kuralları geri yükle
                session()->setFlashdata('success', 'Kullanıcı başarıyla güncellendi.');
                return redirect()->to('/admin/users');
            } else {
                $this->userModel->setValidationRules($originalRules); // Hata durumunda da kuralları geri yükle
                session()->setFlashdata('errors', $this->userModel->errors());
            }
        }

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        $data = [
            'title' => 'Kullanıcı Düzenle',
            'pageTitle' => 'Kullanıcı Düzenle',
            'user' => $user,
            'branches' => $branches,
            'roles' => $this->roleModel->getActiveRoles(),
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/users/edit', $data);
    }

    public function deleteUser($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kullanıcı bulunamadı']);
        }

        try {
            $this->userModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Kullanıcı başarıyla silindi']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function viewUser($id)
    {
        $user = $this->userModel->getUserDetail($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kullanıcı bulunamadı');
        }

        $data = [
            'title' => 'Kullanıcı Detayı',
            'pageTitle' => 'Kullanıcı Detayı',
            'user' => $user
        ];

        return view('admin/users/view', $data);
    }

    // ===================== MÜŞTERİ YÖNETİMİ =====================

    public function customers()
    {
        // AJAX isteği kontrolü
        if ($this->request->isAJAX()) {
            return $this->searchCustomersAjax();
        }

        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Rol bazlı şube listesi
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        // Müşteri listesi (rol bazlı filtreleme)
        $customerQuery = $this->customerModel->select('customers.*, branches.name as branch_name')
                                            ->join('branches', 'branches.id = customers.branch_id', 'left');

        if ($userRole !== 'admin') {
            $customerQuery->where('customers.branch_id', $userBranchId);
        }

        $customers = $customerQuery->orderBy('customers.id', 'DESC')->findAll();

        // Tags'leri JSON'dan array'e çevir
        foreach ($customers as &$customer) {
            if (!empty($customer['tags'])) {
                // Eğer zaten array ise olduğu gibi bırak, string ise JSON decode et
                if (is_string($customer['tags'])) {
                    $customer['tags'] = json_decode($customer['tags'], true) ?: [];
                } elseif (!is_array($customer['tags'])) {
                    $customer['tags'] = [];
                }
            } else {
                $customer['tags'] = [];
            }
        }

        $data = [
            'title' => 'Müşteri Yönetimi',
            'pageTitle' => 'Müşteri Yönetimi',
            'customers' => $customers,
            'branches' => $branches,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/customers/index', $data);
    }

    /**
     * AJAX müşteri arama
     */
    private function searchCustomersAjax()
    {
        $search = $this->request->getGet('search');
        $branchId = $this->request->getGet('branch_id');
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        $customerQuery = $this->customerModel->select('customers.*, branches.name as branch_name')
                                            ->join('branches', 'branches.id = customers.branch_id', 'left');

        // Rol bazlı şube filtresi
        if ($userRole !== 'admin') {
            $customerQuery->where('customers.branch_id', $userBranchId);
        } elseif (!empty($branchId)) {
            $customerQuery->where('customers.branch_id', $branchId);
        }

        // Arama filtresi
        if (!empty($search)) {
            $customerQuery->groupStart()
                         ->like('customers.first_name', $search)
                         ->orLike('customers.last_name', $search)
                         ->orLike('customers.phone', $search)
                         ->orLike('customers.email', $search)
                         ->groupEnd();
        }

        $customers = $customerQuery->orderBy('customers.id', 'DESC')->findAll();

        // Tags'leri JSON'dan array'e çevir
        foreach ($customers as &$customer) {
            if (!empty($customer['tags'])) {
                // Eğer zaten array ise olduğu gibi bırak, string ise JSON decode et
                if (is_string($customer['tags'])) {
                    $customer['tags'] = json_decode($customer['tags'], true) ?: [];
                } elseif (!is_array($customer['tags'])) {
                    $customer['tags'] = [];
                }
            } else {
                $customer['tags'] = [];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'customers' => $customers
        ]);
    }

    public function createCustomer()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'branch_id'  => $this->request->getPost('branch_id'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'phone'      => $this->request->getPost('phone'),
                'email'      => $this->request->getPost('email'),
                'birth_date' => $this->request->getPost('birth_date') ?: null,
                'notes'      => $this->request->getPost('notes'),
            ];

            // Manager sadece kendi şubesine müşteri ekleyebilir
            if ($userRole === 'manager') {
                $data['branch_id'] = $userBranchId;
            }
            // Handle 'tags' explicitly to ensure NULL for empty string
            $tags_input = $this->request->getPost('tags');
            if (!empty($tags_input)) {
                // Virgülle ayrılmış string'i diziye çevir, boşlukları temizle
                $tags_array = array_map('trim', explode(',', $tags_input));
                // Boş elemanları filtrele
                $tags_array = array_filter($tags_array);
                // Eğer tags_array boşsa, boş bir JSON dizisi string'i ata, değilse json_encode et
                $data['tags'] = !empty($tags_array) ? json_encode(array_values($tags_array)) : '[]';
            } else {
                $data['tags'] = '[]'; // Boş input için de boş JSON dizisi
            }

            if ($this->customerModel->save($data)) {
                $customerId = $this->customerModel->getInsertID();
                $customerName = $data['first_name'] . ' ' . $data['last_name'];
                
                // Panel içi bildirim gönder
                $this->sendNewCustomerNotification($customerId, $customerName);
                
                session()->setFlashdata('success', 'Müşteri başarıyla oluşturuldu.');
                return redirect()->to('/admin/customers');
            } else {
                session()->setFlashdata('errors', $this->customerModel->errors());
                // Form verilerini de flashdata ile geri gönderelim ki kullanıcı tekrar girmesin
                session()->setFlashdata('formData', $this->request->getPost());
            }
        }

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        $data = [
            'title' => 'Yeni Müşteri Oluştur',
            'pageTitle' => 'Yeni Müşteri Oluştur',
            'branches' => $branches,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId,
            'formData' => session()->getFlashdata('formData') // Hata durumunda form verilerini yükle
        ];
        
        // TODO: Müşteri oluşturma view'ı oluşturulacak: admin/customers/create.php
        return view('admin/customers/create', $data);
    }

    public function editCustomer($id)
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Müşteri bulunamadı');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'branch_id'  => $this->request->getPost('branch_id'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'phone'      => $this->request->getPost('phone'),
                'email'      => $this->request->getPost('email'),
                'birth_date' => $this->request->getPost('birth_date') ?: null,
                'notes'      => $this->request->getPost('notes'),
            ];

            // Manager sadece kendi şubesine müşteri atayabilir
            if ($userRole === 'manager') {
                $data['branch_id'] = $userBranchId;
            }

            // Handle 'tags' explicitly for update
            $tags_input = $this->request->getPost('tags');
            if (!empty($tags_input)) {
                $tags_array = array_map('trim', explode(',', $tags_input));
                $tags_array = array_filter($tags_array);
                $data['tags'] = !empty($tags_array) ? json_encode(array_values($tags_array)) : '[]';
            } else {
                $data['tags'] = '[]'; // Boş input için de boş JSON dizisi
            }

            if ($this->customerModel->update($id, $data)) {
                session()->setFlashdata('success', 'Müşteri başarıyla güncellendi.');
                return redirect()->to('/admin/customers');
            } else {
                session()->setFlashdata('errors', $this->customerModel->errors());
                // Form verilerini de flashdata ile geri gönderelim ki kullanıcı tekrar girmesin
                session()->setFlashdata('formData', $this->request->getPost());
            }
        }

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        $data = [
            'title' => 'Müşteri Düzenle',
            'pageTitle' => 'Müşteri Düzenle',
            'customer' => $customer,
            'branches' => $branches,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId,
            'formData' => session()->getFlashdata('formData') ?? $customer // Hata yoksa mevcut müşteri verileri
        ];

        return view('admin/customers/edit', $data);
    }

    public function viewCustomer($id)
    {
        $customer = $this->customerModel->select('customers.*, branches.name as branch_name')
                                        ->join('branches', 'branches.id = customers.branch_id', 'left')
                                        ->find($id);
        if (!$customer) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Müşteri bulunamadı');
        }

        // Tags'leri JSON'dan array'e çevir
        if (!empty($customer['tags'])) {
            if (is_string($customer['tags'])) {
                $customer['tags'] = json_decode($customer['tags'], true) ?: [];
            } elseif (!is_array($customer['tags'])) {
                $customer['tags'] = [];
            }
        } else {
            $customer['tags'] = [];
        }

        // Müşteri özet istatistikleri
        $customerStats = $this->customerModel->getCustomerSummaryStats($id);

        // Müşteri kredi bakiyesi
        $creditBalance = $this->customerModel->getCustomerCreditBalance($id);

        // Randevu geçmişi
        $appointments = $this->getCustomerAppointments($id);

        // Ödeme geçmişi
        $payments = $this->getCustomerPayments($id);

        // Paket kullanımları
        $packages = $this->getCustomerPackages($id);

        // Gönderilen mesajlar
        $messages = $this->getCustomerMessages($id);

        $data = [
            'title' => 'Müşteri Detayı',
            'pageTitle' => 'Müşteri Detayı',
            'customer' => $customer,
            'customerStats' => $customerStats,
            'creditBalance' => $creditBalance,
            'appointments' => $appointments,
            'payments' => $payments,
            'packages' => $packages,
            'messages' => $messages
        ];

        return view('admin/customers/view', $data);
    }

    /**
     * Müşteri randevu geçmişini getir
     */
    private function getCustomerAppointments($customerId)
    {
        $appointmentModel = new \App\Models\AppointmentModel();
        return $appointmentModel->select('
                appointments.id,
                appointments.branch_id,
                appointments.customer_id,
                appointments.service_id,
                appointments.staff_id,
                appointments.appointment_date,
                appointments.start_time,
                appointments.end_time,
                appointments.duration,
                appointments.status,
                appointments.type,
                appointments.price,
                appointments.paid_amount,
                appointments.payment_status,
                appointments.notes,
                appointments.service_notes,
                appointments.created_at,
                services.name as service_name,
                staff.first_name as staff_first_name,
                staff.last_name as staff_last_name
            ')
                                ->join('services', 'services.id = appointments.service_id', 'left')
                                ->join('users staff', 'staff.id = appointments.staff_id', 'left')
                                ->where('appointments.customer_id', $customerId)
                                ->orderBy('appointments.start_time', 'DESC')
                                ->findAll();
    }

    /**
     * Müşteri ödeme geçmişini getir
     */
    private function getCustomerPayments($customerId)
    {
        return $this->paymentModel->select('payments.*, appointments.start_time as appointment_date, services.name as service_name')
                                  ->join('appointments', 'appointments.id = payments.appointment_id', 'left')
                                  ->join('services', 'services.id = appointments.service_id', 'left')
                                  ->where('payments.customer_id', $customerId)
                                  ->orderBy('payments.created_at', 'DESC')
                                  ->findAll();
    }

    /**
     * Müşteri paket kullanımlarını getir
     */
    private function getCustomerPackages($customerId)
    {
        return $this->customerPackageModel->select('customer_packages.*, packages.name, packages.name as package_name, packages.type, packages.type as package_type, packages.price as package_price')
                                          ->join('packages', 'packages.id = customer_packages.package_id', 'left')
                                          ->where('customer_packages.customer_id', $customerId)
                                          ->orderBy('customer_packages.created_at', 'DESC')
                                          ->findAll();
    }

    /**
     * Müşteriye gönderilen mesajları getir
     */
    private function getCustomerMessages($customerId)
    {
        return $this->sentMessageModel->where('customer_id', $customerId)
                                      ->orderBy('created_at', 'DESC')
                                      ->findAll();
    }

    public function deleteCustomer($id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Müşteri bulunamadı']);
        }

        try {
            $this->customerModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Müşteri başarıyla silindi']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Yeni müşteri bildirimi gönder
     */
    private function sendNewCustomerNotification($customerId, $customerName)
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Bildirim gönderilecek kullanıcıları belirle
        $targetUsers = [];
        
        // Önce manager rolünün ID'sini bulalım
        $managerRole = $this->roleModel->where('name', 'manager')->first();
        if (!$managerRole) {
            return; // Manager rolü bulunamazsa bildirim gönderme
        }
        
        if ($userRole === 'admin') {
            // Admin ise tüm yöneticilere gönder
            $targetUsers = $this->userModel->where('role_id', $managerRole['id'])->findAll();
        } else {
            // Diğer roller ise sadece aynı şubedeki yöneticilere gönder
            $targetUsers = $this->userModel->where('role_id', $managerRole['id'])
                                           ->where('branch_id', $userBranchId)
                                           ->findAll();
        }

        // Her hedef kullanıcıya bildirim gönder
        foreach ($targetUsers as $user) {
            $notificationData = [
                'user_id' => $user['id'],
                'title' => 'Yeni Müşteri Eklendi',
                'message' => "Yeni müşteri eklendi: {$customerName}",
                'type' => 'info',
                'is_read' => false
            ];
            $this->inAppNotificationModel->save($notificationData);
        }
    }

    // ===================== HİZMET YÖNETİMİ =====================

    public function serviceCategories()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        $categoryQuery = $this->serviceCategoryModel->select('service_categories.*, branches.name as branch_name')
                                                    ->join('branches', 'branches.id = service_categories.branch_id', 'left');

        // Rol bazlı filtreleme
        if ($userRole !== 'admin') {
            // Manager: Kendi şubesindeki kategoriler + Admin'in eklediği genel kategoriler (branch_id = NULL)
            $categoryQuery->groupStart()
                         ->where('service_categories.branch_id', $userBranchId)
                         ->orWhere('service_categories.branch_id', null)
                         ->groupEnd();
        }

        $categories = $categoryQuery->orderBy('service_categories.name', 'ASC')->findAll();

        $data = [
            'title' => 'Hizmet Kategorileri',
            'pageTitle' => 'Hizmet Kategorileri',
            'categories' => $categories,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/services/categories/index', $data);
    }

    public function createServiceCategory()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Admin ise branch_id seçebilir (NULL = tüm şubeler), manager ise otomatik kendi şubesi
            if ($userRole === 'admin') {
                $branchId = $this->request->getPost('branch_id');
                $data['branch_id'] = ($branchId === '' || $branchId === '0') ? null : $branchId;
            } else {
                $data['branch_id'] = $userBranchId;
            }

            if ($this->serviceCategoryModel->save($data)) {
                session()->setFlashdata('success', 'Hizmet kategorisi başarıyla oluşturuldu.');
                return redirect()->to('/admin/service-categories');
            } else {
                session()->setFlashdata('errors', $this->serviceCategoryModel->errors());
            }
        }

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        $data = [
            'title' => 'Yeni Hizmet Kategorisi',
            'pageTitle' => 'Yeni Hizmet Kategorisi',
            'branches' => $branches,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/services/categories/create', $data);
    }

    public function editServiceCategory($id)
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        $category = $this->serviceCategoryModel->find($id);
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hizmet kategorisi bulunamadı');
        }

        // Manager sadece kendi şubesindeki kategorileri düzenleyebilir
        if ($userRole === 'manager' && $category['branch_id'] != $userBranchId) {
            session()->setFlashdata('error', 'Sadece kendi şubenizdeki kategorileri düzenleyebilirsiniz.');
            return redirect()->to('/admin/service-categories');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Admin ise branch_id seçebilir (NULL = tüm şubeler), manager ise değiştiremez
            if ($userRole === 'admin') {
                $branchId = $this->request->getPost('branch_id');
                $data['branch_id'] = ($branchId === '' || $branchId === '0') ? null : $branchId;
            }
            // Manager için branch_id değiştirilmez (mevcut değeri korunur)

            if ($this->serviceCategoryModel->update($id, $data)) {
                session()->setFlashdata('success', 'Hizmet kategorisi başarıyla güncellendi.');
                return redirect()->to('/admin/service-categories');
            } else {
                session()->setFlashdata('errors', $this->serviceCategoryModel->errors());
            }
        }

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        $data = [
            'title' => 'Hizmet Kategorisi Düzenle',
            'pageTitle' => 'Hizmet Kategorisi Düzenle',
            'category' => $category,
            'branches' => $branches,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId,
            'formData' => session()->getFlashdata('formData') ?? $category
        ];

        return view('admin/services/categories/edit', $data);
    }

    public function deleteServiceCategory($id)
    {
        $category = $this->serviceCategoryModel->find($id);
        if (!$category) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hizmet kategorisi bulunamadı']);
        }

        try {
            $this->serviceCategoryModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Hizmet kategorisi başarıyla silindi']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function services()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        $serviceQuery = $this->serviceModel->select('services.*, service_categories.name as category_name, branches.name as branch_name')
                                           ->join('service_categories', 'service_categories.id = services.category_id', 'left')
                                           ->join('branches', 'branches.id = services.branch_id', 'left');

        // Rol bazlı filtreleme
        if ($userRole !== 'admin') {
            // Manager: Kendi şubesindeki hizmetler + Admin'in eklediği genel hizmetler (branch_id = NULL)
            $serviceQuery->groupStart()
                        ->where('services.branch_id', $userBranchId)
                        ->orWhere('services.branch_id', null)
                        ->groupEnd();
        }

        $services = $serviceQuery->orderBy('services.name', 'ASC')->findAll();

        $data = [
            'title' => 'Hizmet Yönetimi',
            'pageTitle' => 'Hizmet Yönetimi',
            'services' => $services,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/services/index', $data);
    }

    public function createService()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'branch_id' => $this->request->getPost('branch_id'),
                'category_id' => $this->request->getPost('category_id'),
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'duration' => $this->request->getPost('duration'),
                'price' => $this->request->getPost('price'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Manager sadece kendi şubesine hizmet ekleyebilir
            if ($userRole === 'manager') {
                $data['branch_id'] = $userBranchId;
            }

            if ($this->serviceModel->save($data)) {
                $serviceId = $this->serviceModel->getInsertID();
                
                // Personel atamalarını kaydet
                $staffIds = $this->request->getPost('staff_ids') ?? [];
                foreach ($staffIds as $staffId) {
                    $this->serviceStaffModel->save([
                        'service_id' => $serviceId,
                        'user_id' => $staffId
                    ]);
                }

                session()->setFlashdata('success', 'Hizmet başarıyla oluşturuldu.');
                return redirect()->to('/admin/services');
            } else {
                session()->setFlashdata('errors', $this->serviceModel->errors());
            }
        }

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        // Kategoriler (şube bazlı)
        $categoryQuery = $this->serviceCategoryModel->where('is_active', true);
        if ($userRole !== 'admin') {
            // Manager: Kendi şubesindeki kategoriler + Admin'in eklediği genel kategoriler (branch_id = NULL)
            $categoryQuery->groupStart()
                         ->where('branch_id', $userBranchId)
                         ->orWhere('branch_id', null)
                         ->groupEnd();
        }
        $categories = $categoryQuery->findAll();

        // Personel listesi (şube bazlı) - şube bilgisiyle birlikte
        $staffQuery = $this->userModel->select('users.*, branches.name as branch_name')
                                     ->join('branches', 'branches.id = users.branch_id', 'left')
                                     ->where('users.is_active', true);
        if ($userRole !== 'admin') {
            $staffQuery->where('users.branch_id', $userBranchId);
        }
        $staff = $staffQuery->findAll();

        $data = [
            'title' => 'Yeni Hizmet',
            'pageTitle' => 'Yeni Hizmet',
            'branches' => $branches,
            'categories' => $categories,
            'staff' => $staff,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/services/create', $data);
    }

    public function editService($id)
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        $service = $this->serviceModel->find($id);
        if (!$service) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hizmet bulunamadı');
        }

        // Manager sadece kendi şubesindeki hizmetleri düzenleyebilir
        if ($userRole === 'manager' && $service['branch_id'] != $userBranchId) {
            session()->setFlashdata('error', 'Sadece kendi şubenizdeki hizmetleri düzenleyebilirsiniz.');
            return redirect()->to('/admin/services');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'branch_id' => $this->request->getPost('branch_id'),
                'category_id' => $this->request->getPost('category_id'),
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'duration' => $this->request->getPost('duration'),
                'price' => $this->request->getPost('price'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Manager için şube değiştirilemez (mevcut değeri korunur)
            if ($userRole === 'manager') {
                $data['branch_id'] = $service['branch_id'];
            }

            if ($this->serviceModel->update($id, $data)) {
                // Mevcut personel atamalarını sil
                $this->serviceStaffModel->where('service_id', $id)->delete();
                
                // Yeni personel atamalarını kaydet
                $staffIds = $this->request->getPost('staff_ids') ?? [];
                foreach ($staffIds as $staffId) {
                    $this->serviceStaffModel->save([
                        'service_id' => $id,
                        'user_id' => $staffId
                    ]);
                }

                session()->setFlashdata('success', 'Hizmet başarıyla güncellendi.');
                return redirect()->to('/admin/services');
            } else {
                session()->setFlashdata('errors', $this->serviceModel->errors());
            }
        }

        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        // Kategoriler (şube bazlı)
        $categoryQuery = $this->serviceCategoryModel->where('is_active', true);
        if ($userRole !== 'admin') {
            // Manager: Kendi şubesindeki kategoriler + Admin'in eklediği genel kategoriler (branch_id = NULL)
            $categoryQuery->groupStart()
                         ->where('branch_id', $userBranchId)
                         ->orWhere('branch_id', null)
                         ->groupEnd();
        }
        $categories = $categoryQuery->findAll();

        // Personel listesi (şube bazlı) - şube bilgisiyle birlikte
        $staffQuery = $this->userModel->select('users.*, branches.name as branch_name')
                                     ->join('branches', 'branches.id = users.branch_id', 'left')
                                     ->where('users.is_active', true);
        if ($userRole !== 'admin') {
            $staffQuery->where('users.branch_id', $userBranchId);
        }
        $staff = $staffQuery->findAll();

        // Mevcut personel atamaları
        $assignedStaff = $this->serviceStaffModel->where('service_id', $id)->findColumn('user_id');

        $data = [
            'title' => 'Hizmet Düzenle',
            'pageTitle' => 'Hizmet Düzenle',
            'service' => $service,
            'branches' => $branches,
            'categories' => $categories,
            'staff' => $staff,
            'assignedStaff' => $assignedStaff,
            'assignedStaffIds' => $assignedStaff, // View uyumluluğu için
            'userRole' => $userRole,
            'userBranchId' => $userBranchId,
            'formData' => session()->getFlashdata('formData') ?? $service
        ];

        return view('admin/services/edit', $data);
    }

    public function deleteService($id)
    {
        $service = $this->serviceModel->find($id);
        if (!$service) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hizmet bulunamadı']);
        }

        try {
            // Personel atamalarını da sil
            $this->serviceStaffModel->where('service_id', $id)->delete();
            $this->serviceModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Hizmet başarıyla silindi']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===================== PAKET YÖNETİMİ =====================

    public function packages()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        $packageQuery = $this->packageModel->select('packages.*, branches.name as branch_name')
                                           ->join('branches', 'branches.id = packages.branch_id', 'left');

        // Rol bazlı filtreleme
        if ($userRole !== 'admin') {
            $packageQuery->where('packages.branch_id', $userBranchId);
        }

        $packages = $packageQuery->orderBy('packages.name', 'ASC')->findAll();

        $data = [
            'title' => 'Paket Yönetimi',
            'pageTitle' => 'Paket Yönetimi',
            'packages' => $packages,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/packages/index', $data);
    }

    public function createPackage()
    {
        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'branch_id' => $this->request->getPost('branch_id'),
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'type' => $this->request->getPost('type'),
                'total_sessions' => $this->request->getPost('total_sessions'),
                'total_minutes' => $this->request->getPost('total_minutes'),
                'price' => $this->request->getPost('price'),
                'validity_months' => $this->request->getPost('validity_months'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Manager sadece kendi şubesine paket ekleyebilir
            if ($userRole === 'manager') {
                $data['branch_id'] = $userBranchId;
            }

            if ($this->packageModel->save($data)) {
                $packageId = $this->packageModel->getInsertID();
                
                // Hizmet atamalarını kaydet
                $serviceIds = $this->request->getPost('service_ids') ?? [];
                foreach ($serviceIds as $serviceId) {
                    $this->packageServiceModel->save([
                        'package_id' => $packageId,
                        'service_id' => $serviceId
                    ]);
                }

                session()->setFlashdata('success', 'Paket başarıyla oluşturuldu.');
                return redirect()->to('/admin/packages');
            } else {
                session()->setFlashdata('errors', $this->packageModel->errors());
            }
        }

        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        // Hizmetler (şube bazlı) - kategori bilgisiyle birlikte
        $serviceQuery = $this->serviceModel->select('services.*, service_categories.name as category_name')
                                          ->join('service_categories', 'service_categories.id = services.category_id', 'left')
                                          ->where('services.is_active', true);
        if ($userRole !== 'admin') {
            $serviceQuery->where('services.branch_id', $userBranchId);
        }
        $services = $serviceQuery->orderBy('service_categories.name', 'ASC')
                                ->orderBy('services.name', 'ASC')
                                ->findAll();

        $data = [
            'title' => 'Yeni Paket',
            'pageTitle' => 'Yeni Paket',
            'branches' => $branches,
            'services' => $services,
            'userRole' => $userRole,
            'userBranchId' => $userBranchId
        ];

        return view('admin/packages/create', $data);
    }

    public function editPackage($id)
    {
        $package = $this->packageModel->find($id);
        if (!$package) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Paket bulunamadı');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'branch_id' => $this->request->getPost('branch_id'),
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'type' => $this->request->getPost('type'),
                'total_sessions' => $this->request->getPost('total_sessions'),
                'total_minutes' => $this->request->getPost('total_minutes'),
                'price' => $this->request->getPost('price'),
                'validity_months' => $this->request->getPost('validity_months'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            if ($this->packageModel->update($id, $data)) {
                // Mevcut hizmet atamalarını sil
                $this->packageServiceModel->where('package_id', $id)->delete();
                
                // Yeni hizmet atamalarını kaydet
                $serviceIds = $this->request->getPost('service_ids') ?? [];
                foreach ($serviceIds as $serviceId) {
                    $this->packageServiceModel->save([
                        'package_id' => $id,
                        'service_id' => $serviceId
                    ]);
                }

                session()->setFlashdata('success', 'Paket başarıyla güncellendi.');
                return redirect()->to('/admin/packages');
            } else {
                session()->setFlashdata('errors', $this->packageModel->errors());
            }
        }

        $userRole = session()->get('role_name');
        $userBranchId = session()->get('branch_id');

        // Şube listesi (rol bazlı)
        $branches = [];
        if ($userRole === 'admin') {
            $branches = $this->branchModel->getActiveBranches();
        } else {
            $branches = $this->branchModel->where('id', $userBranchId)->findAll();
        }

        // Hizmetler (şube bazlı) - kategori bilgisiyle birlikte
        $serviceQuery = $this->serviceModel->select('services.*, service_categories.name as category_name')
                                          ->join('service_categories', 'service_categories.id = services.category_id', 'left')
                                          ->where('services.is_active', true);
        if ($userRole !== 'admin') {
            $serviceQuery->where('services.branch_id', $userBranchId);
        }
        $services = $serviceQuery->orderBy('service_categories.name', 'ASC')
                                ->orderBy('services.name', 'ASC')
                                ->findAll();

        // Mevcut hizmet atamaları
        $assignedServices = $this->packageServiceModel->where('package_id', $id)->findColumn('service_id');

        $data = [
            'title' => 'Paket Düzenle',
            'pageTitle' => 'Paket Düzenle',
            'package' => $package,
            'branches' => $branches,
            'services' => $services,
            'assignedServices' => $assignedServices,
            'assignedServiceIds' => $assignedServices, // View uyumluluğu için
            'userRole' => $userRole,
            'userBranchId' => $userBranchId,
            'formData' => session()->getFlashdata('formData') ?? $package
        ];

        return view('admin/packages/edit', $data);
    }

    public function deletePackage($id)
    {
        $package = $this->packageModel->find($id);
        if (!$package) {
            return $this->response->setJSON(['success' => false, 'message' => 'Paket bulunamadı']);
        }

        try {
            // Hizmet atamalarını da sil
            $this->packageServiceModel->where('package_id', $id)->delete();
            $this->packageModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Paket başarıyla silindi']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function viewPackage($id)
    {
        $package = $this->packageModel->select('packages.*, branches.name as branch_name')
                                     ->join('branches', 'branches.id = packages.branch_id', 'left')
                                     ->find($id);
        if (!$package) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Paket bulunamadı');
        }

        // Paket hizmetlerini getir
        $packageServices = $this->packageServiceModel->select('package_services.*, services.name as service_name, services.duration, services.price')
                                                     ->join('services', 'services.id = package_services.service_id', 'left')
                                                     ->where('package_services.package_id', $id)
                                                     ->findAll();

        // Paket satış istatistikleri
        $salesStats = $this->customerPackageModel->select('
                COUNT(*) as total_sales,
                COUNT(CASE WHEN status = "active" THEN 1 END) as active_sales,
                COUNT(CASE WHEN status = "expired" THEN 1 END) as expired_sales,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_sales
            ')
            ->where('package_id', $id)
            ->first();

        // Son satışlar
        $recentSales = $this->customerPackageModel->select('customer_packages.*, customers.first_name, customers.last_name')
                                                  ->join('customers', 'customers.id = customer_packages.customer_id')
                                                  ->where('customer_packages.package_id', $id)
                                                  ->orderBy('customer_packages.created_at', 'DESC')
                                                  ->limit(10)
                                                  ->findAll();

        // Package'a services ekle (view uyumluluğu için)
        $package['services'] = $packageServices;

        $data = [
            'title' => 'Paket Detayı',
            'pageTitle' => 'Paket Detayı',
            'package' => $package,
            'packageServices' => $packageServices,
            'salesStats' => $salesStats,
            'recentSales' => $recentSales
        ];

        return view('admin/packages/view', $data);
    }

    // ===================== PAKET SATIŞ VE RAPOR YÖNETİMİ =====================

    public function sellPackage()
    {
            if ($this->request->getMethod() === 'POST') {
                $data = [
                    'customer_id' => $this->request->getPost('customer_id'),
                    'package_id' => $this->request->getPost('package_id'),
                    'purchase_date' => date('Y-m-d'),
                    'status' => 'active'
                ];
    
                // Paket bilgilerini al
                $package = $this->packageModel->find($data['package_id']);
                if (!$package) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Paket bulunamadı']);
                }
    
                // Geçerlilik tarihini hesapla
                $data['expiry_date'] = date('Y-m-d', strtotime("+{$package['validity_months']} months"));
                
                // Paket türüne göre kalan değerleri ayarla
                if ($package['type'] === 'session') {
                    $data['remaining_sessions'] = $package['total_sessions'];
                    $data['remaining_minutes'] = null;
                } else {
                    $data['remaining_sessions'] = null;
                    $data['remaining_minutes'] = $package['total_minutes'];
                }
    
                if ($this->customerPackageModel->save($data)) {
                    session()->setFlashdata('success', 'Paket başarıyla satıldı');
                    return redirect()->to('/admin/packages/sales');
                } else {
                    session()->setFlashdata('errors', $this->customerPackageModel->errors());
                }
            }
    
            $userRole = session()->get('role_name');
            $userBranchId = session()->get('branch_id');
    
            // Müşteriler (şube bazlı)
            $customerQuery = $this->customerModel;
            if ($userRole !== 'admin') {
                $customerQuery = $customerQuery->where('branch_id', $userBranchId);
            }
            $customers = $customerQuery->findAll();
    
            // Paketler (şube bazlı)
            $packageQuery = $this->packageModel->where('is_active', true);
            if ($userRole !== 'admin') {
                $packageQuery = $packageQuery->where('branch_id', $userBranchId);
            }
            $packages = $packageQuery->findAll();
    
            $data = [
                'title' => 'Paket Satışı',
                'pageTitle' => 'Paket Satışı',
                'customers' => $customers,
                'packages' => $packages,
                'userRole' => $userRole,
                'userBranchId' => $userBranchId
            ];
    
            return view('admin/packages/sell', $data);
        }
    
        public function packageSales()
        {
            $userRole = session()->get('role_name');
            $userBranchId = session()->get('branch_id');
    
            // Paket satışları
            $salesQuery = $this->customerPackageModel->select('customer_packages.*, customers.first_name, customers.last_name, customers.phone, customers.email, packages.name as package_name, packages.type, packages.price as package_price, branches.name as branch_name')
                                                     ->join('customers', 'customers.id = customer_packages.customer_id')
                                                     ->join('packages', 'packages.id = customer_packages.package_id')
                                                     ->join('branches', 'branches.id = customers.branch_id', 'left');
    
            if ($userRole !== 'admin') {
                $salesQuery = $salesQuery->where('customers.branch_id', $userBranchId);
            }
    
            $sales = $salesQuery->orderBy('customer_packages.created_at', 'DESC')->findAll();
    
            // İstatistikler
            $totalSales = count($sales);
            $activeSales = count(array_filter($sales, function($sale) { return $sale['status'] === 'active'; }));
            $expiredSales = count(array_filter($sales, function($sale) { return $sale['status'] === 'expired'; }));
            $completedSales = count(array_filter($sales, function($sale) { return $sale['status'] === 'completed'; }));
            
            // Toplam gelir hesaplama (paket fiyatlarını topla)
            $totalRevenue = 0;
            foreach ($sales as $sale) {
                // Paket fiyatını JOIN ile aldığımız için direkt kullanabiliriz
                $totalRevenue += $sale['package_price'] ?? 0;
            }
            
            // Ortalama satış hesaplama
            $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
            
            // Bu ay satış sayısı
            $thisMonth = date('Y-m');
            $thisMonthCount = count(array_filter($sales, function($sale) use ($thisMonth) {
                return date('Y-m', strtotime($sale['created_at'])) === $thisMonth;
            }));
    
            $data = [
                'title' => 'Paket Satışları',
                'pageTitle' => 'Paket Satışları',
                'sales' => $sales,
                'totalSales' => $totalSales,
                'totalRevenue' => $totalRevenue,
                'averageSale' => $averageSale,
                'thisMonthCount' => $thisMonthCount,
                'activeSales' => $activeSales,
                'expiredSales' => $expiredSales,
                'completedSales' => $completedSales,
                'userRole' => $userRole,
                'userBranchId' => $userBranchId
            ];
    
            return view('admin/packages/sales', $data);
        }
    
        public function packageReports()
        {
            $userRole = session()->get('role_name');
            $userBranchId = session()->get('branch_id');
    
            // Aktif paketler
            $activePackagesQuery = $this->customerPackageModel->select('customer_packages.*, customers.first_name, customers.last_name, packages.name as package_name, packages.type')
                                                              ->join('customers', 'customers.id = customer_packages.customer_id')
                                                              ->join('packages', 'packages.id = customer_packages.package_id')
                                                              ->where('customer_packages.status', 'active');
    
            if ($userRole !== 'admin') {
                $activePackagesQuery->where('customers.branch_id', $userBranchId);
            }
    
            $activePackages = $activePackagesQuery->orderBy('customer_packages.expiry_date', 'ASC')->findAll();
    
            // Süresi dolmuş paketler
            $expiredPackagesQuery = $this->customerPackageModel->select('customer_packages.*, customers.first_name, customers.last_name, packages.name as package_name')
                                                               ->join('customers', 'customers.id = customer_packages.customer_id')
                                                               ->join('packages', 'packages.id = customer_packages.package_id')
                                                               ->where('customer_packages.status', 'expired');
    
            if ($userRole !== 'admin') {
                $expiredPackagesQuery->where('customers.branch_id', $userBranchId);
            }
    
            $expiredPackages = $expiredPackagesQuery->orderBy('customer_packages.expiry_date', 'DESC')->findAll();
    
            $data = [
                'title' => 'Paket Raporları',
                'pageTitle' => 'Paket Raporları',
                'activePackages' => $activePackages,
                'expiredPackages' => $expiredPackages,
                'userRole' => $userRole,
                'userBranchId' => $userBranchId
            ];
    
            return view('admin/packages/reports', $data);
        }
    
        public function expireOldPackages()
        {
            // Süresi dolmuş paketleri güncelle
            $expiredCount = $this->customerPackageModel->where('expiry_date <', date('Y-m-d'))
                                                       ->where('status', 'active')
                                                       ->set(['status' => 'expired'])
                                                       ->update();
    
            return $this->response->setJSON([
                'success' => true,
                'message' => "{$expiredCount} paket süresi dolmuş olarak güncellendi"
            ]);
        }
    
        public function getPackageAlerts()
        {
            $userRole = session()->get('role_name');
            $userBranchId = session()->get('branch_id');
    
            // Süresi yaklaşan paketler (30 gün içinde)
            $expiringQuery = $this->customerPackageModel->select('customer_packages.*, customers.first_name, customers.last_name, packages.name as package_name')
                                                        ->join('customers', 'customers.id = customer_packages.customer_id')
                                                        ->join('packages', 'packages.id = customer_packages.package_id')
                                                        ->where('customer_packages.status', 'active')
                                                        ->where('customer_packages.expiry_date <=', date('Y-m-d', strtotime('+30 days')))
                                                        ->where('customer_packages.expiry_date >=', date('Y-m-d'));
    
            if ($userRole !== 'admin') {
                $expiringQuery->where('customers.branch_id', $userBranchId);
            }
    
            $expiringPackages = $expiringQuery->findAll();
    
            // Bitmek üzere olan paketler (son 1 seans/10 dakika)
            $lowUsageQuery = $this->customerPackageModel->select('customer_packages.*, customers.first_name, customers.last_name, packages.name as package_name')
                                                        ->join('customers', 'customers.id = customer_packages.customer_id')
                                                        ->join('packages', 'packages.id = customer_packages.package_id')
                                                        ->where('customer_packages.status', 'active')
                                                        ->groupStart()
                                                            ->where('customer_packages.remaining_sessions <=', 1)
                                                            ->orWhere('customer_packages.remaining_minutes <=', 10)
                                                        ->groupEnd();
    
            if ($userRole !== 'admin') {
                $lowUsageQuery->where('customers.branch_id', $userBranchId);
            }
    
            $lowUsagePackages = $lowUsageQuery->findAll();
    
            return $this->response->setJSON([
                'success' => true,
                'expiringPackages' => $expiringPackages,
                'lowUsagePackages' => $lowUsagePackages
            ]);
        }
    
        public function getPackagesByBranch($branchId)
        {
            $packages = $this->packageModel->where('branch_id', $branchId)
                                           ->where('is_active', true)
                                           ->findAll();
    
            return $this->response->setJSON([
                'success' => true,
                'packages' => $packages
            ]);
        }
    
    }