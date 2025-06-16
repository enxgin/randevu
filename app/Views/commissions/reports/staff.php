<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Prim Raporum</h1>
            <p class="text-gray-600">Kişisel prim raporunuzu görüntüleyin</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-print mr-2"></i>
                Yazdır
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <?php if ($summary): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-handshake text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Toplam Hizmet</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= number_format($summary['total_services']) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-lira-sign text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Toplam Ciro</p>
                        <p class="text-2xl font-semibold text-gray-900">₺<?= number_format($summary['total_service_amount'], 2) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-percentage text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Toplam Prim</p>
                        <p class="text-2xl font-semibold text-gray-900">₺<?= number_format($summary['total_commission_amount'], 2) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-orange-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Bekleyen Prim</p>
                        <p class="text-2xl font-semibold text-gray-900">₺<?= number_format($summary['pending_commission'], 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="<?= esc($filters['start_date']) ?>" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="<?= esc($filters['end_date']) ?>" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Tüm Durumlar</option>
                    <?php foreach ($statusLabels as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($filters['status'] == $key) ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-search mr-2"></i>Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Commission Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hizmet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hizmet Tutarı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prim Oranı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prim Tutarı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($commissions)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Seçilen tarih aralığında prim kaydı bulunmamaktadır.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($commissions as $commission): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= esc($commission['customer_first_name'] . ' ' . $commission['customer_last_name']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($commission['service_name']) ?></div>
                                    <?php if ($commission['is_package_service']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Paket Hizmeti
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Normal Hizmet
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d.m.Y', strtotime($commission['appointment_date'])) ?>
                                    <div class="text-xs text-gray-500">
                                        <?= date('H:i', strtotime($commission['start_time'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₺<?= number_format($commission['service_amount'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($commission['commission_type'] === 'percentage'): ?>
                                        %<?= number_format($commission['commission_rate'], 1) ?>
                                    <?php else: ?>
                                        Sabit Tutar
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        ₺<?= number_format($commission['commission_amount'], 2) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        switch ($commission['status']) {
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'paid':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            case 'refunded':
                                                echo 'bg-gray-100 text-gray-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= esc($statusLabels[$commission['status']] ?? $commission['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary by Service Type -->
    <?php if ($summary && ($summary['package_commission'] > 0 || $summary['regular_commission'] > 0)): ?>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Hizmet Türüne Göre Prim</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Normal Hizmetler:</span>
                        <span class="text-sm font-medium text-gray-900">₺<?= number_format($summary['regular_commission'], 2) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Paketli Hizmetler:</span>
                        <span class="text-sm font-medium text-gray-900">₺<?= number_format($summary['package_commission'], 2) ?></span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-900">Toplam:</span>
                            <span class="text-sm font-medium text-gray-900">₺<?= number_format($summary['total_commission_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ödeme Durumu</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Bekleyen:</span>
                        <span class="text-sm font-medium text-yellow-600">₺<?= number_format($summary['pending_commission'], 2) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Ödenen:</span>
                        <span class="text-sm font-medium text-green-600">₺<?= number_format($summary['paid_commission'], 2) ?></span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-900">Toplam:</span>
                            <span class="text-sm font-medium text-gray-900">₺<?= number_format($summary['total_commission_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Performance Info -->
    <?php if ($summary && $summary['total_services'] > 0): ?>
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4">Performans Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        ₺<?= number_format($summary['total_service_amount'] / $summary['total_services'], 2) ?>
                    </div>
                    <div class="text-sm text-blue-700">Ortalama Hizmet Tutarı</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        ₺<?= number_format($summary['total_commission_amount'] / $summary['total_services'], 2) ?>
                    </div>
                    <div class="text-sm text-blue-700">Ortalama Prim</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        %<?= number_format(($summary['total_commission_amount'] / $summary['total_service_amount']) * 100, 1) ?>
                    </div>
                    <div class="text-sm text-blue-700">Ortalama Prim Oranı</div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function printReport() {
    window.print();
}

// Print styles
const printStyles = `
    @media print {
        .no-print { display: none !important; }
        body { font-size: 12px; }
        .bg-blue-50 { background-color: #f0f9ff !important; }
        .text-blue-600 { color: #2563eb !important; }
        .text-blue-700 { color: #1d4ed8 !important; }
        .text-blue-900 { color: #1e3a8a !important; }
    }
`;

// Add print styles to head
const styleSheet = document.createElement("style");
styleSheet.type = "text/css";
styleSheet.innerText = printStyles;
document.head.appendChild(styleSheet);
</script>
<?= $this->endSection() ?>