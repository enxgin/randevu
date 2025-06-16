<?= $this->extend('layouts/app') ?>

<?php $this->section('title') ?><?= $title ?><?php $this->endSection() ?>
<?php $this->section('pageTitle') ?><?= $pageTitle ?><?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Yeni Hizmet Kategorisi</h1>
        <a href="<?= base_url('admin/service-categories') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Geri Dön</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Kategori Bilgileri</h3>
        </div>
        
        <form action="<?= base_url('admin/service-categories/create') ?>" method="post" class="p-6 space-y-6">
            <?= csrf_field() ?>
            
            <!-- Error Messages -->
            <?php if (session()->getFlashdata('errors')) : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Kategori Adı -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Kategori Adı</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       placeholder="Kategori Adı Girin" 
                       value="<?= old('name', $formData['name'] ?? '') ?>" 
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Şube Seçimi -->
            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">Şube</label>
                <?php if ($userRole === 'admin') : ?>
                    <select id="branch_id"
                            name="branch_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Genel Kategori (Tüm Şubeler İçin)</option>
                        <?php if (!empty($branches)) : ?>
                            <?php foreach ($branches as $branch) : ?>
                                <option value="<?= $branch['id'] ?>" <?= (old('branch_id', $formData['branch_id'] ?? '') == $branch['id']) ? 'selected' : '' ?>>
                                    <?= esc($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Bu kategorinin sadece belirli bir şubeye ait olmasını istiyorsanız seçin. Boş bırakırsanız tüm şubelerde geçerli olur.</p>
                <?php else : ?>
                    <!-- Manager için sadece kendi şubesi -->
                    <input type="hidden" name="branch_id" value="<?= $userBranchId ?>">
                    <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700">
                        <?= esc($branches[0]['name'] ?? 'Şube Bulunamadı') ?>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Bu kategori sadece kendi şubenizde geçerli olacaktır.</p>
                <?php endif; ?>
            </div>

            <!-- Açıklama -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          placeholder="Kategori açıklaması (opsiyonel)"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= old('description', $formData['description'] ?? '') ?></textarea>
            </div>

            <!-- Aktif Durumu -->
            <div class="flex items-center">
                <input type="checkbox" 
                       id="is_active" 
                       name="is_active" 
                       value="1" 
                       <?= (old('is_active', $formData['is_active'] ?? 1) == 1) ? 'checked' : '' ?>
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif mi?</label>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/service-categories') ?>" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    İptal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection() ?>