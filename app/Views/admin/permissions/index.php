<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık ve Yeni İzin Butonu -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($pageTitle) ?></h1>
            <p class="text-gray-600 mt-2">Sistem izinlerini yönetin</p>
        </div>
        <a href="/admin/permissions/create" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Yeni İzin
        </a>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Kategori Filtreleri -->
    <?php if (!empty($categories)): ?>
        <div class="mb-6 bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex flex-wrap gap-2">
                <button onclick="filterByCategory('all')" 
                        class="filter-btn px-3 py-1 text-sm rounded-lg border border-gray-300 hover:bg-gray-50 active"
                        data-category="all">
                    Tümü
                </button>
                <?php foreach ($categories as $category): ?>
                    <button onclick="filterByCategory('<?= $category ?>')" 
                            class="filter-btn px-3 py-1 text-sm rounded-lg border border-gray-300 hover:bg-gray-50"
                            data-category="<?= $category ?>">
                        <?= ucfirst($category) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- İzinler Tablosu -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Sistem İzinleri</h3>
        </div>
        
        <?php if (empty($permissions)): ?>
            <div class="p-6 text-center">
                <i class="fas fa-key text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">Henüz izin eklenmemiş</p>
                <p class="text-gray-400 text-sm mt-2">İlk izni eklemek için "Yeni İzin" butonuna tıklayın</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İzin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Açıklama
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Roller
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
                        <?php foreach ($permissions as $permission): ?>
                            <tr class="hover:bg-gray-50 permission-row" data-category="<?= $permission['category'] ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= esc($permission['display_name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= esc($permission['name']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= ucfirst($permission['category']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs">
                                        <?= esc($permission['description']) ?: 'Açıklama yok' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <?php if ($permission['role_names']): ?>
                                            <?= esc($permission['role_names']) ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">Hiçbir role atanmamış</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($permission['is_active']): ?>
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
                                        <a href="/admin/permissions/edit/<?= $permission['id'] ?>" 
                                           class="text-purple-600 hover:text-purple-900"
                                           title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deletePermission(<?= $permission['id'] ?>, '<?= esc($permission['display_name']) ?>')" 
                                                class="text-red-600 hover:text-red-900"
                                                title="Sil">
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
            <h3 class="text-lg font-medium text-gray-900">İzin Sil</h3>
        </div>
        <p class="text-gray-600 mb-6">
            <span id="permissionName"></span> iznini silmek istediğinizden emin misiniz? 
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
let permissionToDelete = null;

function deletePermission(id, name) {
    permissionToDelete = id;
    document.getElementById('permissionName').textContent = name;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
    permissionToDelete = null;
}

function filterByCategory(category) {
    const rows = document.querySelectorAll('.permission-row');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Buton durumlarını güncelle
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-purple-100', 'text-purple-700', 'border-purple-300');
        if (btn.dataset.category === category) {
            btn.classList.add('active', 'bg-purple-100', 'text-purple-700', 'border-purple-300');
        }
    });
    
    // Satırları filtrele
    rows.forEach(row => {
        if (category === 'all' || row.dataset.category === category) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (permissionToDelete) {
        fetch(`/admin/permissions/delete/${permissionToDelete}`, {
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