<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık ve Yeni Kullanıcı Butonu -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kullanıcı Yönetimi</h1>
            <p class="text-gray-600 mt-2">Sistem kullanıcılarını görüntüleyin ve yönetin</p>
        </div>
        <a href="/admin/users/create" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Yeni Kullanıcı
        </a>
    </div>

    <!-- Kullanıcılar Tablosu -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Kullanıcılar (<?= count($users) ?>)</h3>
        </div>
        
        <?php if (empty($users)): ?>
            <div class="p-6 text-center">
                <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">Henüz kullanıcı eklenmemiş</p>
                <p class="text-gray-400 text-sm mt-2">İlk kullanıcıyı eklemek için "Yeni Kullanıcı" butonuna tıklayın</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kullanıcı
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İletişim
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Şube & Rol
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prim Oranı
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
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-orange-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @<?= esc($user['username']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        <?= esc($user['email']) ?>
                                    </div>
                                    <?php if ($user['phone']): ?>
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                                            <?= esc($user['phone']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-building text-gray-400 mr-2"></i>
                                        <?= esc($user['branch_name'] ?? 'Atanmamış') ?>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-user-tag text-gray-400 mr-2"></i>
                                        <?= esc($user['role_name'] ?? 'Atanmamış') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($user['commission_rate']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            %<?= number_format($user['commission_rate'], 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['is_active']): ?>
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
                                        <a href="/admin/users/view/<?= $user['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/users/edit/<?= $user['id'] ?>" 
                                           class="text-orange-600 hover:text-orange-900" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteUser(<?= $user['id'] ?>, '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>')" 
                                                class="text-red-600 hover:text-red-900" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Silme Onay Modal'ı -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-md">
        <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
            <h3 class="text-lg font-medium text-gray-900">Kullanıcı Sil</h3>
        </div>
        <p class="text-gray-600 mb-6">
            <span id="userName"></span> kullanıcısını silmek istediğinizden emin misiniz? 
            Bu işlem geri alınamaz.
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let userToDelete = null;

function deleteUser(id, name) {
    userToDelete = id;
    document.getElementById('userName').textContent = name;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
    userToDelete = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (userToDelete) {
        fetch(`/admin/users/delete/${userToDelete}`, {
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