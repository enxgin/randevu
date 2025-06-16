<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık ve Filtreler -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-chart-bar mr-3 text-blue-600"></i>
                        Ödeme Raporları
                    </h1>
                    <div class="flex space-x-3">
                        <a href="/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Ödeme Listesi
                        </a>
                        <a href="/payments/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Ödeme Al
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tarih Filtresi -->
            <div class="px-6 py-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" id="start_date" value="<?= $startDate ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Bitiş Tarihi</label>
                        <input type="date" name="end_date" id="end_date" value="<?= $endDate ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-search mr-2"></i>
                            Filtrele
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Özet Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Günlük Toplam -->
            <?php 
            $dailyTotal = 0;
            foreach ($dailySummary as $summary) {
                if ($summary['status'] === 'completed') {
                    $dailyTotal += $summary['total_amount'];
                }
            }
            ?>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Bugünkü Toplam</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format($dailyTotal, 2) ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aylık Toplam -->
            <?php 
            $monthlyTotal = 0;
            foreach ($monthlyStats as $stat) {
                $monthlyTotal += $stat['total_amount'];
            }
            ?>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-alt text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Aylık Toplam</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format($monthlyTotal, 2) ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toplam İşlem -->
            <?php 
            $totalTransactions = 0;
            foreach ($paymentStats as $stat) {
                $totalTransactions += $stat['count'];
            }
            ?>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-receipt text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Toplam İşlem</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= $totalTransactions ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ortalama İşlem -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ortalama İşlem</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= $totalTransactions > 0 ? number_format($monthlyTotal / $totalTransactions, 2) : '0.00' ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Ödeme Türü İstatistikleri -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                        Ödeme Türü İstatistikleri
                    </h3>
                    <p class="text-sm text-gray-500">
                        <?= date('d.m.Y', strtotime($startDate)) ?> - <?= date('d.m.Y', strtotime($endDate)) ?>
                    </p>
                </div>
                <div class="p-6">
                    <?php if (empty($paymentStats)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Bu dönemde ödeme bulunmamaktadır.</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($paymentStats as $stat): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <?php
                                $typeIcons = [
                                    'cash' => 'fas fa-money-bill-wave text-green-600',
                                    'credit_card' => 'fas fa-credit-card text-blue-600',
                                    'bank_transfer' => 'fas fa-university text-purple-600',
                                    'gift_card' => 'fas fa-gift text-pink-600'
                                ];
                                $typeNames = [
                                    'cash' => 'Nakit',
                                    'credit_card' => 'Kredi Kartı',
                                    'bank_transfer' => 'Havale/EFT',
                                    'gift_card' => 'Hediye Çeki'
                                ];
                                $typeIcon = $typeIcons[$stat['payment_type']] ?? 'fas fa-money-bill-wave text-gray-600';
                                $typeName = $typeNames[$stat['payment_type']] ?? $stat['payment_type'];
                                ?>
                                <i class="<?= $typeIcon ?> mr-3"></i>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?= $typeName ?></div>
                                    <div class="text-xs text-gray-500"><?= $stat['count'] ?> işlem</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= number_format($stat['total_amount'], 2) ?> ₺
                                </div>
                                <div class="text-xs text-gray-500">
                                    %<?= $monthlyTotal > 0 ? number_format(($stat['total_amount'] / $monthlyTotal) * 100, 1) : '0' ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Günlük Ödeme Özeti -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-calendar-day mr-2 text-green-600"></i>
                        Günlük Ödeme Özeti
                    </h3>
                    <p class="text-sm text-gray-500">
                        <?= date('d.m.Y') ?> tarihli ödemeler
                    </p>
                </div>
                <div class="p-6">
                    <?php if (empty($dailySummary)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-day text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Bugün ödeme alınmamış.</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($dailySummary as $summary): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <?php
                                $typeIcons = [
                                    'cash' => 'fas fa-money-bill-wave text-green-600',
                                    'credit_card' => 'fas fa-credit-card text-blue-600',
                                    'bank_transfer' => 'fas fa-university text-purple-600',
                                    'gift_card' => 'fas fa-gift text-pink-600'
                                ];
                                $statusColors = [
                                    'completed' => 'text-green-600',
                                    'pending' => 'text-yellow-600',
                                    'refunded' => 'text-red-600'
                                ];
                                $typeIcon = $typeIcons[$summary['payment_type']] ?? 'fas fa-money-bill-wave text-gray-600';
                                $statusColor = $statusColors[$summary['status']] ?? 'text-gray-600';
                                ?>
                                <i class="<?= $typeIcon ?> mr-3"></i>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php
                                        $typeNames = [
                                            'cash' => 'Nakit',
                                            'credit_card' => 'Kredi Kartı',
                                            'bank_transfer' => 'Havale/EFT',
                                            'gift_card' => 'Hediye Çeki'
                                        ];
                                        echo $typeNames[$summary['payment_type']] ?? $summary['payment_type'];
                                        ?>
                                    </div>
                                    <div class="text-xs <?= $statusColor ?>">
                                        <?= $summary['count'] ?> işlem - 
                                        <?php
                                        $statusNames = [
                                            'completed' => 'Tamamlandı',
                                            'pending' => 'Beklemede',
                                            'refunded' => 'İade Edildi'
                                        ];
                                        echo $statusNames[$summary['status']] ?? $summary['status'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm font-medium <?= $statusColor ?>">
                                <?= number_format($summary['total_amount'], 2) ?> ₺
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Aylık Trend Grafiği -->
        <?php if (!empty($monthlyStats)): ?>
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                    Aylık Ödeme Trendi
                </h3>
                <p class="text-sm text-gray-500">
                    <?= date('F Y') ?> ayı günlük ödeme tutarları
                </p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <?php foreach ($monthlyStats as $stat): ?>
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <?= $stat['day'] ?> <?= date('F') ?>
                        </div>
                        <div class="flex items-center">
                            <div class="text-sm text-gray-500 mr-3">
                                <?= $stat['count'] ?> işlem
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                <?= number_format($stat['total_amount'], 2) ?> ₺
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Hızlı İşlemler -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                    Hızlı İşlemler
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="/payments/create" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-plus text-blue-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Ödeme Al</div>
                                <div class="text-xs text-gray-500">Yeni ödeme</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="/payments/debtors" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Borçlu Müşteriler</div>
                                <div class="text-xs text-gray-500">Alacak takibi</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="/cash/reports" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-cash-register text-green-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Kasa Raporları</div>
                                <div class="text-xs text-gray-500">Kasa analizi</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="/payments" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-list text-purple-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Ödeme Listesi</div>
                                <div class="text-xs text-gray-500">Tüm ödemeler</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>