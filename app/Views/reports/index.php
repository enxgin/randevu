<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Raporlar</h1>
        
        <?php if ($userRole === 'admin'): ?>
        <div class="flex items-center space-x-4">
            <label for="branch-select" class="text-sm font-medium text-gray-700">Şube:</label>
            <select id="branch-select" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Tüm Şubeler</option>
                <?php foreach ($branches as $branch): ?>
                <option value="<?= $branch['id'] ?>" <?= $branchId == $branch['id'] ? 'selected' : '' ?>>
                    <?= esc($branch['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    </div>

    <!-- Rapor Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Günlük Kasa Raporu -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-cash-register text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Günlük Kasa Raporu
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                Günlük gelir/gider takibi
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= base_url('reports/daily-cash') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-chart-line mr-2"></i>
                        Raporu Görüntüle
                    </a>
                </div>
            </div>
        </div>

        <!-- Detaylı Kasa Geçmişi -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-history text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Kasa Geçmişi
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                Detaylı kasa hareketleri
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= base_url('reports/cash-history') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-list mr-2"></i>
                        Raporu Görüntüle
                    </a>
                </div>
            </div>
        </div>

        <!-- Alacak/Borç Raporu -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Alacak/Borç Raporu
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                Borçlu müşteri takibi
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= base_url('reports/debt-report') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-user-times mr-2"></i>
                        Raporu Görüntüle
                    </a>
                </div>
            </div>
        </div>

        <!-- Personel Prim Raporu -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Personel Prim Raporu
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                Prim hesaplamaları
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= base_url('reports/staff-commission') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <i class="fas fa-calculator mr-2"></i>
                        Raporu Görüntüle
                    </a>
                </div>
            </div>
        </div>

        <!-- Finansal Dashboard -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-chart-pie text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Finansal Dashboard
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                Genel finansal durum
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= base_url('reports/financial-dashboard') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard'u Görüntüle
                    </a>
                </div>
            </div>
        </div>

        <!-- Hızlı İstatistikler (Placeholder) -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Hızlı İstatistikler
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                Anlık veriler
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <button disabled 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed">
                        <i class="fas fa-clock mr-2"></i>
                        Yakında...
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Hızlı Erişim Bağlantıları -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Hızlı Erişim</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?= base_url('payments') ?>" 
               class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <i class="fas fa-credit-card mr-2 text-green-500"></i>
                Ödemeler
            </a>
            <a href="<?= base_url('cash') ?>" 
               class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <i class="fas fa-cash-register mr-2 text-blue-500"></i>
                Kasa Yönetimi
            </a>
            <a href="<?= base_url('commissions/reports') ?>" 
               class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <i class="fas fa-percentage mr-2 text-purple-500"></i>
                Prim Yönetimi
            </a>
            <a href="<?= base_url('calendar') ?>" 
               class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                <i class="fas fa-calendar mr-2 text-indigo-500"></i>
                Randevu Takvimi
            </a>
        </div>
    </div>
</div>

<script>
// Şube değişikliği
document.getElementById('branch-select')?.addEventListener('change', function() {
    const branchId = this.value;
    const currentUrl = new URL(window.location);
    
    if (branchId) {
        currentUrl.searchParams.set('branch_id', branchId);
    } else {
        currentUrl.searchParams.delete('branch_id');
    }
    
    window.location.href = currentUrl.toString();
});
</script>
<?= $this->endSection() ?>