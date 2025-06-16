<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= $pageTitle ?></h1>
            <p class="text-gray-600 mt-1">Paket detayları ve istatistikleri</p>
        </div>
        <div class="flex space-x-3">
            <a href="/admin/packages/edit/<?= $package['id'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Düzenle
            </a>
            <a href="/admin/packages" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Kolon - Paket Bilgileri -->
        <div class="space-y-6">
            <!-- Temel Bilgiler -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Paket Bilgileri</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Paket Adı</h3>
                        <p class="text-lg text-gray-900"><?= esc($package['name']) ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Şube</h3>
                        <p class="text-lg text-gray-900"><?= esc($package['branch_name'] ?? 'Bilinmiyor') ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Paket Türü</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $package['type'] === 'session' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                            <?= $package['type'] === 'session' ? 'Adet Bazlı' : 'Dakika Bazlı' ?>
                        </span>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Miktar</h3>
                        <p class="text-lg text-gray-900">
                            <?php if ($package['type'] === 'session'): ?>
                                <?= number_format($package['total_sessions']) ?> Seans
                            <?php else: ?>
                                <?= number_format($package['total_minutes']) ?> Dakika
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Fiyat</h3>
                        <p class="text-2xl font-bold text-green-600">₺<?= number_format($package['price'], 2) ?></p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Geçerlilik Süresi</h3>
                        <p class="text-lg text-gray-900"><?= $package['validity_months'] ?> Ay</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Durum</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $package['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $package['is_active'] ? 'Aktif' : 'Pasif' ?>
                        </span>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Oluşturma Tarihi</h3>
                        <p class="text-lg text-gray-900"><?= date('d.m.Y H:i', strtotime($package['created_at'])) ?></p>
                    </div>
                </div>
                
                <?php if (!empty($package['description'])): ?>
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Açıklama</h3>
                        <p class="text-gray-900"><?= esc($package['description']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Paket Özeti Kartı -->
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg shadow-sm text-white p-6">
                <h3 class="text-lg font-semibold mb-2">Paket Özeti</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Tür:</span>
                        <span><?= $package['type'] === 'session' ? 'Adet Bazlı' : 'Dakika Bazlı' ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Miktar:</span>
                        <span>
                            <?php if ($package['type'] === 'session'): ?>
                                <?= number_format($package['total_sessions']) ?> Seans
                            <?php else: ?>
                                <?= number_format($package['total_minutes']) ?> Dakika
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fiyat:</span>
                        <span>₺<?= number_format($package['price'], 2) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Geçerlilik:</span>
                        <span><?= $package['validity_months'] ?> Ay</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Hizmet Sayısı:</span>
                        <span><?= count($package['services'] ?? []) ?> Adet</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orta Kolon - Kapsanan Hizmetler -->
        <div class="space-y-6">
            <!-- Kapsanan Hizmetler -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Kapsanan Hizmetler</h2>
                
                <?php if (empty($package['services'])): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-spa text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">Bu pakete henüz hizmet atanmamış</p>
                        <a href="/admin/packages/edit/<?= $package['id'] ?>" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Hizmet ekleyin</a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($package['services'] as $service): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900"><?= esc($service['service_name']) ?></h4>
                                    <p class="text-sm text-gray-500"><?= $service['duration'] ?> dakika</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900">₺<?= number_format($service['price'], 2) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sağ Kolon - İstatistikler ve İşlemler -->
        <div class="space-y-6">
            <!-- Satış İstatistikleri -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Satış İstatistikleri</h2>
                
                <div class="space-y-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600"><?= number_format($salesStats['total_sales'] ?? 0) ?></div>
                        <div class="text-sm text-blue-600">Toplam Satış</div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600"><?= number_format($salesStats['active_sales'] ?? 0) ?></div>
                            <div class="text-xs text-green-600">Aktif</div>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <div class="text-lg font-bold text-yellow-600"><?= number_format($salesStats['expired_sales'] ?? 0) ?></div>
                            <div class="text-xs text-yellow-600">Süresi Dolmuş</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-lg font-bold text-gray-600"><?= number_format($salesStats['completed_sales'] ?? 0) ?></div>
                            <div class="text-xs text-gray-600">Tamamlanmış</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Hızlı İşlemler</h2>
                
                <div class="space-y-3">
                    <a href="/admin/packages/sell?package_id=<?= $package['id'] ?>" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Bu Paketi Sat
                    </a>
                    
                    <a href="/admin/packages/edit/<?= $package['id'] ?>" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Paketi Düzenle
                    </a>
                    
                    <a href="/admin/packages/sales?package_id=<?= $package['id'] ?>" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line mr-2"></i>
                        Satış Detayları
                    </a>
                    
                    <?php if ($package['is_active']): ?>
                        <button onclick="togglePackageStatus(<?= $package['id'] ?>, false)" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pause mr-2"></i>
                            Pasif Yap
                        </button>
                    <?php else: ?>
                        <button onclick="togglePackageStatus(<?= $package['id'] ?>, true)" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                            <i class="fas fa-play mr-2"></i>
                            Aktif Yap
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePackageStatus(packageId, newStatus) {
    const action = newStatus ? 'aktif' : 'pasif';
    
    if (confirm(`Bu paketi ${action} yapmak istediğinizden emin misiniz?`)) {
        // AJAX ile durum güncelleme işlemi burada yapılacak
        // Şimdilik sayfa yenileme ile çözüm
        window.location.reload();
    }
}
</script>
<?= $this->endSection() ?>