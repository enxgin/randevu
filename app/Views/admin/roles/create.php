<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık ve Geri Dön Butonu -->
    <div class="flex items-center mb-6">
        <a href="/admin/roles" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($pageTitle) ?></h1>
            <p class="text-gray-600 mt-2">Sisteme yeni bir rol ve izinleri ekleyin</p>
        </div>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" class="space-y-6">
        <!-- Rol Bilgileri -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Rol Bilgileri</h3>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Rol Kodu -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Rol Kodu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= old('name') ?>"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Örn: branch_manager">
                    <p class="text-xs text-gray-500 mt-1">Benzersiz olmalı, küçük harf ve alt çizgi kullanın</p>
                </div>

                <!-- Rol Adı -->
                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Rol Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="display_name" 
                           name="display_name" 
                           value="<?= old('display_name') ?>"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Örn: Şube Müdürü">
                </div>

                <!-- Açıklama -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Açıklama
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                              placeholder="Rolün görev ve sorumlulukları..."><?= old('description') ?></textarea>
                </div>

                <!-- Durum -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               <?= old('is_active') ? 'checked' : 'checked' ?>
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Rol aktif
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pasif roller sisteme giriş yapamaz</p>
                </div>
            </div>
        </div>

        <!-- İzinler -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">İzinler</h3>
                <p class="text-sm text-gray-600 mt-1">Bu role atanacak izinleri seçin</p>
            </div>
            
            <div class="p-6">
                <?php if (empty($permissions)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-key text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">Henüz izin tanımlanmamış</p>
                        <p class="text-gray-400 text-sm mt-2">
                            <a href="/admin/permissions/create" class="text-blue-600 hover:text-blue-800">İzin oluşturmak için tıklayın</a>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <!-- Tümünü Seç/Kaldır -->
                        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                            <h4 class="text-sm font-medium text-gray-900">İzin Seçimi</h4>
                            <div class="space-x-2">
                                <button type="button" onclick="selectAllPermissions()" 
                                        class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                                    Tümünü Seç
                                </button>
                                <button type="button" onclick="clearAllPermissions()" 
                                        class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">
                                    Tümünü Kaldır
                                </button>
                            </div>
                        </div>

                        <!-- İzin Kategorileri -->
                        <?php foreach ($permissions as $category => $categoryPermissions): ?>
                            <div class="border border-gray-200 rounded-lg">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-gray-900 capitalize">
                                            <?= ucfirst($category) ?> İzinleri
                                        </h4>
                                        <button type="button" onclick="toggleCategory('<?= $category ?>')" 
                                                class="text-xs text-blue-600 hover:text-blue-800">
                                            Kategoriye Göre Seç/Kaldır
                                        </button>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <?php foreach ($categoryPermissions as $permission): ?>
                                            <div class="flex items-center">
                                                <input type="checkbox" 
                                                       id="permission_<?= $permission['id'] ?>" 
                                                       name="permissions[]" 
                                                       value="<?= $permission['id'] ?>"
                                                       data-category="<?= $category ?>"
                                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                                <label for="permission_<?= $permission['id'] ?>" 
                                                       class="ml-2 text-sm text-gray-900">
                                                    <?= esc($permission['display_name']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Butonları -->
        <div class="flex justify-end space-x-3">
            <a href="/admin/roles" 
               class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                İptal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-save mr-2"></i>
                Rol Oluştur
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function selectAllPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function clearAllPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function toggleCategory(category) {
    const checkboxes = document.querySelectorAll(`input[data-category="${category}"]`);
    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}

// Rol kodunu otomatik oluştur
document.getElementById('display_name').addEventListener('input', function() {
    const nameField = document.getElementById('name');
    if (!nameField.value || nameField.dataset.autoGenerated) {
        const cleanName = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '_')
            .substring(0, 50);
        nameField.value = cleanName;
        nameField.dataset.autoGenerated = 'true';
    }
});

document.getElementById('name').addEventListener('input', function() {
    delete this.dataset.autoGenerated;
});
</script>
<?= $this->endSection() ?>