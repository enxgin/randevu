<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık ve Yeni Rol Butonu -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($pageTitle) ?></h1>
            <p class="text-gray-600 mt-2">Sistem rollerini ve yetkilerini yönetin</p>
        </div>
        <a href="/admin/roles/create" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Yeni Rol
        </a>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Roller Tablosu -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Sistem Rolleri</h3>
        </div>
        
        <?php if (empty($roles)): ?>
            <div class="p-6 text-center">
                <i class="fas fa-users-cog text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">Henüz rol eklenmemiş</p>
                <p class="text-gray-400 text-sm mt-2">İlk rolü eklemek için "Yeni Rol" butonuna tıklayın</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rol
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Açıklama
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kullanıcı Sayısı
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durum
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($roles as $role): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= esc($role['display_name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Kod: <?= esc($role['name']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs">
                                        <?= esc($role['description']) ?: 'Açıklama yok' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-gray-400 mr-2"></i>
                                        <?= $role['user_count'] ?? 0 ?> kullanıcı
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($role['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Pasif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/admin/roles/edit/<?= $role['id'] ?>" 
                                           class="text-green-600 hover:text-green-900"
                                           title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($role['name'] !== 'admin'): // Admin rolü silinemez ?>
                                            <button onclick="deleteRole(<?= $role['id'] ?>, '<?= esc($role['display_name']) ?>')" 
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-gray-400" title="Admin rolü silinemez">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Rol Bilgi Kartı -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Rol Sistemi Hakkında</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>• Her rol belirli izinlere sahiptir ve bu izinler rolün yetkilerini belirler</p>
                    <p>• Admin rolü tüm izinlere sahiptir ve silinemez</p>
                    <p>• Aktif olmayan roller sisteme giriş yapamaz</p>
                    <p>• Kullanımda olan roller silinmeden önce kullanıcıları başka rollere atanmalıdır</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Silme Onay Modal'ı -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-md">
        <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
            <h3 class="text-lg font-medium text-gray-900">Rol Sil</h3>
        </div>
        <p class="text-gray-600 mb-6">
            <span id="roleName"></span> rolünü silmek istediğinizden emin misiniz? 
            Bu işlem geri alınamaz ve bu role sahip kullanıcılar sisteme giriş yapamayacaktır.
        </p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                İptal
            </button>
            <button id="confirmDeleteBtn" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Sil
            </button>
        </div>
    </div>
</div>

<script>
let roleToDelete = null;

function deleteRole(id, name) {
    roleToDelete = id;
    document.getElementById('roleName').textContent = name;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
    roleToDelete = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (roleToDelete) {
        fetch(`/admin/roles/delete/${roleToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            alert('Bir hata oluştu: ' + error.message);
        });
    }
    closeDeleteModal();
});

// Modal dışına tıklandığında kapat
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
<?= $this->endSection() ?>