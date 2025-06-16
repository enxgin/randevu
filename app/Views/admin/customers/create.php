<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= esc($pageTitle ?? 'Yeni Müşteri Oluştur') ?></h1>
                    <p class="text-gray-600">Yeni müşteri bilgilerini girin</p>
                </div>
                <a href="<?= site_url('/admin/customers') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Geri Dön
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php
            $errors = session()->getFlashdata('errors');
            $formData = session()->getFlashdata('formData') ?? [];
        ?>
        <?php if (!empty($errors)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex">
                <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                <div>
                    <p class="font-medium">Lütfen aşağıdaki hataları düzeltin:</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Müşteri Bilgileri</h3>
            </div>
            
            <form action="<?= site_url('/admin/customers/create') ?>" method="post" class="p-6">
        <?= csrf_field() ?>

                <?= csrf_field() ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ad -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Ad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['first_name']) ? 'border-red-300' : '' ?>"
                               placeholder="Müşteri adı" value="<?= esc($formData['first_name'] ?? '', 'attr') ?>">
                        <?php if (isset($errors['first_name'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['first_name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Soyad -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Soyad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['last_name']) ? 'border-red-300' : '' ?>"
                               placeholder="Müşteri soyadı" value="<?= esc($formData['last_name'] ?? '', 'attr') ?>">
                        <?php if (isset($errors['last_name'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['last_name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Telefon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefon Numarası <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['phone']) ? 'border-red-300' : '' ?>"
                               placeholder="05XXXXXXXXX" value="<?= esc($formData['phone'] ?? '', 'attr') ?>">
                        <?php if (isset($errors['phone'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['phone']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- E-posta -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            E-posta Adresi
                        </label>
                        <input type="email" id="email" name="email"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['email']) ? 'border-red-300' : '' ?>"
                               placeholder="musteri@example.com" value="<?= esc($formData['email'] ?? '', 'attr') ?>">
                        <?php if (isset($errors['email'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['email']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Şube -->
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Şube <span class="text-red-500">*</span>
                        </label>
                        <?php if (isset($userRole) && $userRole === 'admin') : ?>
                            <select id="branch_id" name="branch_id" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['branch_id']) ? 'border-red-300' : '' ?>">
                                <option value="">Şube Seçiniz</option>
                                <?php if (!empty($branches)): ?>
                                    <?php foreach ($branches as $branch): ?>
                                        <option value="<?= esc($branch['id'], 'attr') ?>" <?= (isset($formData['branch_id']) && $formData['branch_id'] == $branch['id']) ? 'selected' : '' ?>>
                                            <?= esc($branch['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        <?php else : ?>
                            <!-- Manager için sadece görüntüleme -->
                            <div class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-700">
                                <?= esc($branches[0]['name'] ?? 'Şube Bulunamadı') ?>
                            </div>
                            <input type="hidden" name="branch_id" value="<?= isset($userBranchId) ? $userBranchId : ($branches[0]['id'] ?? '') ?>">
                        <?php endif; ?>
                        <?php if (isset($errors['branch_id'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['branch_id']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Doğum Tarihi -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Doğum Tarihi
                        </label>
                        <input type="date" id="birth_date" name="birth_date"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['birth_date']) ? 'border-red-300' : '' ?>"
                               value="<?= esc($formData['birth_date'] ?? '', 'attr') ?>">
                        <?php if (isset($errors['birth_date'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['birth_date']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Notlar -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Müşteri Notları
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['notes']) ? 'border-red-300' : '' ?>"
                              placeholder="Müşteri hakkında özel notlar, alerjiler, tercihler vb."><?= esc($formData['notes'] ?? '', 'html') ?></textarea>
                    <?php if (isset($errors['notes'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= esc($errors['notes']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Etiketler -->
                <div class="mt-6">
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                        Etiketler
                    </label>
                    <input type="text" id="tags" name="tags"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= isset($errors['tags']) ? 'border-red-300' : '' ?>"
                           placeholder="VIP, Sorunlu Müşteri, İlk Ziyaret (virgülle ayırın)" value="<?= esc($formData['tags'] ?? '', 'attr') ?>">
                    <p class="mt-1 text-sm text-gray-500">Etiketleri virgül (,) ile ayırarak giriniz.</p>
                    <?php if (isset($errors['tags'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= esc($errors['tags']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Form Butonları -->
                <div class="mt-8 flex items-center justify-between">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Müşteriyi Kaydet
                    </button>
                    <a href="<?= site_url('/admin/customers') ?>" class="text-gray-600 hover:text-gray-800 font-medium">
                        İptal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>