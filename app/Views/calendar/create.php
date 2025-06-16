<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                    <p class="text-gray-600">Adım adım randevu oluşturun</p>
                </div>
                <a href="/calendar" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Takvime Dön
                </a>
            </div>
        </div>

        <!-- Sihirbaz Adımları -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-8">
                        <!-- Adım 1: Müşteri -->
                        <div class="flex items-center" id="step-1-indicator">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-medium">1</div>
                            <span class="ml-2 text-sm font-medium text-gray-900">Müşteri Seçimi</span>
                        </div>
                        
                        <!-- Çizgi -->
                        <div class="flex-1 h-0.5 bg-gray-200" id="line-1"></div>
                        
                        <!-- Adım 2: Hizmet/Paket -->
                        <div class="flex items-center" id="step-2-indicator">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-gray-600 text-sm font-medium">2</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Hizmet/Paket</span>
                        </div>
                        
                        <!-- Çizgi -->
                        <div class="flex-1 h-0.5 bg-gray-200" id="line-2"></div>
                        
                        <!-- Adım 3: Personel -->
                        <div class="flex items-center" id="step-3-indicator">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-gray-600 text-sm font-medium">3</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Personel</span>
                        </div>
                        
                        <!-- Çizgi -->
                        <div class="flex-1 h-0.5 bg-gray-200" id="line-3"></div>
                        
                        <!-- Adım 4: Tarih/Saat -->
                        <div class="flex items-center" id="step-4-indicator">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-gray-600 text-sm font-medium">4</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Tarih & Saat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" action="/calendar/create" id="appointment-form">
                <?= csrf_field() ?>
                
                <!-- Hata Mesajları -->
                <?php if (session()->getFlashdata('errors')): ?>
                <div class="m-6 bg-red-50 border border-red-200 rounded-lg p-4">
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

                <!-- Gizli Alanlar -->
                <?php if ($userRole !== 'admin' || count($branches) <= 1): ?>
                <input type="hidden" name="branch_id" value="<?= $userBranchId ?>">
                <?php endif; ?>
                
                <!-- ADIM 1: MÜŞTERİ SEÇİMİ -->
                <div id="step-1" class="step-content p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Adım 1: Müşteri Seçimi</h2>
                        <p class="text-gray-600">Randevu alacak müşteriyi seçin veya yeni müşteri ekleyin</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Sol: Müşteri Arama -->
                        <div>
                            <h3 class="text-md font-medium text-gray-900 mb-4">Mevcut Müşteri</h3>
                            
                            <!-- Şube Seçimi (Sadece Admin için) -->
                            <?php if ($userRole === 'admin' && count($branches) > 1): ?>
                            <div class="mb-4">
                                <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">Şube *</label>
                                <select id="branch_id" name="branch_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Şube Seçiniz</option>
                                    <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= (old('branch_id', $formData['branch_id'] ?? '') == $branch['id']) ? 'selected' : '' ?>>
                                        <?= esc($branch['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Müşteri Arama -->
                            <div class="mb-4">
                                <label for="customer-search" class="block text-sm font-medium text-gray-700 mb-2">Müşteri Ara</label>
                                <div class="relative">
                                    <input type="text" id="customer-search" placeholder="Ad, soyad, telefon veya e-posta ile arayın..." 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Arama Sonuçları -->
                            <div id="customer-search-results" class="hidden mb-4">
                                <div class="border border-gray-200 rounded-lg max-h-60 overflow-y-auto">
                                    <!-- Dinamik olarak doldurulacak -->
                                </div>
                            </div>
                            
                            <!-- Seçilen Müşteri -->
                            <div id="selected-customer" class="hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-blue-900" id="selected-customer-name"></h4>
                                            <p class="text-sm text-blue-700" id="selected-customer-phone"></p>
                                        </div>
                                        <button type="button" id="clear-customer" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="customer_id" name="customer_id">
                            </div>
                        </div>

                        <!-- Sağ: Yeni Müşteri Ekleme -->
                        <div>
                            <h3 class="text-md font-medium text-gray-900 mb-4">Yeni Müşteri Ekle</h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="new-customer-first-name" class="block text-sm font-medium text-gray-700 mb-1">Ad *</label>
                                        <input type="text" id="new-customer-first-name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="new-customer-last-name" class="block text-sm font-medium text-gray-700 mb-1">Soyad *</label>
                                        <input type="text" id="new-customer-last-name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="new-customer-phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
                                    <input type="tel" id="new-customer-phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label for="new-customer-email" class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                    <input type="email" id="new-customer-email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <button type="button" id="add-new-customer" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Müşteri Ekle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ADIM 2: HİZMET/PAKET SEÇİMİ -->
                <div id="step-2" class="step-content hidden p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Adım 2: Hizmet/Paket Seçimi</h2>
                        <p class="text-gray-600">Müşterinin mevcut paketlerini kullanın veya yeni hizmet seçin</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Sol: Mevcut Paketler -->
                        <div>
                            <h3 class="text-md font-medium text-gray-900 mb-4">Mevcut Paketler</h3>
                            <div id="customer-packages-list">
                                <div class="text-gray-500 text-center py-8">
                                    <i class="fas fa-box-open text-3xl mb-2"></i>
                                    <p>Önce müşteri seçiniz</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sağ: Normal Hizmetler -->
                        <div>
                            <h3 class="text-md font-medium text-gray-900 mb-4">Normal Hizmetler</h3>
                            <div>
                                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">Hizmet Seçiniz</label>
                                <select id="service_id" name="service_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Hizmet Seçiniz</option>
                                    <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['id'] ?>" 
                                            data-duration="<?= $service['duration'] ?>" 
                                            data-price="<?= $service['price'] ?>"
                                            <?= (old('service_id', $formData['service_id'] ?? '') == $service['id']) ? 'selected' : '' ?>>
                                        <?= esc($service['category_name']) ?> - <?= esc($service['name']) ?> (<?= $service['duration'] ?> dk - <?= $service['price'] ?> ₺)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Seçilen Hizmet Detayları -->
                            <div id="selected-service-details" class="hidden mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Hizmet Detayları</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Süre:</span>
                                        <span id="service-duration" class="font-medium"></span> dakika
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Fiyat:</span>
                                        <span id="service-price" class="font-medium"></span> ₺
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gizli alanlar -->
                    <input type="hidden" id="customer_package_id" name="customer_package_id">
                    <input type="hidden" id="duration" name="duration">
                    <input type="hidden" id="price" name="price">
                </div>

                <!-- ADIM 3: PERSONEL SEÇİMİ -->
                <div id="step-3" class="step-content hidden p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Adım 3: Personel Seçimi</h2>
                        <p class="text-gray-600">Bu hizmeti verebilen personellerden birini seçin</p>
                    </div>

                    <div id="staff-selection">
                        <div class="text-gray-500 text-center py-8">
                            <i class="fas fa-user-friends text-3xl mb-2"></i>
                            <p>Önce hizmet seçiniz</p>
                        </div>
                    </div>
                    
                    <input type="hidden" id="staff_id" name="staff_id">
                </div>

                <!-- ADIM 4: TARİH VE SAAT SEÇİMİ -->
                <div id="step-4" class="step-content hidden p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Adım 4: Tarih & Saat Seçimi</h2>
                        <p class="text-gray-600">Randevu tarih ve saatini belirleyin</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Sol: Tarih Seçimi -->
                        <div>
                            <h3 class="text-md font-medium text-gray-900 mb-4">Tarih Seçimi</h3>
                            <div>
                                <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">Randevu Tarihi *</label>
                                <input type="date" id="appointment_date" name="appointment_date" required 
                                       value="<?= old('appointment_date', $formData['appointment_date'] ?? date('Y-m-d')) ?>"
                                       min="<?= date('Y-m-d') ?>"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Tekrar Eden Randevu Seçenekleri -->
                            <div class="mt-6">
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" id="recurring-appointment" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="recurring-appointment" class="ml-2 text-sm font-medium text-gray-700">Tekrar eden randevu oluştur</label>
                                </div>
                                
                                <div id="recurring-options" class="hidden space-y-4">
                                    <div>
                                        <label for="recurring-type" class="block text-sm font-medium text-gray-700 mb-2">Tekrar Sıklığı</label>
                                        <select id="recurring-type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="weekly">Haftalık</option>
                                            <option value="biweekly">2 Haftada Bir</option>
                                            <option value="monthly">Aylık</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="recurring-count" class="block text-sm font-medium text-gray-700 mb-2">Kaç Kez Tekrarlanacak</label>
                                        <input type="number" id="recurring-count" min="2" max="12" value="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sağ: Saat Seçimi -->
                        <div>
                            <h3 class="text-md font-medium text-gray-900 mb-4">Saat Seçimi</h3>
                            
                            <!-- Manuel Saat Girişi -->
                            <div class="mb-4">
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Saati</label>
                                <input type="time" id="start_time" name="start_time" 
                                       value="<?= old('start_time', $formData['start_time'] ?? '09:00') ?>"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Uygun Saatler -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Uygun Saatler</h4>
                                <div id="available-time-slots" class="grid grid-cols-3 gap-2">
                                    <div class="text-gray-500 text-center py-4 col-span-3">
                                        <i class="fas fa-clock text-2xl mb-2"></i>
                                        <p class="text-sm">Tarih seçildikten sonra uygun saatler gösterilecek</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notlar -->
                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Randevu Notları</label>
                        <textarea id="notes" name="notes" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Randevu ile ilgili özel notlar..."><?= old('notes', $formData['notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Navigasyon Butonları -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <button type="button" id="prev-step" class="hidden bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Önceki
                    </button>
                    
                    <div class="flex space-x-3 ml-auto">
                        <a href="/calendar" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                            İptal
                        </a>
                        <button type="button" id="next-step" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Sonraki <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        <button type="submit" id="submit-btn" class="hidden bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>Randevu Oluştur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global değişkenler
window.userBranchId = <?= $userBranchId ?>;
</script>
<script src="/assets/js/appointment-wizard.js"></script>

<?= $this->endSection() ?>