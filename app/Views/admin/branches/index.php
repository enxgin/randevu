<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık ve Yeni Şube Butonu -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($pageTitle) ?></h1>
            <p class="text-gray-600 mt-2">Tüm şubeleri görüntüleyin ve yönetin</p>
        </div>
        <a href="/admin/branches/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Yeni Şube
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

    <!-- Şubeler Tablosu -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Şubeler</h3>
        </div>
        
        <?php if (empty($branches)): ?>
            <div class="p-6 text-center">
                <i class="fas fa-building text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">Henüz şube eklenmemiş</p>
                <p class="text-gray-400 text-sm mt-2">İlk şubenizi eklemek için "Yeni Şube" butonuna tıklayın</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Şube Adı
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İletişim
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
                        <?php foreach ($branches as $branch): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= esc($branch['name']) ?>
                                        </div>
                                        <?php if ($branch['address']): ?>
                                            <div class="text-sm text-gray-500">
                                                <?= esc($branch['address']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($branch['phone']): ?>
                                        <div class="flex items-center mb-1">
                                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                                            <?= esc($branch['phone']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($branch['email']): ?>
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                            <?= esc($branch['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-gray-400 mr-2"></i>
                                        <?= $branch['user_count'] ?? 0 ?> kullanıcı
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($branch['is_active']): ?>
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
                                        <a href="/admin/branches/edit/<?= $branch['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteBranch(<?= $branch['id'] ?>, '<?= esc($branch['name']) ?>')" 
                                                class="text-red-600 hover:text-red-900">
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Silme Onay Modal'ı -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-md">
        <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
            <h3 class="text-lg font-medium text-gray-900">Şube Sil</h3>
        </div>
        <p class="text-gray-600 mb-6">
            <span id="branchName"></span> şubesini silmek istediğinizden emin misiniz? 
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

<script>
let branchToDelete = null;

function deleteBranch(id, name) {
    branchToDelete = id;
    document.getElementById('branchName').textContent = name;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
    branchToDelete = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (branchToDelete) {
        fetch(`/admin/branches/delete/${branchToDelete}`, {
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