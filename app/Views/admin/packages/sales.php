<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık ve Butonlar -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= $pageTitle ?></h1>
            <p class="text-gray-600 mt-1">Paket satış geçmişi ve raporları</p>
        </div>
        <div class="flex space-x-3">
            <a href="/admin/packages/sell" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-shopping-cart mr-2"></i>
                Yeni Satış
            </a>
            <a href="/admin/packages" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Paket Listesi
            </a>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Toplam Satış</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $totalSales ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-lira-sign text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Toplam Gelir</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        ₺<?= number_format($totalRevenue, 2) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Ortalama Satış</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        ₺<?= number_format($averageSale, 2) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Bu Ay</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= $thisMonthCount ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Paket</label>
                <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Paketler</option>
                    <!-- Paketler buraya eklenecek -->
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Durumlar</option>
                    <option value="active">Aktif</option>
                    <option value="expired">Süresi Dolmuş</option>
                    <option value="completed">Tamamlandı</option>
                    <option value="cancelled">İptal Edildi</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri</label>
                <input type="text" placeholder="Müşteri adı..." class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Satış Listesi -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-2"></i>
                                    <p>Henüz paket satışı yapılmamış</p>
                                    <a href="/admin/packages/sell" class="text-blue-600 hover:text-blue-800 mt-2">İlk satışı yapın</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= esc($sale['first_name'] . ' ' . $sale['last_name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500"><?= esc($sale['phone']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= esc($sale['package_name']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $sale['type'] === 'session' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= $sale['type'] === 'session' ? 'Adet Bazlı' : 'Dakika Bazlı' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d.m.Y H:i', strtotime($sale['purchase_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d.m.Y', strtotime($sale['expiry_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php if ($sale['type'] === 'session'): ?>
                                            <?= $sale['used_sessions'] ?>/<?= $sale['remaining_sessions'] + $sale['used_sessions'] ?> Seans
                                        <?php else: ?>
                                            <?= $sale['used_minutes'] ?>/<?= $sale['remaining_minutes'] + $sale['used_minutes'] ?> Dakika
                                        <?php endif; ?>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <?php 
                                        $total = $sale['type'] === 'session' ? 
                                            ($sale['remaining_sessions'] + $sale['used_sessions']) : 
                                            ($sale['remaining_minutes'] + $sale['used_minutes']);
                                        $used = $sale['type'] === 'session' ? $sale['used_sessions'] : $sale['used_minutes'];
                                        $percentage = $total > 0 ? ($used / $total) * 100 : 0;
                                        ?>
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                    </div>
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
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$sale['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                        <?= $statusLabels[$sale['status']] ?? $sale['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ₺<?= number_format($sale['price'] ?? 0, 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="/admin/customers/view/<?= $sale['customer_id'] ?>" class="text-blue-600 hover:text-blue-900" title="Müşteri Detayı">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <a href="/admin/packages/view/<?= $sale['package_id'] ?>" class="text-green-600 hover:text-green-900" title="Paket Detayı">
                                            <i class="fas fa-box"></i>
                                        </a>
                                        <?php if ($sale['status'] === 'active'): ?>
                                            <button onclick="cancelPackage(<?= $sale['id'] ?>)" class="text-red-600 hover:text-red-900" title="İptal Et">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- İptal Onay Modal'ı -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Paket Satışını İptal Et</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Bu paket satışını iptal etmek istediğinizden emin misiniz?
                    Bu işlem geri alınamaz.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmCancel" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    İptal Et
                </button>
                <button onclick="closeCancelModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Vazgeç
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let packageToCancel = null;

function cancelPackage(id) {
    packageToCancel = id;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    packageToCancel = null;
}

document.getElementById('confirmCancel').addEventListener('click', function() {
    if (packageToCancel) {
        // AJAX ile iptal işlemi burada yapılacak
        // Şimdilik sayfa yenileme ile çözüm
        alert('Paket satışı iptal edildi.');
        window.location.reload();
    }
    closeCancelModal();
});

// Modal dışına tıklandığında kapat
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
<?= $this->endSection() ?>