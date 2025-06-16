<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Başlık ve Geri Dön Butonu -->
    <div class="flex items-center mb-6">
        <a href="/admin/branches" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($pageTitle) ?></h1>
            <p class="text-gray-600 mt-2">Sisteme yeni bir şube ekleyin</p>
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
            <h3 class="text-lg font-medium text-gray-900">Şube Bilgileri</h3>
        </div>
        
        <form method="POST" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Şube Adı -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Şube Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= old('name') ?>"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Örn: Merkez Şube">
                </div>

                <!-- Telefon -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Telefon
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= old('phone') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0212 123 45 67">
                </div>

                <!-- E-posta -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        E-posta
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= old('email') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="merkez@salon.com">
                </div>

                <!-- Adres -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Adres
                    </label>
                    <textarea id="address" 
                              name="address" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Şube adresi..."><?= old('address') ?></textarea>
                </div>

                <!-- Çalışma Saatleri -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Çalışma Saatleri
                    </label>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="grid grid-cols-1 gap-4">
                            <?php
                            $days = [
                                'pazartesi' => 'Pazartesi',
                                'sali' => 'Salı',
                                'carsamba' => 'Çarşamba',
                                'persembe' => 'Perşembe',
                                'cuma' => 'Cuma',
                                'cumartesi' => 'Cumartesi',
                                'pazar' => 'Pazar'
                            ];
                            
                            // Eski değerleri parse et
                            $oldWorkingHours = [];
                            if (old('working_hours')) {
                                $oldWorkingHours = json_decode(old('working_hours'), true) ?: [];
                            }
                            ?>
                            
                            <?php foreach ($days as $dayKey => $dayName): ?>
                            <div class="flex items-center space-x-4 p-4 bg-white rounded-lg border border-gray-200">
                                <!-- Gün Adı -->
                                <div class="w-24 flex-shrink-0">
                                    <span class="text-sm font-medium text-gray-700"><?= $dayName ?></span>
                                </div>
                                
                                <!-- Açık/Kapalı Toggle -->
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           id="day_<?= $dayKey ?>_active"
                                           name="days[<?= $dayKey ?>][active]"
                                           value="1"
                                           <?= isset($oldWorkingHours[$dayKey]) ? 'checked' : '' ?>
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded day-toggle"
                                           data-day="<?= $dayKey ?>">
                                    <label for="day_<?= $dayKey ?>_active" class="ml-2 text-sm text-gray-600">
                                        Açık
                                    </label>
                                </div>
                                
                                <!-- Saat Seçimi -->
                                <div class="flex items-center space-x-2 day-hours" id="hours_<?= $dayKey ?>" style="<?= isset($oldWorkingHours[$dayKey]) ? '' : 'display: none;' ?>">
                                    <div class="flex items-center space-x-1">
                                        <label class="text-sm text-gray-600">Açılış:</label>
                                        <select name="days[<?= $dayKey ?>][start]"
                                                class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <?php for ($hour = 6; $hour <= 23; $hour++): ?>
                                                <?php for ($minute = 0; $minute < 60; $minute += 30): ?>
                                                    <?php
                                                    $timeValue = sprintf('%02d:%02d', $hour, $minute);
                                                    $selected = '';
                                                    if (isset($oldWorkingHours[$dayKey]['start']) && $oldWorkingHours[$dayKey]['start'] === $timeValue) {
                                                        $selected = 'selected';
                                                    } elseif (!isset($oldWorkingHours[$dayKey]) && $timeValue === '09:00') {
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                    <option value="<?= $timeValue ?>" <?= $selected ?>><?= $timeValue ?></option>
                                                <?php endfor; ?>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="flex items-center space-x-1">
                                        <label class="text-sm text-gray-600">Kapanış:</label>
                                        <select name="days[<?= $dayKey ?>][end]"
                                                class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <?php for ($hour = 6; $hour <= 23; $hour++): ?>
                                                <?php for ($minute = 0; $minute < 60; $minute += 30): ?>
                                                    <?php
                                                    $timeValue = sprintf('%02d:%02d', $hour, $minute);
                                                    $selected = '';
                                                    if (isset($oldWorkingHours[$dayKey]['end']) && $oldWorkingHours[$dayKey]['end'] === $timeValue) {
                                                        $selected = 'selected';
                                                    } elseif (!isset($oldWorkingHours[$dayKey]) && $timeValue === '18:00') {
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                    <option value="<?= $timeValue ?>" <?= $selected ?>><?= $timeValue ?></option>
                                                <?php endfor; ?>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Hızlı Ayarlar -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">Hızlı Ayarlar:</p>
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors quick-set"
                                        data-days="pazartesi,sali,carsamba,persembe,cuma"
                                        data-start="09:00"
                                        data-end="18:00">
                                    Hafta İçi (09:00-18:00)
                                </button>
                                <button type="button"
                                        class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors quick-set"
                                        data-days="cumartesi"
                                        data-start="10:00"
                                        data-end="17:00">
                                    Cumartesi (10:00-17:00)
                                </button>
                                <button type="button"
                                        class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors"
                                        onclick="clearAllDays()">
                                    Tümünü Temizle
                                </button>
                            </div>
                        </div>
                        
                        <!-- Hidden input for JSON data -->
                        <input type="hidden" id="working_hours" name="working_hours" value="<?= old('working_hours') ?>">
                    </div>
                </div>

                <!-- Durum -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               <?= old('is_active') ? 'checked' : 'checked' ?>
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Şube aktif
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pasif şubeler sisteme giriş yapamaz</p>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="/admin/branches" 
                   class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    İptal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Şube Oluştur
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gün toggle'ları için event listener
    document.querySelectorAll('.day-toggle').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const hoursDiv = document.getElementById('hours_' + day);
            
            if (this.checked) {
                hoursDiv.style.display = 'flex';
            } else {
                hoursDiv.style.display = 'none';
            }
            
            updateWorkingHoursJSON();
        });
    });
    
    // Saat değişikliklerini dinle
    document.querySelectorAll('select[name*="[start]"], select[name*="[end]"]').forEach(function(select) {
        select.addEventListener('change', updateWorkingHoursJSON);
    });
    
    // Hızlı ayar butonları
    document.querySelectorAll('.quick-set').forEach(function(button) {
        button.addEventListener('click', function() {
            const days = this.dataset.days.split(',');
            const startTime = this.dataset.start;
            const endTime = this.dataset.end;
            
            days.forEach(function(day) {
                // Checkbox'ı işaretle
                const checkbox = document.getElementById('day_' + day + '_active');
                const hoursDiv = document.getElementById('hours_' + day);
                const startSelect = document.querySelector('select[name="days[' + day + '][start]"]');
                const endSelect = document.querySelector('select[name="days[' + day + '][end]"]');
                
                if (checkbox && hoursDiv && startSelect && endSelect) {
                    checkbox.checked = true;
                    hoursDiv.style.display = 'flex';
                    startSelect.value = startTime;
                    endSelect.value = endTime;
                }
            });
            
            updateWorkingHoursJSON();
        });
    });
    
    // Form submit edildiğinde JSON'u güncelle
    document.querySelector('form').addEventListener('submit', function() {
        updateWorkingHoursJSON();
    });
    
    // Sayfa yüklendiğinde mevcut değerleri kontrol et
    updateWorkingHoursJSON();
});

// Tüm günleri temizle
function clearAllDays() {
    document.querySelectorAll('.day-toggle').forEach(function(checkbox) {
        checkbox.checked = false;
        const day = checkbox.dataset.day;
        const hoursDiv = document.getElementById('hours_' + day);
        hoursDiv.style.display = 'none';
    });
    updateWorkingHoursJSON();
}

// Çalışma saatleri JSON'unu güncelle
function updateWorkingHoursJSON() {
    const workingHours = {};
    
    document.querySelectorAll('.day-toggle').forEach(function(checkbox) {
        if (checkbox.checked) {
            const day = checkbox.dataset.day;
            const startSelect = document.querySelector('select[name="days[' + day + '][start]"]');
            const endSelect = document.querySelector('select[name="days[' + day + '][end]"]');
            
            if (startSelect && endSelect) {
                workingHours[day] = {
                    start: startSelect.value,
                    end: endSelect.value
                };
            }
        }
    });
    
    // JSON'u hidden input'a yaz
    const hiddenInput = document.getElementById('working_hours');
    if (hiddenInput) {
        hiddenInput.value = Object.keys(workingHours).length > 0 ? JSON.stringify(workingHours) : '';
    }
}
</script>
<?= $this->endSection() ?>