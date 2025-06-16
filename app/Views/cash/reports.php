<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık ve Filtreler -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-chart-bar mr-3 text-green-600"></i>
                        Kasa Raporları
                    </h1>
                    <div class="flex space-x-3">
                        <a href="/cash" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kasa Yönetimi
                        </a>
                        <a href="/cash/history" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-history mr-2"></i>
                            Hareket Geçmişi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tarih Filtresi -->
            <div class="px-6 py-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" id="start_date" value="<?= $startDate ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Bitiş Tarihi</label>
                        <input type="date" name="end_date" id="end_date" value="<?= $endDate ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-search mr-2"></i>
                            Filtrele
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Özet Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Güncel Bakiye -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-wallet text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Güncel Bakiye</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format($currentBalance, 2) ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bugünkü Gelir -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-up text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Bugünkü Gelir</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format($todaySummary['total_income'], 2) ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bugünkü Gider -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-down text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Bugünkü Gider</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format($todaySummary['total_expense'], 2) ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Değişim -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <?php $netChange = $todaySummary['net_change']; ?>
                            <i class="fas fa-<?= $netChange >= 0 ? 'plus' : 'minus' ?> text-<?= $netChange >= 0 ? 'green' : 'red' ?>-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Net Değişim</dt>
                                <dd class="text-lg font-medium text-<?= $netChange >= 0 ? 'green' : 'red' ?>-900">
                                    <?= number_format($netChange, 2) ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kategori Bazlı Harcamalar -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-chart-pie mr-2 text-red-600"></i>
                        Kategori Bazlı Harcamalar
                    </h3>
                    <p class="text-sm text-gray-500">
                        <?= date('d.m.Y', strtotime($startDate)) ?> - <?= date('d.m.Y', strtotime($endDate)) ?>
                    </p>
                </div>
                <div class="p-6">
                    <?php if (empty($expensesByCategory)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Bu dönemde harcama bulunmamaktadır.</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($expensesByCategory as $expense): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-red-500 mr-3"></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php
                                        $categoryNames = [
                                            'rent' => 'Kira',
                                            'utilities' => 'Faturalar',
                                            'supplies' => 'Malzeme',
                                            'staff_advance' => 'Personel Avansı',
                                            'maintenance' => 'Bakım-Onarım',
                                            'marketing' => 'Pazarlama',
                                            'other_expense' => 'Diğer Gider',
                                            'refund' => 'İade'
                                        ];
                                        echo $categoryNames[$expense['category']] ?? ucfirst($expense['category']);
                                        ?>
                                    </div>
                                    <div class="text-xs text-gray-500"><?= $expense['count'] ?> işlem</div>
                                </div>
                            </div>
                            <div class="text-sm font-medium text-red-600">
                                <?= number_format($expense['total_amount'], 2) ?> ₺
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Aylık Trend -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                        Aylık Trend
                    </h3>
                    <p class="text-sm text-gray-500">
                        <?= date('F Y') ?> ayı günlük hareketler
                    </p>
                </div>
                <div class="p-6">
                    <?php if (empty($monthlySummary)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-chart-line text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Bu ay henüz hareket bulunmamaktadır.</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($monthlySummary as $day): ?>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <?= $day['day'] ?> <?= date('F') ?>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-sm text-green-600">
                                    +<?= number_format($day['daily_income'], 2) ?> ₺
                                </div>
                                <div class="text-sm text-red-600">
                                    -<?= number_format($day['daily_expense'], 2) ?> ₺
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= number_format($day['daily_income'] - $day['daily_expense'], 2) ?> ₺
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

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
                    <a href="/cash/add-movement" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-plus text-blue-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Manuel Hareket</div>
                                <div class="text-xs text-gray-500">Gelir/Gider ekle</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="/cash/history" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-history text-gray-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Hareket Geçmişi</div>
                                <div class="text-xs text-gray-500">Detaylı geçmiş</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="/payments/reports" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-credit-card text-green-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Ödeme Raporları</div>
                                <div class="text-xs text-gray-500">Ödeme analizi</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="/cash" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-cash-register text-purple-600 text-xl mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Kasa Yönetimi</div>
                                <div class="text-xs text-gray-500">Ana sayfa</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>