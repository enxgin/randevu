<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık ve Filtreler -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-credit-card mr-3 text-blue-600"></i>
                        Ödeme Yönetimi
                    </h1>
                    <div class="flex space-x-3">
                        <a href="/payments/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i>
                            Ödeme Al
                        </a>
                        <a href="/payments/reports" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Raporlar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filtreler -->
            <div class="px-6 py-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" id="start_date" value="<?= $filters['start_date'] ?? '' ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Bitiş Tarihi</label>
                        <input type="date" name="end_date" id="end_date" value="<?= $filters['end_date'] ?? '' ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="payment_type" class="block text-sm font-medium text-gray-700">Ödeme Türü</label>
                        <select name="payment_type" id="payment_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Tümü</option>
                            <option value="cash" <?= ($filters['payment_type'] ?? '') === 'cash' ? 'selected' : '' ?>>Nakit</option>
                            <option value="credit_card" <?= ($filters['payment_type'] ?? '') === 'credit_card' ? 'selected' : '' ?>>Kredi Kartı</option>
                            <option value="bank_transfer" <?= ($filters['payment_type'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Havale/EFT</option>
                            <option value="gift_card" <?= ($filters['payment_type'] ?? '') === 'gift_card' ? 'selected' : '' ?>>Hediye Çeki</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Durum</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Tümü</option>
                            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Tamamlandı</option>
                            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Beklemede</option>
                            <option value="refunded" <?= ($filters['status'] ?? '') === 'refunded' ? 'selected' : '' ?>>İade Edildi</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-search mr-2"></i>
                            Filtrele
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <?php if (!empty($paymentStats)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <?php foreach ($paymentStats as $stat): ?>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <?php
                            $icon = 'fas fa-money-bill-wave';
                            $color = 'text-green-600';
                            switch ($stat['payment_type']) {
                                case 'cash':
                                    $icon = 'fas fa-money-bill-wave';
                                    $color = 'text-green-600';
                                    break;
                                case 'credit_card':
                                    $icon = 'fas fa-credit-card';
                                    $color = 'text-blue-600';
                                    break;
                                case 'bank_transfer':
                                    $icon = 'fas fa-university';
                                    $color = 'text-purple-600';
                                    break;
                                case 'gift_card':
                                    $icon = 'fas fa-gift';
                                    $color = 'text-pink-600';
                                    break;
                            }
                            ?>
                            <i class="<?= $icon ?> <?= $color ?> text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    <?php
                                    $typeNames = [
                                        'cash' => 'Nakit',
                                        'credit_card' => 'Kredi Kartı',
                                        'bank_transfer' => 'Havale/EFT',
                                        'gift_card' => 'Hediye Çeki'
                                    ];
                                    echo $typeNames[$stat['payment_type']] ?? $stat['payment_type'];
                                    ?>
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format($stat['total_amount'], 2) ?> ₺
                                </dd>
                                <dd class="text-sm text-gray-500">
                                    <?= $stat['count'] ?> işlem
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Ödeme Listesi -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Ödeme Listesi</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Toplam <?= count($payments) ?> ödeme kaydı
                </p>
            </div>
            
            <?php if (empty($payments)): ?>
            <div class="text-center py-12">
                <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Ödeme bulunamadı</h3>
                <p class="text-gray-500">Seçilen kriterlere uygun ödeme kaydı bulunmamaktadır.</p>
            </div>
            <?php else: ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($payments as $payment): ?>
                <li>
                    <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <?php
                                $statusColors = [
                                    'completed' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'refunded' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusColor = $statusColors[$payment['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <div class="h-10 w-10 rounded-full <?= $statusColor ?> flex items-center justify-center">
                                    <i class="fas fa-receipt text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= esc($payment['first_name'] . ' ' . $payment['last_name']) ?>
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <?php
                                        $typeIcons = [
                                            'cash' => 'fas fa-money-bill-wave text-green-600',
                                            'credit_card' => 'fas fa-credit-card text-blue-600',
                                            'bank_transfer' => 'fas fa-university text-purple-600',
                                            'gift_card' => 'fas fa-gift text-pink-600'
                                        ];
                                        $typeIcon = $typeIcons[$payment['payment_type']] ?? 'fas fa-money-bill-wave text-gray-600';
                                        ?>
                                        <i class="<?= $typeIcon ?>" title="<?= ucfirst($payment['payment_type']) ?>"></i>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= esc($payment['phone']) ?>
                                    <?php if ($payment['appointment_date']): ?>
                                    • Randevu: <?= date('d.m.Y H:i', strtotime($payment['appointment_date'])) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="text-right mr-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= number_format($payment['amount'], 2) ?> ₺
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= date('d.m.Y H:i', strtotime($payment['created_at'])) ?>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <?= esc($payment['processed_by_name'] . ' ' . $payment['processed_by_surname']) ?>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="/payments/show/<?= $payment['id'] ?>" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (in_array($userRole, ['admin', 'manager']) && $payment['status'] === 'completed'): ?>
                                <a href="/payments/refund/<?= $payment['id'] ?>" class="text-red-600 hover:text-red-900" title="İade">
                                    <i class="fas fa-undo"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>