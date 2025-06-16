<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık ve Filtreler -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-history mr-3 text-blue-600"></i>
                        Kasa Hareketleri Geçmişi
                    </h1>
                    <a href="/cash" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kasa Yönetimine Dön
                    </a>
                </div>
            </div>
            
            <!-- Filtreler -->
            <div class="px-6 py-4 bg-gray-50">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" id="start_date" value="<?= $startDate ?>" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                        <input type="date" name="end_date" id="end_date" value="<?= $endDate ?>" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Hareket Türü</label>
                        <select name="type" id="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Tümü</option>
                            <option value="opening" <?= $filters['type'] === 'opening' ? 'selected' : '' ?>>Açılış</option>
                            <option value="closing" <?= $filters['type'] === 'closing' ? 'selected' : '' ?>>Kapanış</option>
                            <option value="income" <?= $filters['type'] === 'income' ? 'selected' : '' ?>>Gelir</option>
                            <option value="expense" <?= $filters['type'] === 'expense' ? 'selected' : '' ?>>Gider</option>
                            <option value="adjustment" <?= $filters['type'] === 'adjustment' ? 'selected' : '' ?>>Düzeltme</option>
                        </select>
                    </div>
                    <div>
                        <label for="description_search" class="block text-sm font-medium text-gray-700 mb-1">Açıklama Ara</label>
                        <input type="text" name="description_search" id="description_search" value="<?= esc($filters['description_search']) ?>" 
                               placeholder="Açıklama ara..." 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Filtrele
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hareketler Listesi -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    Kasa Hareketleri
                    <span class="text-sm text-gray-500 ml-2">
                        (<?= date('d.m.Y', strtotime($startDate)) ?> - <?= date('d.m.Y', strtotime($endDate)) ?>)
                    </span>
                </h3>
            </div>
            
            <?php if (empty($movements)): ?>
            <div class="text-center py-12">
                <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Hareket bulunamadı</h3>
                <p class="text-gray-500">Seçilen kriterlere uygun kasa hareketi bulunmamaktadır.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tarih/Saat
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tür
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Açıklama
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tutar
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bakiye
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İşlemi Yapan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($movements as $movement): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium"><?= date('d.m.Y', strtotime($movement['created_at'])) ?></div>
                                <div class="text-gray-500"><?= date('H:i:s', strtotime($movement['created_at'])) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $typeColors = [
                                    'opening' => 'bg-blue-100 text-blue-800',
                                    'closing' => 'bg-gray-100 text-gray-800',
                                    'income' => 'bg-green-100 text-green-800',
                                    'expense' => 'bg-red-100 text-red-800',
                                    'adjustment' => 'bg-yellow-100 text-yellow-800'
                                ];
                                $typeNames = [
                                    'opening' => 'Açılış',
                                    'closing' => 'Kapanış',
                                    'income' => 'Gelir',
                                    'expense' => 'Gider',
                                    'adjustment' => 'Düzeltme'
                                ];
                                $typeIcons = [
                                    'opening' => 'fas fa-unlock',
                                    'closing' => 'fas fa-lock',
                                    'income' => 'fas fa-arrow-up',
                                    'expense' => 'fas fa-arrow-down',
                                    'adjustment' => 'fas fa-edit'
                                ];
                                $typeColor = $typeColors[$movement['type']] ?? 'bg-gray-100 text-gray-800';
                                $typeName = $typeNames[$movement['type']] ?? $movement['type'];
                                $typeIcon = $typeIcons[$movement['type']] ?? 'fas fa-receipt';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeColor ?>">
                                    <i class="<?= $typeIcon ?> mr-1"></i>
                                    <?= $typeName ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate" title="<?= esc($movement['description']) ?>">
                                    <?= esc($movement['description']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($movement['category']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span class="<?= in_array($movement['type'], ['income', 'opening']) ? 'text-green-900' : 'text-red-900' ?>">
                                    <?= in_array($movement['type'], ['income', 'opening']) ? '+' : '-' ?>
                                    <?= number_format($movement['amount'], 2) ?> ₺
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= number_format($movement['balance_after'], 2) ?> ₺
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($movement['first_name'] . ' ' . $movement['last_name']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>