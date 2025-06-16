<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                    <p class="text-gray-600">Randevu bilgilerini güncelleyin</p>
                </div>
                <a href="/calendar" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Takvime Dön
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Randevu Bilgileri</h2>
            </div>

            <form method="POST" action="/calendar/edit/<?= $appointment['id'] ?>" class="p-6">
                <?= csrf_field() ?>

                <!-- Hata Mesajları -->
                <?php if (session()->getFlashdata('errors')): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mt-0.5 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Aşağıdaki hatalar oluştu:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sol Kolon -->
                    <div class="space-y-6">
                        <!-- Müşteri Bilgisi (Salt Okunur) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Müşteri</label>
                            <div class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700">
                                <?= esc($appointment['customer_first_name'] . ' ' . $appointment['customer_last_name']) ?>
                                <?php if ($appointment['customer_phone']): ?>
                                - <?= esc($appointment['customer_phone']) ?>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="customer_id" value="<?= $appointment['customer_id'] ?>">
                        </div>

                        <!-- Hizmet Seçimi -->
                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">Hizmet *</label>
                            <select id="service_id" name="service_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Hizmet Seçiniz</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>" 
                                        data-duration="<?= $service['duration'] ?>" 
                                        data-price="<?= $service['price'] ?>"
                                        <?= ($formData['service_id'] == $service['id']) ? 'selected' : '' ?>>
                                    <?= esc($service['category_name']) ?> - <?= esc($service['name']) ?> (<?= $service['duration'] ?> dk - <?= $service['price'] ?> ₺)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Personel Seçimi -->
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">Personel *</label>
                            <select id="staff_id" name="staff_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Personel Seçiniz</option>
                                <?php foreach ($staff as $person): ?>
                                <option value="<?= $person['id'] ?>" <?= ($formData['staff_id'] == $person['id']) ? 'selected' : '' ?>>
                                    <?= esc($person['first_name'] . ' ' . $person['last_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Durum -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Durum *</label>
                            <select id="status" name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending" <?= ($formData['status'] == 'pending') ? 'selected' : '' ?>>Onay Bekliyor</option>
                                <option value="confirmed" <?= ($formData['status'] == 'confirmed') ? 'selected' : '' ?>>Onaylandı</option>
                                <option value="completed" <?= ($formData['status'] == 'completed') ? 'selected' : '' ?>>Tamamlandı</option>
                                <option value="cancelled" <?= ($formData['status'] == 'cancelled') ? 'selected' : '' ?>>İptal Edildi</option>
                                <option value="no_show" <?= ($formData['status'] == 'no_show') ? 'selected' : '' ?>>Gelmedi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sağ Kolon -->
                    <div class="space-y-6">
                        <!-- Tarih -->
                        <div>
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">Tarih *</label>
                            <input type="date" id="appointment_date" name="appointment_date" required 
                                   value="<?= $formData['appointment_date'] ?>"
                                   min="<?= date('Y-m-d') ?>"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Saat -->
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Saati *</label>
                            <input type="time" id="start_time" name="start_time" required 
                                   value="<?= $formData['start_time'] ?>"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Süre -->
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Süre (Dakika) *</label>
                            <input type="number" id="duration" name="duration" required min="15" max="480" step="15"
                                   value="<?= $formData['duration'] ?>"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Fiyat -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Fiyat (₺) *</label>
                            <input type="number" id="price" name="price" required min="0" step="0.01"
                                   value="<?= $formData['price'] ?>"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Notlar -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                            <textarea id="notes" name="notes" rows="3" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Randevu ile ilgili notlar..."><?= esc($formData['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Randevu Geçmişi -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Randevu Geçmişi</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Oluşturulma:</span>
                                <div class="text-gray-600"><?= date('d.m.Y H:i', strtotime($appointment['created_at'])) ?></div>
                                <?php if ($appointment['creator_first_name']): ?>
                                <div class="text-gray-500">Oluşturan: <?= esc($appointment['creator_first_name'] . ' ' . $appointment['creator_last_name']) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if ($appointment['confirmed_at']): ?>
                            <div>
                                <span class="font-medium text-gray-700">Onaylanma:</span>
                                <div class="text-gray-600"><?= date('d.m.Y H:i', strtotime($appointment['confirmed_at'])) ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if ($appointment['completed_at']): ?>
                            <div>
                                <span class="font-medium text-gray-700">Tamamlanma:</span>
                                <div class="text-gray-600"><?= date('d.m.Y H:i', strtotime($appointment['completed_at'])) ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if ($appointment['cancelled_at']): ?>
                            <div>
                                <span class="font-medium text-gray-700">İptal:</span>
                                <div class="text-gray-600"><?= date('d.m.Y H:i', strtotime($appointment['cancelled_at'])) ?></div>
                                <?php if ($appointment['cancellation_reason']): ?>
                                <div class="text-gray-500">Sebep: <?= esc($appointment['cancellation_reason']) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Müsaitlik Kontrolü -->
                <div id="availability-check" class="mt-6 p-4 rounded-lg border hidden">
                    <div class="flex items-center">
                        <div id="availability-spinner" class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-3"></div>
                        <span id="availability-text" class="text-sm text-gray-600">Müsaitlik kontrol ediliyor...</span>
                    </div>
                </div>

                <!-- Form Butonları -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="/calendar" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                        İptal
                    </a>
                    <button type="submit" id="submit-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const staffSelect = document.getElementById('staff_id');
    const durationInput = document.getElementById('duration');
    const priceInput = document.getElementById('price');
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('start_time');
    const availabilityCheck = document.getElementById('availability-check');
    const availabilityText = document.getElementById('availability-text');
    const submitBtn = document.getElementById('submit-btn');

    // Hizmet değiştiğinde
    serviceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Süre ve fiyatı otomatik doldur
            const duration = selectedOption.dataset.duration;
            const price = selectedOption.dataset.price;
            
            if (duration) durationInput.value = duration;
            if (price) priceInput.value = price;
        }
    });

    // Müsaitlik kontrolü için event listener'lar
    [staffSelect, dateInput, timeInput, durationInput].forEach(element => {
        element.addEventListener('change', checkAvailability);
    });

    // Müsaitlik kontrolü
    function checkAvailability() {
        const staffId = staffSelect.value;
        const date = dateInput.value;
        const startTime = timeInput.value;
        const duration = durationInput.value;
        
        if (!staffId || !date || !startTime || !duration) {
            availabilityCheck.classList.add('hidden');
            return;
        }
        
        availabilityCheck.classList.remove('hidden');
        availabilityCheck.className = 'mt-6 p-4 rounded-lg border bg-blue-50 border-blue-200';
        availabilityText.textContent = 'Müsaitlik kontrol ediliyor...';
        submitBtn.disabled = true;
        
        const formData = new FormData();
        formData.append('staff_id', staffId);
        formData.append('date', date);
        formData.append('start_time', startTime);
        formData.append('duration', duration);
        formData.append('exclude_id', <?= $appointment['id'] ?>); // Mevcut randevuyu hariç tut
        
        fetch('/calendar/check-availability', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                availabilityCheck.className = 'mt-6 p-4 rounded-lg border bg-green-50 border-green-200';
                availabilityText.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-2"></i>Personel müsait';
                submitBtn.disabled = false;
            } else {
                availabilityCheck.className = 'mt-6 p-4 rounded-lg border bg-red-50 border-red-200';
                availabilityText.innerHTML = '<i class="fas fa-times-circle text-red-600 mr-2"></i>Personel bu saatte müsait değil';
                submitBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            availabilityCheck.className = 'mt-6 p-4 rounded-lg border bg-yellow-50 border-yellow-200';
            availabilityText.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>Kontrol edilemedi';
            submitBtn.disabled = false; // Hata durumunda form gönderilsin
        });
    }
});
</script>

<?= $this->endSection() ?>