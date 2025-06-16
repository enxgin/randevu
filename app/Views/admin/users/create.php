<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık ve Geri Dön Butonu -->
    <div class="flex items-center mb-6">
        <a href="/admin/users" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($pageTitle) ?></h1>
            <p class="text-gray-600 mt-2">Sisteme yeni bir kullanıcı ekleyin</p>
        </div>
    </div>

    <div class="max-w-4xl">
        <!-- Flash Mesajları -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Aşağıdaki hataları düzeltin:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="/admin/users/create" method="POST" class="space-y-8">
            <?= csrf_field() ?>

            <!-- Temel Bilgiler -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Temel Bilgiler</h3>
                    <p class="mt-1 text-sm text-gray-500">Kullanıcının temel bilgilerini girin</p>
                </div>
                <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Şube Seçimi -->
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-1"></i>
                            Şube <span class="text-red-500">*</span>
                        </label>
                        <select id="branch_id" name="branch_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Şube Seçin</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= old('branch_id') == $branch['id'] ? 'selected' : '' ?>>
                                    <?= esc($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Rol Seçimi -->
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tag mr-1"></i>
                            Rol <span class="text-red-500">*</span>
                        </label>
                        <select id="role_id" name="role_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Rol Seçin</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                    <?= esc($role['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Ad -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>
                            Ad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" value="<?= old('first_name') ?>" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Adını girin">
                    </div>

                    <!-- Soyad -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>
                            Soyad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" value="<?= old('last_name') ?>" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Soyadını girin">
                    </div>

                    <!-- Kullanıcı Adı -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-at mr-1"></i>
                            Kullanıcı Adı <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="username" name="username" value="<?= old('username') ?>" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Kullanıcı adını girin">
                    </div>

                    <!-- E-posta -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1"></i>
                            E-posta <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="<?= old('email') ?>" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="E-posta adresini girin">
                    </div>

                    <!-- Telefon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-1"></i>
                            Telefon
                        </label>
                        <input type="tel" id="phone" name="phone" value="<?= old('phone') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Telefon numarasını girin">
                    </div>

                    <!-- Şifre -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>
                            Şifre <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Şifre girin (min. 6 karakter)">
                    </div>
                </div>
            </div>

            <!-- Çalışma Saatleri -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Çalışma Saatleri</h3>
                    <p class="mt-1 text-sm text-gray-500">Kullanıcının haftalık çalışma programını belirleyin</p>
                </div>
                <div class="px-6 py-6">
                    <div class="space-y-4">
                        <?php 
                        $days = [
                            'monday' => 'Pazartesi',
                            'tuesday' => 'Salı', 
                            'wednesday' => 'Çarşamba',
                            'thursday' => 'Perşembe',
                            'friday' => 'Cuma',
                            'saturday' => 'Cumartesi',
                            'sunday' => 'Pazar'
                        ];
                        ?>
                        <?php foreach ($days as $dayKey => $dayName): ?>
                            <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                <div class="w-20">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="<?= $dayKey ?>_working" value="1" 
                                               class="form-checkbox h-5 w-5 text-blue-600 working-day-checkbox" 
                                               data-day="<?= $dayKey ?>" <?= old($dayKey . '_working') ? 'checked' : '' ?>>
                                        <span class="ml-2 font-medium text-gray-700"><?= $dayName ?></span>
                                    </label>
                                </div>
                                <div class="flex items-center space-x-2 working-hours" data-day="<?= $dayKey ?>" style="<?= old($dayKey . '_working') ? '' : 'display: none;' ?>">
                                    <input type="time" name="<?= $dayKey ?>_start" value="<?= old($dayKey . '_start', '09:00') ?>"
                                           class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-gray-500">-</span>
                                    <input type="time" name="<?= $dayKey ?>_end" value="<?= old($dayKey . '_end', '18:00') ?>"
                                           class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Ek Ayarlar -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ek Ayarlar</h3>
                    <p class="mt-1 text-sm text-gray-500">Prim oranı ve durum ayarları</p>
                </div>
                <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prim Oranı -->
                    <div>
                        <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-percentage mr-1"></i>
                            Prim Oranı (%)
                        </label>
                        <input type="number" id="commission_rate" name="commission_rate" value="<?= old('commission_rate') ?>" 
                               min="0" max="100" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Varsayılan prim oranını girin">
                    </div>

                    <!-- Durum -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-toggle-on mr-1"></i>
                            Durum
                        </label>
                        <div class="flex items-center">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" <?= old('is_active', '1') ? 'checked' : '' ?>
                                       class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2 text-gray-700">Aktif kullanıcı</span>
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Pasif kullanıcılar sisteme giriş yapamaz</p>
                    </div>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="flex justify-end space-x-3">
                <a href="/admin/users" class="px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    İptal
                </a>
                <button type="submit" class="px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Kullanıcı Oluştur
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Çalışma günü checkbox'larını dinle
    const workingDayCheckboxes = document.querySelectorAll('.working-day-checkbox');
    
    workingDayCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const workingHours = document.querySelector(`.working-hours[data-day="${day}"]`);
            
            if (this.checked) {
                workingHours.style.display = 'flex';
            } else {
                workingHours.style.display = 'none';
            }
        });
    });

    // Sayfa yüklendiğinde mevcut durumları kontrol et
    workingDayCheckboxes.forEach(checkbox => {
        const day = checkbox.dataset.day;
        const workingHours = document.querySelector(`.working-hours[data-day="${day}"]`);
        
        if (checkbox.checked) {
            workingHours.style.display = 'flex';
        } else {
            workingHours.style.display = 'none';
        }
    });
});
</script>
<?= $this->endSection() ?>