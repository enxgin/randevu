<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık -->
    <div class="flex items-center mb-6">
        <a href="/admin/roles" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($pageTitle) ?></h1>
            <p class="text-gray-600 mt-2"><?= esc($role['display_name']) ?> rolünü düzenleyin</p>
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
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Rol Kodu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= old('name', $role['name']) ?>"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Rol Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="display_name" 
                           name="display_name" 
                           value="<?= old('display_name', $role['display_name']) ?>"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Açıklama
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"><?= old('description', $role['description']) ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               <?= old('is_active', $role['is_active']) ? 'checked' : '' ?>
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Rol aktif
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- İzinler -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">İzinler</h3>
            </div>
            
            <div class="p-6">
                <?php foreach ($permissions as $category => $categoryPermissions): ?>
                    <div class="mb-6 border border-gray-200 rounded-lg">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 capitalize">
                                <?= ucfirst($category) ?> İzinleri
                            </h4>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <?php foreach ($categoryPermissions as $permission): ?>
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="permission_<?= $permission['id'] ?>" 
                                               name="permissions[]" 
                                               value="<?= $permission['id'] ?>"
                                               <?= in_array($permission['id'], $rolePermissions) ? 'checked' : '' ?>
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
                Güncelle
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>