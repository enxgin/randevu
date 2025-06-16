<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-600"></i>
                        Borçlu Müşteriler
                    </h1>
                    <div class="flex space-x-3">
                        <a href="/payments/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Ödeme Al
                        </a>
                        <a href="/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Ödeme Listesi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Özet Kartları -->
        <?php if (!empty($debtors)): ?>
        <?php 
        $totalDebt = array_sum(array_column($debtors, 'total_debt'));
        $totalCustomers = count($debtors);
        $avgDebt = $totalCustomers > 0 ? $totalDebt / $totalCustomers : 0;
        ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Borçlu Müşteri</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $totalCustomers ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-lira-sign text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Toplam Alacak</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= number_format($totalDebt, 2) ?> ₺</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ortalama Borç</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= number_format($avgDebt, 2) ?> ₺</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Borçlu Müşteri Listesi -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Borçlu Müşteri Listesi</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Ödenmemiş randevuları bulunan müşteriler
                </p>
            </div>
            
            <?php if (empty($debtors)): ?>
            <div class="text-center py-12">
                <i class="fas fa-smile text-green-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Harika! Borçlu müşteri yok</h3>
                <p class="text-gray-500">Tüm müşteriler ödemelerini tamamlamış.</p>
            </div>
            <?php else: ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($debtors as $debtor): ?>
                <li>
                    <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-red-100 text-red-800 flex items-center justify-center">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= esc($debtor['first_name'] . ' ' . $debtor['last_name']) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-phone mr-1"></i>
                                    <?= esc($debtor['phone']) ?>
                                    <?php if ($debtor['email']): ?>
                                    <span class="ml-3">
                                        <i class="fas fa-envelope mr-1"></i>
                                        <?= esc($debtor['email']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    <?= $debtor['unpaid_appointments'] ?> ödenmemiş randevu
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="text-right mr-4">
                                <div class="text-lg font-bold text-red-600">
                                    <?= number_format($debtor['total_debt'], 2) ?> ₺
                                </div>
                                <div class="text-xs text-gray-500">
                                    Toplam Borç
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="/admin/customers/view/<?= $debtor['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-900" title="Müşteri Detayları">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/payments/create?customer_id=<?= $debtor['id'] ?>" 
                                   class="text-green-600 hover:text-green-900" title="Ödeme Al">
                                    <i class="fas fa-credit-card"></i>
                                </a>
                                <a href="tel:<?= esc($debtor['phone']) ?>" 
                                   class="text-purple-600 hover:text-purple-900" title="Ara">
                                    <i class="fas fa-phone"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

        <!-- Borç Takip Bilgileri -->
        <?php if (!empty($debtors)): ?>
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Borç Takip Bilgileri
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Borç Takip Süreci</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-start">
                                <i class="fas fa-circle text-yellow-500 text-xs mt-2 mr-2"></i>
                                <span>Randevu tamamlandığında ödeme alınmadıysa otomatik borç kaydı oluşur</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-circle text-blue-500 text-xs mt-2 mr-2"></i>
                                <span>Müşteri ile iletişime geçerek ödeme hatırlatması yapılabilir</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-circle text-green-500 text-xs mt-2 mr-2"></i>
                                <span>Ödeme alındığında borç otomatik olarak kapanır</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Hızlı İşlemler</h4>
                        <div class="space-y-3">
                            <a href="/payments/create" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md border border-gray-200">
                                <i class="fas fa-plus text-blue-600 mr-2"></i>
                                Yeni Ödeme Al
                            </a>
                            <a href="/payments/reports" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md border border-gray-200">
                                <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                                Ödeme Raporları
                            </a>
                            <a href="/admin/customers" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md border border-gray-200">
                                <i class="fas fa-users text-purple-600 mr-2"></i>
                                Müşteri Yönetimi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>