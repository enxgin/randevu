<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-piggy-bank mr-3 text-green-600"></i>
                    Kredi Bakiyesi Olan Müşteriler
                </h1>
                <div class="flex space-x-3">
                    <a href="/payments/debtors" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-exclamation-triangle mr-2 text-yellow-600"></i>
                        Borçlu Müşteriler
                    </a>
                    <a href="/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Geri Dön
                    </a>
                </div>
            </div>
        </div>

        <!-- Özet Kartları -->
        <?php if (!empty($creditCustomers)): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Toplam Müşteri</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= count($creditCustomers) ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Toplam Kredi</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format(array_sum(array_column($creditCustomers, 'credit_balance')), 2) ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ortalama Kredi</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?= number_format(array_sum(array_column($creditCustomers, 'credit_balance')) / count($creditCustomers), 2) ?> ₺
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Kredi Bakiyesi Tablosu -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Kredi Bakiyesi Listesi
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Fazla ödeme yapan müşterilerin kredi bakiyeleri
                </p>
            </div>

            <?php if (empty($creditCustomers)): ?>
            <div class="text-center py-12">
                <i class="fas fa-smile text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Kredi Bakiyesi Olan Müşteri Yok</h3>
                <p class="text-gray-500">Şu anda fazla ödeme yapan müşteri bulunmuyor.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Müşteri Bilgileri
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İletişim
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Randevu Tutarı
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ödenen Tutar
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kredi Bakiyesi
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($creditCustomers as $customer): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-user text-green-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= esc($customer['first_name'] . ' ' . $customer['last_name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: #<?= $customer['id'] ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-phone mr-1"></i>
                                    <?= esc($customer['phone']) ?>
                                </div>
                                <?php if ($customer['email']): ?>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-envelope mr-1"></i>
                                    <?= esc($customer['email']) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= number_format($customer['total_appointments'], 2) ?> ₺
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= number_format($customer['total_payments'], 2) ?> ₺
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    <?= number_format($customer['credit_balance'], 2) ?> ₺
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="/admin/customers/view/<?= $customer['id'] ?>" 
                                       class="text-blue-600 hover:text-blue-900" title="Müşteri Detayları">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/payments/create?customer_id=<?= $customer['id'] ?>" 
                                       class="text-green-600 hover:text-green-900" title="Ödeme Al">
                                        <i class="fas fa-cash-register"></i>
                                    </a>
                                    <a href="tel:<?= $customer['phone'] ?>" 
                                       class="text-purple-600 hover:text-purple-900" title="Ara">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Bilgi Notu -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Kredi Bakiyesi Hakkında</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Kredi bakiyesi, müşterinin randevu tutarından fazla ödeme yapması durumunda oluşur</li>
                            <li>Bu bakiye gelecek randevularda kullanılabilir veya müşteriye iade edilebilir</li>
                            <li>Kredi bakiyesi olan müşteriler yeni randevu aldığında otomatik olarak düşülür</li>
                            <li>İade işlemi için müşteri detay sayfasından işlem yapabilirsiniz</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>