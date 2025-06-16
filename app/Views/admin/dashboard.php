<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Paneli</h1>
        <p class="text-gray-600 mt-2">Sistem yönetimi ve ayarları</p>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Şubeler -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Şubeler</h3>
                    <p class="text-3xl font-bold text-blue-600"><?= $stats['branches'] ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/branches" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Şubeleri Yönet <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Roller -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users-cog text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Roller</h3>
                    <p class="text-3xl font-bold text-green-600"><?= $stats['roles'] ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/roles" class="text-green-600 hover:text-green-800 text-sm font-medium">
                    Rolleri Yönet <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- İzinler -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-key text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">İzinler</h3>
                    <p class="text-3xl font-bold text-purple-600"><?= $stats['permissions'] ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/permissions" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                    İzinleri Yönet <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Kullanıcılar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Kullanıcılar</h3>
                    <p class="text-3xl font-bold text-orange-600"><?= $stats['users'] ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/users" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                    Kullanıcıları Yönet <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Hızlı İşlemler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Hızlı İşlemler</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="/admin/branches/create" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-plus-circle text-blue-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-medium text-gray-900">Yeni Şube</h3>
                    <p class="text-sm text-gray-600">Yeni şube ekle</p>
                </div>
            </a>
            
            <a href="/admin/roles/create" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-user-plus text-green-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-medium text-gray-900">Yeni Rol</h3>
                    <p class="text-sm text-gray-600">Yeni rol oluştur</p>
                </div>
            </a>
            
            <a href="/admin/permissions/create" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <i class="fas fa-key text-purple-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-medium text-gray-900">Yeni İzin</h3>
                    <p class="text-sm text-gray-600">Yeni izin tanımla</p>
                </div>
            </a>
            
            <a href="/admin/users/create" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                <i class="fas fa-user-plus text-orange-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-medium text-gray-900">Yeni Kullanıcı</h3>
                    <p class="text-sm text-gray-600">Yeni kullanıcı ekle</p>
                </div>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>