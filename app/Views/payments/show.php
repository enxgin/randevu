<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-receipt mr-3 text-blue-600"></i>
                    Ödeme Detayları
                </h1>
                <div class="flex space-x-3">
                    <?php if (in_array($userRole, ['admin', 'manager'])): ?>
                    <a href="/payments/refund/<?= $payment['id'] ?>" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                        <i class="fas fa-undo mr-2"></i>
                        İade İşlemi
                    </a>
                    <?php endif; ?>
                    <a href="/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Geri Dön
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ödeme Bilgileri -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Temel Bilgiler -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ödeme Bilgileri</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ödeme ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">#<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                                <dd class="mt-1">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'refunded' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusNames = [
                                        'pending' => 'Beklemede',
                                        'completed' => 'Tamamlandı',
                                        'refunded' => 'İade Edildi',
                                        'cancelled' => 'İptal Edildi'
                                    ];
                                    $statusColor = $statusColors[$payment['status']] ?? 'bg-gray-100 text-gray-800';
                                    $statusName = $statusNames[$payment['status']] ?? $payment['status'];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColor ?>">
                                        <?= $statusName ?>
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tutar</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900"><?= number_format($payment['amount'], 2) ?> ₺</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ödeme Türü</dt>
                                <dd class="mt-1">
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
                                    <div class="flex items-center">
                                        <i class="<?= $typeIcon ?> mr-2"></i>
                                        <span class="text-sm text-gray-900"><?= $typeName ?></span>
                                    </div>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">İşlem Tarihi</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?= date('d.m.Y H:i:s', strtotime($payment['created_at'])) ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">İşlemi Yapan</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?= esc($payment['processed_by_name'] . ' ' . $payment['processed_by_surname']) ?></dd>
                            </div>
                            <?php if ($payment['transaction_id']): ?>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">İşlem Referans No</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono"><?= esc($payment['transaction_id']) ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($payment['notes']): ?>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Notlar</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?= esc($payment['notes']) ?></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>

                <!-- Ödeme Yöntemi Detayları -->
                <?php if ($payment['payment_method_details']): ?>
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ödeme Yöntemi Detayları</h3>
                    </div>
                    <div class="p-6">
                        <?php
                        $methodDetails = json_decode($payment['payment_method_details'], true);
                        if ($methodDetails):
                        ?>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                            <?php foreach ($methodDetails as $key => $value): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    <?php
                                    $labels = [
                                        'card_last_four' => 'Kartın Son 4 Hanesi',
                                        'card_type' => 'Kart Türü',
                                        'transaction_id' => 'İşlem Referans No',
                                        'bank_name' => 'Banka Adı',
                                        'reference_number' => 'Referans Numarası'
                                    ];
                                    echo $labels[$key] ?? $key;
                                    ?>
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900"><?= esc($value) ?></dd>
                            </div>
                            <?php endforeach; ?>
                        </dl>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- İade Bilgileri -->
                <?php if ($payment['status'] === 'refunded' && $payment['refund_amount']): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg">
                    <div class="px-6 py-4 border-b border-red-200">
                        <h3 class="text-lg font-medium text-red-900">İade Bilgileri</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                            <div>
                                <dt class="text-sm font-medium text-red-700">İade Tutarı</dt>
                                <dd class="mt-1 text-lg font-semibold text-red-900"><?= number_format($payment['refund_amount'], 2) ?> ₺</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-red-700">İade Tarihi</dt>
                                <dd class="mt-1 text-sm text-red-900"><?= date('d.m.Y H:i:s', strtotime($payment['refunded_at'])) ?></dd>
                            </div>
                            <?php if ($payment['refund_reason']): ?>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-red-700">İade Sebebi</dt>
                                <dd class="mt-1 text-sm text-red-900"><?= esc($payment['refund_reason']) ?></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Parçalı Ödemeler -->
                <?php if (!empty($partialPayments) && count($partialPayments) > 1): ?>
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Parçalı Ödemeler</h3>
                        <p class="text-sm text-gray-500">Bu randevu için yapılan tüm ödemeler</p>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($partialPayments as $partialPayment): ?>
                                <tr class="<?= $partialPayment['id'] == $payment['id'] ? 'bg-blue-50' : '' ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('d.m.Y H:i', strtotime($partialPayment['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= number_format($partialPayment['amount'], 2) ?> ₺
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $typeNames[$partialPayment['payment_type']] ?? $partialPayment['payment_type'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$partialPayment['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <?= $statusNames[$partialPayment['status']] ?? $partialPayment['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Yan Panel -->
            <div class="space-y-6">
                <!-- Müşteri Bilgileri -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Müşteri Bilgileri</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= esc($payment['first_name'] . ' ' . $payment['last_name']) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= esc($payment['phone']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="/admin/customers/view/<?= $payment['customer_id'] ?>" class="text-sm text-blue-600 hover:text-blue-900">
                                Müşteri detaylarını görüntüle
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Randevu Bilgileri -->
                <?php if ($payment['appointment_id']): ?>
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Randevu Bilgileri</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <?php if ($payment['service_name']): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Hizmet</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?= esc($payment['service_name']) ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($payment['appointment_date']): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Randevu Tarihi</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?= date('d.m.Y H:i', strtotime($payment['appointment_date'])) ?></dd>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4">
                            <a href="/calendar" class="text-sm text-blue-600 hover:text-blue-900">
                                Randevu takvimini görüntüle
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Hızlı İşlemler -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Hızlı İşlemler</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="/payments/create?customer_id=<?= $payment['customer_id'] ?>" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-plus text-green-600 mr-2"></i>
                            Yeni Ödeme Al
                        </a>
                        <a href="/payments?customer_search=<?= urlencode($payment['phone']) ?>" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-search text-blue-600 mr-2"></i>
                            Müşteri Ödemeleri
                        </a>
                        <?php if (in_array($userRole, ['admin', 'manager'])): ?>
                        <a href="/payments/refund/<?= $payment['id'] ?>" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-undo text-red-600 mr-2"></i>
                            İade İşlemi
                        </a>
                        <?php endif; ?>
                        <a href="/cash" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-cash-register text-purple-600 mr-2"></i>
                            Kasa Yönetimi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>