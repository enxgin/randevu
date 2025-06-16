<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık ve Tarih Seçimi -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-cash-register mr-3 text-green-600"></i>
                        Kasa Yönetimi
                    </h1>
                    <div class="flex items-center space-x-3">
                        <form method="GET" class="flex items-center space-x-2">
                            <label for="date" class="text-sm font-medium text-gray-700">Tarih:</label>
                            <input type="date" name="date" id="date" value="<?= $selectedDate ?>" 
                                   class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   onchange="this.form.submit()">
                        </form>
                        <div class="flex space-x-2">
                            <?php if (!$isCashOpened): ?>
                            <a href="/cash/open" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                <i class="fas fa-unlock mr-2"></i>
                                Kasa Aç
                            </a>
                            <?php elseif (!$isCashClosed): ?>
                            <a href="/cash/close" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                <i class="fas fa-lock mr-2"></i>
                                Kasa Kapat
                            </a>
                            <?php endif; ?>
                            <a href="/cash/add-movement" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-plus mr-2"></i>
                                Manuel Hareket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kasa Durumu ve Özet Kartları -->
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
                                <dd class="text-sm text-gray-500">
                                    <?php if ($isCashOpened && !$isCashClosed): ?>
                                        <span class="text-green-600">Kasa Açık</span>
                                    <?php elseif ($isCashClosed): ?>
                                        <span class="text-red-600">Kasa Kapalı</span>
                                    <?php else: ?>
                                        <span class="text-yellow-600">Kasa Açılmamış</span>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Günlük Gelir -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-up text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Günlük Gelir</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format($dailySummary['total_income'], 2) ?> ₺
                                </dd>
                                <dd class="text-sm text-gray-500">
                                    <?= $dailySummary['movement_count'] ?> hareket
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Günlük Gider -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-down text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Günlük Gider</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format($dailySummary['total_expense'], 2) ?> ₺
                                </dd>
                                <dd class="text-sm text-gray-500">
                                    Gider hareketleri
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
                            <?php $netChange = $dailySummary['net_change']; ?>
                            <i class="fas fa-<?= $netChange >= 0 ? 'plus' : 'minus' ?> text-<?= $netChange >= 0 ? 'green' : 'red' ?>-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Net Değişim</dt>
                                <dd class="text-lg font-medium text-<?= $netChange >= 0 ? 'green' : 'red' ?>-900">
                                    <?= number_format($netChange, 2) ?> ₺
                                </dd>
                                <dd class="text-sm text-gray-500">
                                    Gelir - Gider
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Günlük Kasa Hareketleri -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Günlük Kasa Hareketleri
                                <span class="text-sm text-gray-500 ml-2">(<?= date('d.m.Y', strtotime($selectedDate)) ?>)</span>
                            </h3>
                            <a href="/cash/history" class="text-sm text-blue-600 hover:text-blue-900">
                                Tüm Geçmiş
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <?php if (empty($dailyMovements)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hareket bulunamadı</h3>
                        <p class="text-gray-500">Bu tarihte kasa hareketi bulunmamaktadır.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-hidden">
                        <ul class="divide-y divide-gray-200">
                            <?php foreach ($dailyMovements as $movement): ?>
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <?php
                                            $typeColors = [
                                                'opening' => 'bg-blue-100 text-blue-800',
                                                'closing' => 'bg-gray-100 text-gray-800',
                                                'income' => 'bg-green-100 text-green-800',
                                                'expense' => 'bg-red-100 text-red-800',
                                                'adjustment' => 'bg-yellow-100 text-yellow-800'
                                            ];
                                            $typeIcons = [
                                                'opening' => 'fas fa-unlock',
                                                'closing' => 'fas fa-lock',
                                                'income' => 'fas fa-arrow-up',
                                                'expense' => 'fas fa-arrow-down',
                                                'adjustment' => 'fas fa-edit'
                                            ];
                                            $typeColor = $typeColors[$movement['type']] ?? 'bg-gray-100 text-gray-800';
                                            $typeIcon = $typeIcons[$movement['type']] ?? 'fas fa-receipt';
                                            ?>
                                            <div class="h-10 w-10 rounded-full <?= $typeColor ?> flex items-center justify-center">
                                                <i class="<?= $typeIcon ?> text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($movement['description']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= esc($movement['first_name'] . ' ' . $movement['last_name']) ?>
                                                • <?= date('H:i', strtotime($movement['created_at'])) ?>
                                                <?php if ($movement['category']): ?>
                                                • <?= esc($movement['category']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium <?= in_array($movement['type'], ['income', 'opening']) ? 'text-green-900' : 'text-red-900' ?>">
                                            <?= in_array($movement['type'], ['income', 'opening']) ? '+' : '-' ?>
                                            <?= number_format($movement['amount'], 2) ?> ₺
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Bakiye: <?= number_format($movement['balance_after'], 2) ?> ₺
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Günlük Ödeme Özeti -->
            <div class="space-y-6">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Günlük Ödeme Özeti</h3>
                    </div>
                    <div class="p-6">
                        <?php if (empty($paymentSummary)): ?>
                        <p class="text-gray-500 text-sm">Bugün ödeme alınmamış.</p>
                        <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($paymentSummary as $payment): ?>
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
                                    $typeIcon = $typeIcons[$payment['payment_type']] ?? 'fas fa-money-bill-wave text-gray-600';
                                    $typeName = $typeNames[$payment['payment_type']] ?? $payment['payment_type'];
                                    ?>
                                    <i class="<?= $typeIcon ?> mr-3"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $typeName ?></div>
                                        <div class="text-xs text-gray-500"><?= $payment['count'] ?> işlem</div>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= number_format($payment['total_amount'], 2) ?> ₺
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hızlı İşlemler -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Hızlı İşlemler</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="/cash/add-movement" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-plus text-blue-600 mr-2"></i>
                            Manuel Hareket Ekle
                        </a>
                        <a href="/cash/history" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-history text-gray-600 mr-2"></i>
                            Hareket Geçmişi
                        </a>
                        <a href="/cash/reports" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                            Kasa Raporları
                        </a>
                        <a href="/payments" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                            Ödeme Yönetimi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>