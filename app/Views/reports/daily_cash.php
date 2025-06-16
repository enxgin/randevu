<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık ve Filtreler -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4 lg:mb-0">Günlük Kasa Raporu</h1>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
            <!-- Tarih Seçici -->
            <div class="flex items-center space-x-2">
                <label for="report-date" class="text-sm font-medium text-gray-700">Tarih:</label>
                <input type="date" id="report-date" value="<?= $date ?>" 
                       class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            
            <?php if ($userRole === 'admin'): ?>
            <!-- Şube Seçici -->
            <div class="flex items-center space-x-2">
                <label for="branch-select" class="text-sm font-medium text-gray-700">Şube:</label>
                <select id="branch-select" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>" <?= $branchId == $branch['id'] ? 'selected' : '' ?>>
                        <?= esc($branch['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <!-- Yazdır Butonu -->
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-print mr-2"></i>
                Yazdır
            </button>
        </div>
    </div>

    <!-- Özet Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Toplam Gelir -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-arrow-up text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Toplam Gelir</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                ₺<?= number_format($cashSummary['total_income'] ?? 0, 2) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toplam Gider -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-arrow-down text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Toplam Gider</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                ₺<?= number_format($cashSummary['total_expense'] ?? 0, 2) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Açılış Bakiyesi -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-play text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Açılış Bakiyesi</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                ₺<?= number_format($cashSummary['opening_balance'] ?? 0, 2) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kapanış Bakiyesi -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-stop text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Kapanış Bakiyesi</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                ₺<?= number_format($cashSummary['closing_balance'] ?? 0, 2) ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ödemeler -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Günlük Ödemeler</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    <?= date('d.m.Y', strtotime($date)) ?> tarihindeki tüm ödemeler
                </p>
            </div>
            <div class="border-t border-gray-200">
                <?php if (empty($payments)): ?>
                <div class="px-4 py-5 text-center text-gray-500">
                    Bu tarihte ödeme kaydı bulunmuyor.
                </div>
                <?php else: ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($payments as $payment): ?>
                    <li class="px-4 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-credit-card text-green-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= esc($payment['first_name'] . ' ' . $payment['last_name']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= esc($payment['service_name'] ?? 'Genel Ödeme') ?> - 
                                        <?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">
                                    ₺<?= number_format($payment['amount'], 2) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= date('H:i', strtotime($payment['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kasa Hareketleri -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Kasa Hareketleri</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Manuel gelir/gider kayıtları
                </p>
            </div>
            <div class="border-t border-gray-200">
                <?php if (empty($cashMovements)): ?>
                <div class="px-4 py-5 text-center text-gray-500">
                    Bu tarihte kasa hareketi bulunmuyor.
                </div>
                <?php else: ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($cashMovements as $movement): ?>
                    <li class="px-4 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 <?= $movement['type'] === 'income' ? 'bg-green-100' : 'bg-red-100' ?> rounded-full flex items-center justify-center">
                                        <i class="fas <?= $movement['type'] === 'income' ? 'fa-plus text-green-600' : 'fa-minus text-red-600' ?> text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= esc($movement['description']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= esc($movement['first_name'] . ' ' . $movement['last_name']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium <?= $movement['type'] === 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $movement['type'] === 'income' ? '+' : '-' ?>₺<?= number_format($movement['amount'], 2) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= date('H:i', strtotime($movement['created_at'])) ?>
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

    <!-- Borçlu Müşteriler -->
    <?php if (!empty($debtCustomers)): ?>
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Borçlu Müşteriler</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Ödenmemiş borcu olan müşteriler
            </p>
        </div>
        <div class="border-t border-gray-200">
            <ul class="divide-y divide-gray-200">
                <?php foreach (array_slice($debtCustomers, 0, 5) as $customer): ?>
                <li class="px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-red-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= esc($customer['first_name'] . ' ' . $customer['last_name']) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= esc($customer['phone']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-red-600">
                                ₺<?= number_format($customer['total_debt'], 2) ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?= $customer['unpaid_appointments'] ?> randevu
                            </div>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php if (count($debtCustomers) > 5): ?>
            <div class="px-4 py-3 bg-gray-50 text-center">
                <a href="<?= base_url('reports/debt-report') ?>" 
                   class="text-sm text-indigo-600 hover:text-indigo-500">
                    Tüm borçlu müşterileri görüntüle (<?= count($debtCustomers) ?> müşteri)
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Tarih değişikliği
document.getElementById('report-date').addEventListener('change', function() {
    updateReport();
});

// Şube değişikliği
document.getElementById('branch-select')?.addEventListener('change', function() {
    updateReport();
});

function updateReport() {
    const date = document.getElementById('report-date').value;
    const branchId = document.getElementById('branch-select')?.value;
    
    const params = new URLSearchParams();
    params.set('date', date);
    if (branchId) {
        params.set('branch_id', branchId);
    }
    
    window.location.href = '<?= base_url('reports/daily-cash') ?>?' + params.toString();
}
</script>
<?= $this->endSection() ?>