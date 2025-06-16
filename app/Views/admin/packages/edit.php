<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= $pageTitle ?></h1>
            <p class="text-gray-600 mt-1"><?= esc($package['name']) ?> paketini düzenleyin</p>
        </div>
        <div class="flex space-x-3">
            <a href="/admin/packages/view/<?= $package['id'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-eye mr-2"></i>
                Görüntüle
            </a>
            <a href="/admin/packages" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
        </div>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="/admin/packages/edit/<?= $package['id'] ?>" method="POST" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sol Kolon -->
                <div class="space-y-6">
                    <!-- Temel Bilgiler -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Temel Bilgiler</h3>
                        
                        <!-- Şube -->
                        <div class="mb-4">
                            <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-1">Şube *</label>
                            <select name="branch_id" id="branch_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Şube Seçin</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= ($formData['branch_id'] == $branch['id']) ? 'selected' : '' ?>>
                                        <?= esc($branch['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Paket Adı -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Paket Adı *</label>
                            <input type="text" name="name" id="name" required 
                                   value="<?= esc($formData['name']) ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Örn: 10 Seans Lazer Epilasyon">
                        </div>

                        <!-- Açıklama -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Paket hakkında detaylı bilgi..."><?= esc($formData['description']) ?></textarea>
                        </div>
                    </div>

                    <!-- Paket Türü ve Miktar -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Paket Türü</h3>
                        
                        <!-- Tür Seçimi -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Paket Türü *</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="session" 
                                           <?= ($formData['type'] === 'session') ? 'checked' : '' ?>
                                           class="mr-2" onchange="togglePackageType()">
                                    <span class="text-sm text-gray-700">Adet Bazlı (Seans sayısı)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="time" 
                                           <?= ($formData['type'] === 'time') ? 'checked' : '' ?>
                                           class="mr-2" onchange="togglePackageType()">
                                    <span class="text-sm text-gray-700">Dakika Bazlı (Toplam süre)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Seans Sayısı -->
                        <div id="sessionField" class="mb-4">
                            <label for="total_sessions" class="block text-sm font-medium text-gray-700 mb-1">Toplam Seans Sayısı</label>
                            <input type="number" name="total_sessions" id="total_sessions" min="1"
                                   value="<?= esc($formData['total_sessions']) ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Örn: 10">
                        </div>

                        <!-- Dakika -->
                        <div id="timeField" class="mb-4 hidden">
                            <label for="total_minutes" class="block text-sm font-medium text-gray-700 mb-1">Toplam Dakika</label>
                            <input type="number" name="total_minutes" id="total_minutes" min="1"
                                   value="<?= esc($formData['total_minutes']) ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Örn: 300">
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon -->
                <div class="space-y-6">
                    <!-- Fiyat ve Geçerlilik -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Fiyat ve Geçerlilik</h3>
                        
                        <!-- Fiyat -->
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Fiyat (₺) *</label>
                            <input type="number" name="price" id="price" step="0.01" min="0" required
                                   value="<?= esc($formData['price']) ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                        </div>

                        <!-- Geçerlilik Süresi -->
                        <div class="mb-4">
                            <label for="validity_months" class="block text-sm font-medium text-gray-700 mb-1">Geçerlilik Süresi (Ay) *</label>
                            <input type="number" name="validity_months" id="validity_months" min="1" required
                                   value="<?= esc($formData['validity_months']) ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="6">
                            <p class="text-xs text-gray-500 mt-1">Satın alımdan itibaren kaç ay geçerli olacak</p>
                        </div>

                        <!-- Durum -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                       <?= ($formData['is_active']) ? 'checked' : '' ?>
                                       class="mr-2">
                                <span class="text-sm text-gray-700">Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Kapsanan Hizmetler -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Kapsanan Hizmetler</h3>
                        <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3">
                            <?php if (empty($services)): ?>
                                <p class="text-gray-500 text-sm">Henüz hizmet tanımlanmamış</p>
                            <?php else: ?>
                                <?php 
                                $currentCategory = '';
                                foreach ($services as $service): 
                                    if ($currentCategory !== $service['category_name']):
                                        if ($currentCategory !== ''): ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mb-3">
                                            <h4 class="font-medium text-gray-800 mb-2"><?= esc($service['category_name']) ?></h4>
                                        <?php $currentCategory = $service['category_name'];
                                    endif; ?>
                                    
                                    <label class="flex items-center mb-1">
                                        <input type="checkbox" name="service_ids[]" value="<?= $service['id'] ?>"
                                               <?= in_array($service['id'], $assignedServiceIds) ? 'checked' : '' ?>
                                               class="mr-2">
                                        <span class="text-sm text-gray-700"><?= esc($service['name']) ?></span>
                                        <span class="text-xs text-gray-500 ml-auto">₺<?= number_format($service['price'], 2) ?></span>
                                    </label>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Bu paket hangi hizmetler için kullanılabilir?</p>
                    </div>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="/admin/packages" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    İptal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Paketi Güncelle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePackageType() {
    const sessionRadio = document.querySelector('input[name="type"][value="session"]');
    const timeRadio = document.querySelector('input[name="type"][value="time"]');
    const sessionField = document.getElementById('sessionField');
    const timeField = document.getElementById('timeField');
    const sessionInput = document.getElementById('total_sessions');
    const timeInput = document.getElementById('total_minutes');

    if (sessionRadio.checked) {
        sessionField.classList.remove('hidden');
        timeField.classList.add('hidden');
        sessionInput.required = true;
        timeInput.required = false;
        timeInput.value = '';
    } else if (timeRadio.checked) {
        sessionField.classList.add('hidden');
        timeField.classList.remove('hidden');
        sessionInput.required = false;
        timeInput.required = true;
        sessionInput.value = '';
    }
}

// Sayfa yüklendiğinde doğru alanları göster
document.addEventListener('DOMContentLoaded', function() {
    togglePackageType();
});
</script>
<?= $this->endSection() ?>