<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 sm:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700"><?= esc($pageTitle ?? 'Müşteri Detayı') ?></h1>
        <div>
            <a href="<?= site_url('/admin/customers/edit/' . esc($customer['id'] ?? '', 'attr')) ?>" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                <i class="fas fa-edit mr-2"></i>Düzenle
            </a>
            <a href="<?= site_url('/admin/customers') ?>" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Listeye Dön
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Temel Bilgiler</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600"><strong class="font-medium text-gray-700">Ad Soyad:</strong> <?= esc($customer['first_name'] ?? '') ?> <?= esc($customer['last_name'] ?? '') ?></p>
                <p class="text-gray-600"><strong class="font-medium text-gray-700">Telefon:</strong> <?= esc($customer['phone'] ?? '-') ?></p>
                <p class="text-gray-600"><strong class="font-medium text-gray-700">E-posta:</strong> <?= esc($customer['email'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-gray-600"><strong class="font-medium text-gray-700">Şube:</strong> <?= esc($customer['branch_name'] ?? 'N/A') ?></p>
                <p class="text-gray-600"><strong class="font-medium text-gray-700">Doğum Tarihi:</strong> <?= isset($customer['birth_date']) && $customer['birth_date'] ? date('d.m.Y', strtotime($customer['birth_date'])) : '-' ?></p>
                <p class="text-gray-600"><strong class="font-medium text-gray-700">Kayıt Tarihi:</strong> <?= isset($customer['created_at']) ? date('d.m.Y H:i', strtotime($customer['created_at'])) : '-' ?></p>
            </div>
        </div>
        <?php if (!empty($customer['notes'])): ?>
        <div class="mt-4">
            <p class="text-gray-600"><strong class="font-medium text-gray-700">Notlar:</strong></p>
            <p class="text-gray-600 whitespace-pre-wrap"><?= esc($customer['notes']) ?></p>
        </div>
        <?php endif; ?>
        <?php if (!empty($customer['tags']) && is_array($customer['tags'])): ?>
        <div class="mt-4">
            <p class="text-gray-600"><strong class="font-medium text-gray-700">Etiketler:</strong></p>
            <div>
                <?php foreach ($customer['tags'] as $tag): ?>
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800 mb-1">
                        <?= esc(trim($tag)) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php elseif (!empty($customer['tags']) && !is_array($customer['tags'])): ?>
        <div class="mt-4">
            <p class="text-gray-600"><strong class="font-medium text-gray-700">Etiketler (Ham Veri):</strong></p>
            <p class="text-gray-500 text-xs"><?= esc($customer['tags']) ?></p>
            <p class="text-red-500 text-xs italic">Not: Etiketler PHP dizisi olarak gelmedi. Modeldeki $casts ayarını kontrol edin.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Müşteri İstatistikleri -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Müşteri İstatistikleri</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Randevu İstatistikleri -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">Toplam Randevu</p>
                        <p class="text-2xl font-bold text-blue-900"><?= $customerStats['appointments']['total_appointments'] ?></p>
                        <p class="text-xs text-blue-600">
                            Tamamlanan: <?= $customerStats['appointments']['completed_appointments'] ?> |
                            İptal: <?= $customerStats['appointments']['cancelled_appointments'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ödeme İstatistikleri -->
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">Toplam Ödeme</p>
                        <p class="text-2xl font-bold text-green-900">₺<?= number_format($customerStats['payments']['total_paid'], 0) ?></p>
                        <p class="text-xs text-green-600">
                            <?= $customerStats['payments']['total_payments'] ?> işlem
                            <?php if ($customerStats['payments']['total_refunded'] > 0): ?>
                                | İade: ₺<?= number_format($customerStats['payments']['total_refunded'], 0) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Paket İstatistikleri -->
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-box text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-purple-800">Paket Sayısı</p>
                        <p class="text-2xl font-bold text-purple-900"><?= $customerStats['packages']['total_packages'] ?></p>
                        <p class="text-xs text-purple-600">
                            Aktif: <?= $customerStats['packages']['active_packages'] ?> |
                            Tamamlanan: <?= $customerStats['packages']['completed_packages'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mesaj İstatistikleri -->
            <div class="bg-orange-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-sms text-2xl text-orange-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-orange-800">Gönderilen Mesaj</p>
                        <p class="text-2xl font-bold text-orange-900"><?= $customerStats['messages']['total_messages'] ?></p>
                        <p class="text-xs text-orange-600">
                            SMS: <?= $customerStats['messages']['sms_messages'] ?> |
                            WhatsApp: <?= $customerStats['messages']['whatsapp_messages'] ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Müşteri Geçmişi Özeti -->
        <?php if ($customerStats['appointments']['first_appointment'] || $customerStats['appointments']['last_appointment']): ?>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <?php if ($customerStats['appointments']['first_appointment']): ?>
                <div>
                    <span class="font-medium text-gray-700">İlk Randevu:</span>
                    <?= date('d.m.Y', strtotime($customerStats['appointments']['first_appointment'])) ?>
                </div>
                <?php endif; ?>
                <?php if ($customerStats['appointments']['last_appointment']): ?>
                <div>
                    <span class="font-medium text-gray-700">Son Randevu:</span>
                    <?= date('d.m.Y', strtotime($customerStats['appointments']['last_appointment'])) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Müşteri Geçmişi Sekmeleri -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <!-- Sekme Başlıkları -->
        <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <!-- Desktop Sekme Navigasyonu -->
            <nav class="hidden md:flex -mb-px px-6" aria-label="Tabs">
                <button onclick="showTab('randevular')" id="tab-randevular" class="tab-button group relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-center border-b-2 font-medium text-sm border-blue-500 text-blue-600 bg-blue-50 rounded-t-lg mx-1">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-calendar-alt text-lg"></i>
                        <span class="hidden lg:inline">Randevu Geçmişi</span>
                        <span class="lg:hidden">Randevular</span>
                    </div>
                    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-blue-500"></div>
                </button>
                <button onclick="showTab('paketler')" id="tab-paketler" class="tab-button group relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg mx-1 transition-all duration-200">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-box text-lg"></i>
                        <span class="hidden lg:inline">Paket Kullanımları</span>
                        <span class="lg:hidden">Paketler</span>
                    </div>
                </button>
                <button onclick="showTab('odemeler')" id="tab-odemeler" class="tab-button group relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg mx-1 transition-all duration-200">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-credit-card text-lg"></i>
                        <span class="hidden lg:inline">Ödeme Geçmişi</span>
                        <span class="lg:hidden">Ödemeler</span>
                    </div>
                </button>
                <button onclick="showTab('mesajlar')" id="tab-mesajlar" class="tab-button group relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg mx-1 transition-all duration-200">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-sms text-lg"></i>
                        <span class="hidden lg:inline">Gönderilen Mesajlar</span>
                        <span class="lg:hidden">Mesajlar</span>
                    </div>
                </button>
            </nav>
            
            <!-- Mobil Dropdown Sekme Navigasyonu -->
            <div class="md:hidden px-4 py-3">
                <select id="mobile-tab-selector" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="randevular" selected>📅 Randevu Geçmişi</option>
                    <option value="paketler">📦 Paket Kullanımları</option>
                    <option value="odemeler">💳 Ödeme Geçmişi</option>
                    <option value="mesajlar">💬 Gönderilen Mesajlar</option>
                </select>
            </div>
        </div>
        <!-- Sekme İçerikleri -->
        <div class="p-4 md:p-6">
            <!-- Randevu Geçmişi -->
            <div id="content-randevular" class="tab-content">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Randevu Geçmişi</h3>
                            <p class="text-sm text-gray-500">Müşterinin tüm randevu kayıtları</p>
                        </div>
                    </div>
                    <?php if (!empty($appointments)): ?>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium"><?= count($appointments) ?></span> randevu
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($appointments)): ?>
                    <!-- Filtreleme -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-filter text-blue-600 mr-2"></i>
                            <h4 class="text-sm font-medium text-blue-900">Filtreleme Seçenekleri</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Durum Filtresi</label>
                                <select id="appointment-status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="pending">Bekliyor</option>
                                    <option value="confirmed">Onaylandı</option>
                                    <option value="completed">Tamamlandı</option>
                                    <option value="cancelled">İptal</option>
                                    <option value="no_show">Gelmedi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Durumu</label>
                                <select id="payment-status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">Tüm Ödemeler</option>
                                    <option value="pending">Bekliyor</option>
                                    <option value="partial">Kısmi</option>
                                    <option value="paid">Ödendi</option>
                                    <option value="refunded">İade</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hizmet Arama</label>
                                <input type="text" id="service-search" placeholder="Hizmet adı ara..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih & Saat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hizmet</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödeme</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('d.m.Y', strtotime($appointment['appointment_date'])) ?><br>
                                        <span class="text-gray-500"><?= date('H:i', strtotime($appointment['start_time'])) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= esc($appointment['service_name']) ?><br>
                                        <span class="text-gray-500"><?= $appointment['duration'] ?> dk</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= esc($appointment['staff_first_name'] . ' ' . $appointment['staff_last_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'no_show' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Bekliyor',
                                            'confirmed' => 'Onaylandı',
                                            'completed' => 'Tamamlandı',
                                            'cancelled' => 'İptal',
                                            'no_show' => 'Gelmedi'
                                        ];
                                        $colorClass = $statusColors[$appointment['status']] ?? 'bg-gray-100 text-gray-800';
                                        $statusLabel = $statusLabels[$appointment['status']] ?? $appointment['status'];
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₺<?= number_format($appointment['price'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $paymentColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'partial' => 'bg-orange-100 text-orange-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'refunded' => 'bg-red-100 text-red-800'
                                        ];
                                        $paymentLabels = [
                                            'pending' => 'Bekliyor',
                                            'partial' => 'Kısmi',
                                            'paid' => 'Ödendi',
                                            'refunded' => 'İade'
                                        ];
                                        $paymentColorClass = $paymentColors[$appointment['payment_status']] ?? 'bg-gray-100 text-gray-800';
                                        $paymentLabel = $paymentLabels[$appointment['payment_status']] ?? $appointment['payment_status'];
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $paymentColorClass ?>">
                                            <?= $paymentLabel ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Henüz randevu geçmişi bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Paket Kullanımları -->
            <div id="content-paketler" class="tab-content" style="display: none;">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Paket Kullanımları</h3>
                            <p class="text-sm text-gray-500">Satın alınan paketler ve kullanım durumu</p>
                        </div>
                    </div>
                    <button onclick="openPackageSaleModal()" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="hidden sm:inline">Paket Sat</span>
                        <span class="sm:hidden">Sat</span>
                    </button>
                </div>

                <?php if (!empty($packages)): ?>
                    <div class="grid gap-4">
                        <?php foreach ($packages as $package): ?>
                        <div class="border rounded-lg p-4 <?= $package['status'] === 'active' ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' ?>">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900"><?= esc($package['name']) ?></h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Satın Alma: <?= date('d.m.Y', strtotime($package['purchase_date'])) ?> |
                                        Bitiş: <?= date('d.m.Y', strtotime($package['expiry_date'])) ?>
                                    </p>
                                    
                                    <?php if ($package['type'] === 'session'): ?>
                                        <div class="mt-2">
                                            <div class="flex justify-between text-sm">
                                                <span>Kullanılan: <?= $package['used_sessions'] ?> seans</span>
                                                <span>Kalan: <?= $package['remaining_sessions'] ?> seans</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                <?php
                                                $total = $package['used_sessions'] + $package['remaining_sessions'];
                                                $percentage = $total > 0 ? ($package['used_sessions'] / $total) * 100 : 0;
                                                ?>
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2">
                                            <div class="flex justify-between text-sm">
                                                <span>Kullanılan: <?= $package['used_minutes'] ?> dk</span>
                                                <span>Kalan: <?= $package['remaining_minutes'] ?> dk</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                <?php
                                                $total = $package['used_minutes'] + $package['remaining_minutes'];
                                                $percentage = $total > 0 ? ($package['used_minutes'] / $total) * 100 : 0;
                                                ?>
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <?php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'expired' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusLabels = [
                                        'active' => 'Aktif',
                                        'expired' => 'Süresi Dolmuş',
                                        'completed' => 'Tamamlandı',
                                        'cancelled' => 'İptal Edildi'
                                    ];
                                    $colorClass = $statusColors[$package['status']] ?? 'bg-gray-100 text-gray-800';
                                    $statusLabel = $statusLabels[$package['status']] ?? $package['status'];
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 mb-4">Henüz satın alınmış paket bulunmuyor.</p>
                        <button onclick="openPackageSaleModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i>İlk Paketi Sat
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Ödeme Geçmişi -->
            <div id="content-odemeler" class="tab-content" style="display: none;">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-credit-card text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Ödeme Geçmişi</h3>
                            <p class="text-sm text-gray-500">Tüm ödeme işlemleri ve detayları</p>
                        </div>
                    </div>
                    <?php if (!empty($payments)): ?>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium"><?= count($payments) ?></span> işlem
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($customerPayments)): ?>
                    <!-- Filtreleme -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-filter text-green-600 mr-2"></i>
                            <h4 class="text-sm font-medium text-green-900">Filtreleme Seçenekleri</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Türü</label>
                                <select id="payment-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">Tüm Türler</option>
                                    <option value="cash">Nakit</option>
                                    <option value="credit_card">Kredi Kartı</option>
                                    <option value="bank_transfer">Havale/EFT</option>
                                    <option value="gift_card">Hediye Çeki</option>
                                    <option value="package">Paket</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                                <select id="payment-status-filter-tab" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="pending">Bekliyor</option>
                                    <option value="completed">Tamamlandı</option>
                                    <option value="refunded">İade Edildi</option>
                                    <option value="cancelled">İptal Edildi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tutar Aralığı</label>
                                <input type="number" id="amount-filter" placeholder="Min. tutar"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hizmet/Randevu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödeme Türü</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemi Yapan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('d.m.Y', strtotime($payment['created_at'])) ?><br>
                                        <span class="text-gray-500"><?= date('H:i', strtotime($payment['created_at'])) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if (!empty($payment['service_name'])): ?>
                                            <?= esc($payment['service_name']) ?><br>
                                            <span class="text-gray-500"><?= date('d.m.Y H:i', strtotime($payment['appointment_date'])) ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-500">Genel Ödeme</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₺<?= number_format($payment['amount'], 2) ?>
                                        <?php if (!empty($payment['refund_amount']) && $payment['refund_amount'] > 0): ?>
                                            <br><span class="text-red-500 text-xs">İade: ₺<?= number_format($payment['refund_amount'], 2) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php
                                        $paymentTypes = [
                                            'cash' => 'Nakit',
                                            'credit_card' => 'Kredi Kartı',
                                            'bank_transfer' => 'Havale/EFT',
                                            'gift_card' => 'Hediye Çeki',
                                            'package' => 'Paket'
                                        ];
                                        ?>
                                        <?= $paymentTypes[$payment['payment_type']] ?? $payment['payment_type'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'refunded' => 'bg-red-100 text-red-800',
                                            'cancelled' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Bekliyor',
                                            'completed' => 'Tamamlandı',
                                            'refunded' => 'İade Edildi',
                                            'cancelled' => 'İptal Edildi'
                                        ];
                                        $colorClass = $statusColors[$payment['status']] ?? 'bg-gray-100 text-gray-800';
                                        $statusLabel = $statusLabels[$payment['status']] ?? $payment['status'];
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= esc($payment['processed_by_name'] . ' ' . $payment['processed_by_surname']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Ödeme Özeti -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php
                        $totalPaid = array_sum(array_column(array_filter($payments, function($p) { return $p['status'] === 'completed'; }), 'amount'));
                        $totalRefunded = array_sum(array_column(array_filter($payments, function($p) { return $p['status'] === 'refunded'; }), 'refund_amount'));
                        $netPaid = $totalPaid - $totalRefunded;
                        ?>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-800">Toplam Ödenen</div>
                            <div class="text-2xl font-bold text-green-900">₺<?= number_format($totalPaid, 2) ?></div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-red-800">Toplam İade</div>
                            <div class="text-2xl font-bold text-red-900">₺<?= number_format($totalRefunded, 2) ?></div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-800">Net Ödeme</div>
                            <div class="text-2xl font-bold text-blue-900">₺<?= number_format($netPaid, 2) ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-credit-card text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Henüz ödeme geçmişi bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Gönderilen Mesajlar -->
            <div id="content-mesajlar" class="tab-content" style="display: none;">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sms text-orange-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Gönderilen Mesajlar</h3>
                            <p class="text-sm text-gray-500">SMS ve WhatsApp mesaj geçmişi</p>
                        </div>
                    </div>
                    <?php if (!empty($customerMessages)): ?>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium"><?= count($customerMessages) ?></span> mesaj
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($customerMessages)): ?>
                    <div class="space-y-4">
                        <?php foreach ($customerMessages as $message): ?>
                        <div class="border rounded-lg p-4 <?= $message['status'] === 'sent' ? 'border-green-200 bg-green-50' : ($message['status'] === 'failed' ? 'border-red-200 bg-red-50' : 'border-yellow-200 bg-yellow-50') ?>">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $message['message_type'] === 'sms' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                            <i class="<?= $message['message_type'] === 'sms' ? 'fas fa-sms' : 'fab fa-whatsapp' ?> mr-1"></i>
                                            <?= strtoupper($message['message_type']) ?>
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            <?= date('d.m.Y H:i', strtotime($message['created_at'])) ?>
                                        </span>
                                        <?php if (!empty($message['sent_at'])): ?>
                                            <span class="text-xs text-gray-500">
                                                (Gönderildi: <?= date('H:i', strtotime($message['sent_at'])) ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="text-sm font-medium text-gray-700">Tetikleyici:</span>
                                        <span class="text-sm text-gray-600">
                                            <?php
                                            $triggerLabels = [
                                                'appointment_reminder_24h' => 'Randevu Hatırlatma (24 saat)',
                                                'appointment_reminder_2h' => 'Randevu Hatırlatma (2 saat)',
                                                'package_warning' => 'Paket Uyarısı',
                                                'no_show_notification' => 'No-Show Bildirimi',
                                                'birthday_greeting' => 'Doğum Günü Kutlaması',
                                                'manual' => 'Manuel Gönderim'
                                            ];
                                            echo $triggerLabels[$message['trigger_type']] ?? $message['trigger_type'];
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="text-sm font-medium text-gray-700">Alıcı:</span>
                                        <span class="text-sm text-gray-600"><?= esc($message['recipient_phone']) ?></span>
                                    </div>
                                    
                                    <div class="bg-white p-3 rounded border">
                                        <p class="text-sm text-gray-800 whitespace-pre-wrap"><?= esc($message['message_content']) ?></p>
                                    </div>
                                    
                                    <?php if (!empty($message['provider_response']) && $message['status'] === 'failed'): ?>
                                    <div class="mt-2">
                                        <span class="text-sm font-medium text-red-700">Hata Detayı:</span>
                                        <p class="text-xs text-red-600"><?= esc($message['provider_response']) ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'sent' => 'bg-green-100 text-green-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'delivered' => 'bg-blue-100 text-blue-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Bekliyor',
                                        'sent' => 'Gönderildi',
                                        'failed' => 'Başarısız',
                                        'delivered' => 'Teslim Edildi'
                                    ];
                                    $colorClass = $statusColors[$message['status']] ?? 'bg-gray-100 text-gray-800';
                                    $statusLabel = $statusLabels[$message['status']] ?? $message['status'];
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Mesaj İstatistikleri -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <?php
                        $totalMessages = count($customerMessages);
                        $sentMessages = count(array_filter($customerMessages, function($m) { return $m['status'] === 'sent'; }));
                        $failedMessages = count(array_filter($customerMessages, function($m) { return $m['status'] === 'failed'; }));
                        $smsMessages = count(array_filter($customerMessages, function($m) { return $m['message_type'] === 'sms'; }));
                        $whatsappMessages = count(array_filter($customerMessages, function($m) { return $m['message_type'] === 'whatsapp'; }));
                        ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-800">Toplam Mesaj</div>
                            <div class="text-2xl font-bold text-gray-900"><?= $totalMessages ?></div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-800">Başarılı</div>
                            <div class="text-2xl font-bold text-green-900"><?= $sentMessages ?></div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-800">SMS</div>
                            <div class="text-2xl font-bold text-blue-900"><?= $smsMessages ?></div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-800">WhatsApp</div>
                            <div class="text-2xl font-bold text-green-900"><?= $whatsappMessages ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-sms text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Henüz gönderilen mesaj bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Paket Satış Modal -->
<div id="packageSaleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Paket Satışı</h3>
            <form id="packageSaleForm" action="<?= site_url('/admin/packages/sell') ?>" method="POST">
                <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Paket Seçin</label>
                    <select name="package_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Paket seçiniz...</option>
                        <?php if (!empty($availablePackages)): ?>
                            <?php foreach ($availablePackages as $pkg): ?>
                                <option value="<?= $pkg['id'] ?>">
                                    <?= esc($pkg['name']) ?> - ₺<?= number_format($pkg['price'], 2) ?>
                                    (<?= $pkg['type'] === 'session' ? $pkg['total_sessions'] . ' seans' : $pkg['total_minutes'] . ' dakika' ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satış Tarihi</label>
                    <input type="date" name="purchase_date" value="<?= date('Y-m-d') ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notlar (Opsiyonel)</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Paket satışı ile ilgili notlar..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closePackageSaleModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        İptal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Paketi Sat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Tüm tab içeriklerini gizle
    document.querySelectorAll('.tab-content').forEach(function(content) {
        content.style.display = 'none';
    });
    
    // Tüm desktop tab butonlarının aktif stilini kaldır
    document.querySelectorAll('.tab-button').forEach(function(button) {
        button.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50');
        button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        // Alt çizgiyi kaldır
        const underline = button.querySelector('.absolute');
        if (underline) {
            underline.style.display = 'none';
        }
    });

    // Seçili tab içeriğini göster
    document.getElementById('content-' + tabName).style.display = 'block';
    
    // Desktop: Seçili tab butonuna aktif stilini ekle
    const activeButton = document.getElementById('tab-' + tabName);
    if (activeButton) {
        activeButton.classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50');
        activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        // Alt çizgiyi göster
        const underline = activeButton.querySelector('.absolute');
        if (underline) {
            underline.style.display = 'block';
        }
    }
    
    // Mobil: Dropdown değerini güncelle
    const mobileSelector = document.getElementById('mobile-tab-selector');
    if (mobileSelector) {
        mobileSelector.value = tabName;
    }
}

function openPackageSaleModal() {
    document.getElementById('packageSaleModal').classList.remove('hidden');
}

function closePackageSaleModal() {
    document.getElementById('packageSaleModal').classList.add('hidden');
}

// Modal dışına tıklandığında kapat
document.getElementById('packageSaleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePackageSaleModal();
    }
});

// Filtreleme fonksiyonları
document.addEventListener('DOMContentLoaded', function() {
    // Mobil dropdown için event listener
    const mobileSelector = document.getElementById('mobile-tab-selector');
    if (mobileSelector) {
        mobileSelector.addEventListener('change', function() {
            showTab(this.value);
        });
    }
    
    // Randevu geçmişi filtreleme
    const appointmentStatusFilter = document.getElementById('appointment-status-filter');
    const paymentStatusFilter = document.getElementById('payment-status-filter');
    const serviceSearch = document.getElementById('service-search');
    
    if (appointmentStatusFilter) {
        appointmentStatusFilter.addEventListener('change', filterAppointments);
    }
    if (paymentStatusFilter) {
        paymentStatusFilter.addEventListener('change', filterAppointments);
    }
    if (serviceSearch) {
        serviceSearch.addEventListener('input', filterAppointments);
    }
    
    // Ödeme geçmişi filtreleme
    const paymentTypeFilter = document.getElementById('payment-type-filter');
    const paymentStatusFilterTab = document.getElementById('payment-status-filter-tab');
    const amountFilter = document.getElementById('amount-filter');
    
    if (paymentTypeFilter) {
        paymentTypeFilter.addEventListener('change', filterPayments);
    }
    if (paymentStatusFilterTab) {
        paymentStatusFilterTab.addEventListener('change', filterPayments);
    }
    if (amountFilter) {
        amountFilter.addEventListener('input', filterPayments);
    }
});

function filterAppointments() {
    const statusFilter = document.getElementById('appointment-status-filter').value.toLowerCase();
    const paymentFilter = document.getElementById('payment-status-filter').value.toLowerCase();
    const serviceFilter = document.getElementById('service-search').value.toLowerCase();
    
    const rows = document.querySelectorAll('#content-randevular tbody tr');
    
    rows.forEach(function(row) {
        let showRow = true;
        
        // Durum filtresi
        if (statusFilter) {
            const statusCell = row.querySelector('td:nth-child(4) span');
            const statusText = statusCell ? statusCell.textContent.toLowerCase() : '';
            const statusMap = {
                'bekliyor': 'pending',
                'onaylandı': 'confirmed',
                'tamamlandı': 'completed',
                'iptal': 'cancelled',
                'gelmedi': 'no_show'
            };
            const actualStatus = Object.keys(statusMap).find(key => statusText.includes(key));
            if (!actualStatus || statusMap[actualStatus] !== statusFilter) {
                showRow = false;
            }
        }
        
        // Ödeme durumu filtresi
        if (paymentFilter && showRow) {
            const paymentCell = row.querySelector('td:nth-child(6) span');
            const paymentText = paymentCell ? paymentCell.textContent.toLowerCase() : '';
            const paymentMap = {
                'bekliyor': 'pending',
                'kısmi': 'partial',
                'ödendi': 'paid',
                'iade': 'refunded'
            };
            const actualPayment = Object.keys(paymentMap).find(key => paymentText.includes(key));
            if (!actualPayment || paymentMap[actualPayment] !== paymentFilter) {
                showRow = false;
            }
        }
        
        // Hizmet arama
        if (serviceFilter && showRow) {
            const serviceCell = row.querySelector('td:nth-child(2)');
            const serviceText = serviceCell ? serviceCell.textContent.toLowerCase() : '';
            if (!serviceText.includes(serviceFilter)) {
                showRow = false;
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

function filterPayments() {
    const typeFilter = document.getElementById('payment-type-filter').value.toLowerCase();
    const statusFilter = document.getElementById('payment-status-filter-tab').value.toLowerCase();
    const amountFilter = parseFloat(document.getElementById('amount-filter').value) || 0;
    
    const rows = document.querySelectorAll('#content-odemeler tbody tr');
    
    rows.forEach(function(row) {
        let showRow = true;
        
        // Ödeme türü filtresi
        if (typeFilter) {
            const typeCell = row.querySelector('td:nth-child(4)');
            const typeText = typeCell ? typeCell.textContent.toLowerCase() : '';
            const typeMap = {
                'nakit': 'cash',
                'kredi kartı': 'credit_card',
                'havale/eft': 'bank_transfer',
                'hediye çeki': 'gift_card',
                'paket': 'package'
            };
            const actualType = Object.keys(typeMap).find(key => typeText.includes(key));
            if (!actualType || typeMap[actualType] !== typeFilter) {
                showRow = false;
            }
        }
        
        // Durum filtresi
        if (statusFilter && showRow) {
            const statusCell = row.querySelector('td:nth-child(5) span');
            const statusText = statusCell ? statusCell.textContent.toLowerCase() : '';
            const statusMap = {
                'bekliyor': 'pending',
                'tamamlandı': 'completed',
                'iade edildi': 'refunded',
                'iptal edildi': 'cancelled'
            };
            const actualStatus = Object.keys(statusMap).find(key => statusText.includes(key));
            if (!actualStatus || statusMap[actualStatus] !== statusFilter) {
                showRow = false;
            }
        }
        
        // Tutar filtresi
        if (amountFilter > 0 && showRow) {
            const amountCell = row.querySelector('td:nth-child(3)');
            const amountText = amountCell ? amountCell.textContent.replace(/[₺,]/g, '') : '0';
            const amount = parseFloat(amountText) || 0;
            if (amount < amountFilter) {
                showRow = false;
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}
</script>
<?= $this->endSection() ?>