<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50">
    <!-- Başlık -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kullanıcı Detayı</h1>
                    <p class="mt-1 text-sm text-gray-500"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?> kullanıcısının detay bilgileri</p>
                </div>
                <div class="flex space-x-3">
                    <a href="/admin/users/edit/<?= $user['id'] ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Düzenle
                    </a>
                    <a href="/admin/users" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ana İçerik -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Kullanıcı Özet Kartı -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-8">
            <div class="px-6 py-6">
                <div class="flex items-center">
                    <div class="h-20 w-20 flex-shrink-0">
                        <?php if (!empty($user['avatar'])): ?>
                            <img class="h-20 w-20 rounded-full object-cover" src="<?= base_url('uploads/avatars/' . $user['avatar']) ?>" alt="<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>">
                        <?php else: ?>
                            <div class="h-20 w-20 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-white font-bold text-2xl"><?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="ml-6 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                                <p class="text-gray-500">@<?= esc($user['username']) ?></p>
                            </div>
                            <div class="flex space-x-3">
                                <?php if ($user['is_active']): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Pasif
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-building mr-2"></i>
                                <span class="font-medium">Şube:</span>
                                <span class="ml-1"><?= esc($user['branch_name']) ?></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-user-tag mr-2"></i>
                                <span class="font-medium">Rol:</span>
                                <span class="ml-1"><?= esc($user['role_name']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- İletişim Bilgileri -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">İletişim Bilgileri</h3>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-gray-400 mr-3 w-5"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-500">E-posta</p>
                            <p class="text-gray-900"><?= esc($user['email']) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($user['phone'])): ?>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Telefon</p>
                                <p class="text-gray-900"><?= esc($user['phone']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($user['commission_rate'])): ?>
                        <div class="flex items-center">
                            <i class="fas fa-percentage text-gray-400 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Prim Oranı</p>
                                <p class="text-gray-900">%<?= number_format($user['commission_rate'], 2) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sistem Bilgileri -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sistem Bilgileri</h3>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-plus text-gray-400 mr-3 w-5"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kayıt Tarihi</p>
                            <p class="text-gray-900"><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($user['last_login'])): ?>
                        <div class="flex items-center">
                            <i class="fas fa-sign-in-alt text-gray-400 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Son Giriş</p>
                                <p class="text-gray-900"><?= date('d.m.Y H:i', strtotime($user['last_login'])) ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center">
                            <i class="fas fa-sign-in-alt text-gray-400 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Son Giriş</p>
                                <p class="text-gray-500 italic">Hiç giriş yapmadı</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($user['updated_at']) && $user['updated_at'] !== $user['created_at']): ?>
                        <div class="flex items-center">
                            <i class="fas fa-edit text-gray-400 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Son Güncelleme</p>
                                <p class="text-gray-900"><?= date('d.m.Y H:i', strtotime($user['updated_at'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Çalışma Saatleri -->
        <?php if (!empty($user['working_hours'])): ?>
            <?php $workingHours = json_decode($user['working_hours'], true); ?>
            <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Çalışma Saatleri</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php 
                        $days = [
                            'monday' => 'Pazartesi',
                            'tuesday' => 'Salı', 
                            'wednesday' => 'Çarşamba',
                            'thursday' => 'Perşembe',
                            'friday' => 'Cuma',
                            'saturday' => 'Cumartesi',
                            'sunday' => 'Pazar'
                        ];
                        ?>
                        <?php foreach ($days as $dayKey => $dayName): ?>
                            <?php
                            $dayData = $workingHours[$dayKey] ?? [];
                            $isWorking = isset($dayData['is_working']) ? $dayData['is_working'] : false;
                            $startTime = isset($dayData['start_time']) ? $dayData['start_time'] : '';
                            $endTime = isset($dayData['end_time']) ? $dayData['end_time'] : '';
                            ?>
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <span class="font-medium text-gray-700"><?= $dayName ?></span>
                                <?php if ($isWorking && !empty($startTime) && !empty($endTime)): ?>
                                    <span class="text-sm text-green-600 font-medium">
                                        <?= esc($startTime) ?> - <?= esc($endTime) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-sm text-gray-400 italic">Çalışmıyor</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- İstatistikler (Gelecekte eklenebilir) -->
        <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">İstatistikler</h3>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">-</div>
                        <div class="text-sm text-gray-600">Toplam Randevu</div>
                        <div class="text-xs text-gray-500 mt-1">Yakında eklenecek</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">-</div>
                        <div class="text-sm text-gray-600">Toplam Prim</div>
                        <div class="text-xs text-gray-500 mt-1">Yakında eklenecek</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">-</div>
                        <div class="text-sm text-gray-600">Müşteri Sayısı</div>
                        <div class="text-xs text-gray-500 mt-1">Yakında eklenecek</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- İşlem Butonları -->
        <div class="mt-8 flex justify-center space-x-3">
            <a href="/admin/users/edit/<?= $user['id'] ?>" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-edit mr-2"></i>
                Kullanıcıyı Düzenle
            </a>
            <button type="button" onclick="deleteUser(<?= $user['id'] ?>, '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>')" class="inline-flex items-center px-6 py-3 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                <i class="fas fa-trash mr-2"></i>
                Kullanıcıyı Sil
            </button>
        </div>
    </div>
</div>

<!-- Silme Onayı Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Kullanıcıyı Sil</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    <span id="deleteUserName"></span> kullanıcısını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 mt-4">
                <button id="cancelDelete" type="button" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                    İptal
                </button>
                <button id="confirmDelete" type="button" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                    Sil
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let userToDelete = null;

function deleteUser(userId, userName) {
    userToDelete = userId;
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').classList.add('hidden');
    userToDelete = null;
});

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (userToDelete) {
        fetch(`/admin/users/delete/${userToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/admin/users';
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
    
    document.getElementById('deleteModal').classList.add('hidden');
});

// Modal dışına tıklayınca kapat
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        userToDelete = null;
    }
});
</script>
<?= $this->endSection() ?>