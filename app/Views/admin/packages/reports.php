<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 sm:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700"><?= esc($pageTitle ?? 'Paket Takibi ve Raporlama') ?></h1>
        <div class="flex space-x-2">
            <button onclick="expireOldPackages()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded text-sm">
                <i class="fas fa-clock mr-2"></i>Süresi Dolmuş Paketleri Güncelle
            </button>
            <button onclick="refreshAlerts()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                <i class="fas fa-sync-alt mr-2"></i>Uyarıları Yenile
            </button>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-box text-2xl text-green-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Aktif Paketler</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= number_format($stats['total_active']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-2xl text-yellow-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Süresi Yaklaşan</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= number_format($stats['near_expiry']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-battery-quarter text-2xl text-orange-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Bitmek Üzere</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= number_format($stats['near_completion']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-2xl text-red-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Bugün Süresi Dolan</dt>
                            <dd class="text-lg font-medium text-gray-900"><?= number_format($stats['expired_today']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Uyarılar -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Süresi Yaklaşan Paketler -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    Süresi Yaklaşan Paketler (7 Gün)
                </h3>
                <?php if (!empty($packagesNearExpiry)): ?>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <?php foreach ($packagesNearExpiry as $package): ?>
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">
                                    <?= esc($package['first_name'] . ' ' . $package['last_name']) ?>
                                </p>
                                <p class="text-sm text-gray-600"><?= esc($package['package_name']) ?></p>
                                <p class="text-xs text-gray-500">
                                    Bitiş: <?= date('d.m.Y', strtotime($package['expiry_date'])) ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <a href="tel:<?= esc($package['phone']) ?>" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-phone"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-4">Süresi yaklaşan paket bulunmuyor.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bitmek Üzere Olan Paketler -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-battery-quarter text-orange-500 mr-2"></i>
                    Bitmek Üzere Olan Paketler
                </h3>
                <?php if (!empty($packagesNearCompletion)): ?>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <?php foreach ($packagesNearCompletion as $package): ?>
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">
                                    <?= esc($package['first_name'] . ' ' . $package['last_name']) ?>
                                </p>
                                <p class="text-sm text-gray-600"><?= esc($package['package_name']) ?></p>
                                <p class="text-xs text-gray-500">
                                    Kalan: 
                                    <?php if ($package['remaining_sessions']): ?>
                                        <?= $package['remaining_sessions'] ?> seans
                                    <?php else: ?>
                                        <?= $package['remaining_minutes'] ?> dakika
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <a href="tel:<?= esc($package['phone']) ?>" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-phone"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-4">Bitmek üzere olan paket bulunmuyor.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filtreleme -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Paket Kullanım Raporu</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <?php if ($userRole === 'admin'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
                    <select name="branch_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tüm Şubeler</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>" <?= $filters['branch_id'] == $branch['id'] ? 'selected' : '' ?>>
                                <?= esc($branch['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" value="<?= esc($filters['start_date']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                    <input type="date" name="end_date" value="<?= esc($filters['end_date']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-filter mr-2"></i>Filtrele
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Paket Kullanım Raporu -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <?php if (!empty($packageUsageReport)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satış Tarihi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bitiş Tarihi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanım</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($packageUsageReport as $report): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= esc($report['first_name'] . ' ' . $report['last_name']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500"><?= esc($report['phone']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= esc($report['package_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $report['type'] === 'session' ? 'Seans' : 'Dakika' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d.m.Y', strtotime($report['purchase_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d.m.Y', strtotime($report['expiry_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($report['type'] === 'session'): ?>
                                        <?= $report['used_sessions'] ?>/<?= $report['used_sessions'] + $report['remaining_sessions'] ?> seans
                                    <?php else: ?>
                                        <?= $report['used_minutes'] ?>/<?= $report['used_minutes'] + $report['remaining_minutes'] ?> dk
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    $colorClass = $statusColors[$report['status']] ?? 'bg-gray-100 text-gray-800';
                                    $statusLabel = $statusLabels[$report['status']] ?? $report['status'];
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₺<?= number_format($report['price'], 2) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-chart-bar text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Seçilen kriterlere uygun paket bulunamadı.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function expireOldPackages() {
    if (!confirm('Süresi dolmuş paketleri güncellemek istediğinizden emin misiniz?')) {
        return;
    }

    fetch('<?= site_url('/admin/packages/expire-old') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu.');
    });
}

function refreshAlerts() {
    fetch('<?= site_url('/admin/packages/alerts') ?>')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Toplam ${data.total_alerts} uyarı bulundu.\n- Süresi yaklaşan: ${data.near_expiry.length}\n- Bitmek üzere: ${data.near_completion.length}`);
        } else {
            alert('Uyarılar yüklenirken hata oluştu.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu.');
    });
}
</script>
<?= $this->endSection() ?>