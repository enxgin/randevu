<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık ve Geri Dön Butonu -->
    <div class="flex items-center mb-6">
        <a href="/admin/permissions" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($pageTitle) ?></h1>
            <p class="text-gray-600 mt-2"><?= esc($permission['display_name']) ?> iznini düzenleyin</p>
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
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">İzin Bilgileri</h3>
        </div>
        
        <form method="POST" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- İzin Adı -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        İzin Kodu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= old('name', $permission['name']) ?>"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Örn: user.create">
                    <p class="text-xs text-gray-500 mt-1">Benzersiz olmalı, noktalı format kullanın</p>
                </div>

                <!-- Görünen İsim -->
                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Görünen İsim <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="display_name" 
                           name="display_name" 
                           value="<?= old('display_name', $permission['display_name']) ?>"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Örn: Kullanıcı Oluştur">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category" 
                            name="category" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Kategori Seçin</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category ?>" <?= old('category', $permission['category']) == $category ? 'selected' : '' ?>>
                                    <?= ucfirst($category) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <option value="user" <?= old('category', $permission['category']) == 'user' ? 'selected' : '' ?>>User</option>
                        <option value="appointment" <?= old('category', $permission['category']) == 'appointment' ? 'selected' : '' ?>>Appointment</option>
                        <option value="branch" <?= old('category', $permission['category']) == 'branch' ? 'selected' : '' ?>>Branch</option>
                        <option value="cash" <?= old('category', $permission['category']) == 'cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="customer" <?= old('category', $permission['category']) == 'customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="package" <?= old('category', $permission['category']) == 'package' ? 'selected' : '' ?>>Package</option>
                        <option value="payment" <?= old('category', $permission['category']) == 'payment' ? 'selected' : '' ?>>Payment</option>
                        <option value="report" <?= old('category', $permission['category']) == 'report' ? 'selected' : '' ?>>Report</option>
                        <option value="service" <?= old('category', $permission['category']) == 'service' ? 'selected' : '' ?>>Service</option>
                        <option value="setting" <?= old('category', $permission['category']) == 'setting' ? 'selected' : '' ?>>Setting</option>
                    </select>
                </div>

                <!-- Açıklama -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Açıklama
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="İznin ne işe yaradığını açıklayın..."><?= old('description', $permission['description']) ?></textarea>
                </div>

                <!-- Durum -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               <?= old('is_active', $permission['is_active']) ? 'checked' : '' ?>
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            İzin aktif
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pasif izinler rol atamalarında görünmez</p>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="/admin/permissions" 
                   class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    İptal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Güncelle
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>