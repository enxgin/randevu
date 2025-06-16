<?= $this->extend('layouts/app') ?>

<?= $this->section('head') ?>
<!-- Dashboard specific styles -->
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .chart-container {
        height: 300px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Bugün ki Randevular -->
    <div class="stat-card bg-white overflow-hidden shadow rounded-lg border border-gray-200">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-calendar-day text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Bugünkü Randevular
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <?= number_format($stats['todayAppointments']) ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="<?= base_url('calendar') ?>" class="font-medium text-blue-600 hover:text-blue-500">
                    Takvimi görüntüle
                </a>
            </div>
        </div>
    </div>

    <!-- Toplam Müşteri -->
    <div class="stat-card bg-white overflow-hidden shadow rounded-lg border border-gray-200">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Toplam Müşteri
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <?= number_format($stats['totalCustomers']) ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="<?= base_url('admin/customers') ?>" class="font-medium text-green-600 hover:text-green-500">
                    Müşteri listesini görüntüle
                </a>
            </div>
        </div>
    </div>

    <!-- Günlük Ciro -->
    <div class="stat-card bg-white overflow-hidden shadow rounded-lg border border-gray-200">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-lira-sign text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Günlük Ciro
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            ₺<?= number_format($stats['dailyRevenue'], 2) ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="<?= base_url('reports/daily-cash') ?>" class="font-medium text-yellow-600 hover:text-yellow-500">
                    Detaylı rapor
                </a>
            </div>
        </div>
    </div>

    <!-- Bekleyen Ödemeler -->
    <div class="stat-card bg-white overflow-hidden shadow rounded-lg border border-gray-200">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-credit-card text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Bekleyen Ödemeler
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            ₺<?= number_format($stats['pendingPayments'], 2) ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="<?= base_url('payments/debtors') ?>" class="font-medium text-red-600 hover:text-red-500">
                    Alacakları görüntüle
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    
    <!-- Son Randevular -->
    <div class="bg-white shadow rounded-lg border border-gray-200">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Son Randevular
            </h3>
            <div class="flow-root">
                <?php if (!empty($recentAppointments)): ?>
                <ul class="-my-5 divide-y divide-gray-200">
                    <?php foreach ($recentAppointments as $appointment): ?>
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <?php
                                $initials = strtoupper(substr($appointment['customer_first_name'], 0, 1) . substr($appointment['customer_last_name'], 0, 1));
                                $colors = ['bg-pink-500', 'bg-blue-500', 'bg-purple-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500', 'bg-indigo-500'];
                                $colorIndex = crc32($appointment['customer_id']) % count($colors);
                                ?>
                                <div class="h-8 w-8 rounded-full <?= $colors[$colorIndex] ?> flex items-center justify-center">
                                    <span class="text-sm font-medium text-white"><?= $initials ?></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?= esc($appointment['customer_first_name'] . ' ' . $appointment['customer_last_name']) ?>
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    <?= esc($appointment['service_name']) ?> - <?= date('H:i', strtotime($appointment['start_time'])) ?>
                                    <?php if ($appointment['appointment_date'] != date('Y-m-d')): ?>
                                        (<?= date('d.m.Y', strtotime($appointment['appointment_date'])) ?>)
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <?php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'no_show' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusLabels = [
                                    'pending' => 'Onay Bekliyor',
                                    'confirmed' => 'Onaylandı',
                                    'completed' => 'Tamamlandı',
                                    'cancelled' => 'İptal Edildi',
                                    'no_show' => 'Gelmedi'
                                ];
                                $statusClass = $statusClasses[$appointment['status']] ?? 'bg-gray-100 text-gray-800';
                                $statusLabel = $statusLabels[$appointment['status']] ?? $appointment['status'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-gray-400 text-3xl mb-2"></i>
                    <p class="text-gray-500">Henüz randevu bulunmuyor</p>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-6">
                <a href="<?= base_url('calendar') ?>" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Tüm randevuları görüntüle
                </a>
            </div>
        </div>
    </div>

    <!-- Haftalık Performans -->
    <div class="bg-white shadow rounded-lg border border-gray-200">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Haftalık Performans (Son 7 Gün)
            </h3>
            <?php if (!empty($weeklyPerformance)): ?>
            <div class="space-y-4">
                <!-- Özet İstatistikler -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <?php
                    $totalWeeklyRevenue = array_sum(array_column($weeklyPerformance, 'revenue'));
                    $totalWeeklyAppointments = array_sum(array_column($weeklyPerformance, 'appointments'));
                    ?>
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">₺<?= number_format($totalWeeklyRevenue, 0) ?></div>
                        <div class="text-sm text-blue-500">Haftalık Ciro</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600"><?= $totalWeeklyAppointments ?></div>
                        <div class="text-sm text-green-500">Toplam Randevu</div>
                    </div>
                </div>

                <!-- Günlük Detaylar -->
                <div class="space-y-3">
                    <?php foreach ($weeklyPerformance as $day): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-blue-600"><?= $day['day_short'] ?></span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= date('d.m.Y', strtotime($day['date'])) ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= $day['appointments'] ?> randevu
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">
                                ₺<?= number_format($day['revenue'], 0) ?>
                            </div>
                            <?php if ($totalWeeklyRevenue > 0): ?>
                            <div class="text-xs text-gray-500">
                                %<?= number_format(($day['revenue'] / $totalWeeklyRevenue) * 100, 1) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Basit Bar Chart -->
                <div class="mt-6">
                    <div class="flex items-end justify-between h-32 space-x-1">
                        <?php
                        $maxRevenue = max(array_column($weeklyPerformance, 'revenue'));
                        if ($maxRevenue == 0) $maxRevenue = 1; // Sıfıra bölme hatası önleme
                        ?>
                        <?php foreach ($weeklyPerformance as $day): ?>
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-blue-200 rounded-t" style="height: <?= $maxRevenue > 0 ? (($day['revenue'] / $maxRevenue) * 100) : 0 ?>%">
                                <div class="w-full bg-blue-500 rounded-t" style="height: 100%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1"><?= $day['day_short'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-8">
                <i class="fas fa-chart-line text-gray-400 text-3xl mb-2"></i>
                <p class="text-gray-500">Henüz performans verisi bulunmuyor</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hızlı İşlemler -->
<div class="bg-white shadow rounded-lg border border-gray-200">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            Hızlı İşlemler
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php if (in_array($userRole, ['admin', 'manager', 'receptionist'])): ?>
            <a href="<?= base_url('calendar/create') ?>" class="group flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-pink-300 hover:bg-pink-50 transition-colors">
                <div class="w-12 h-12 bg-pink-500 rounded-lg flex items-center justify-center group-hover:bg-pink-600 transition-colors">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-pink-600">
                    Yeni Randevu
                </span>
            </a>
            
            <a href="<?= base_url('admin/customers/create') ?>" class="group flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-blue-600">
                    Yeni Müşteri
                </span>
            </a>
            
            <a href="<?= base_url('payments') ?>" class="group flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors">
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                    <i class="fas fa-credit-card text-white"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-green-600">
                    Ödeme Al
                </span>
            </a>
            
            <a href="<?= base_url('cash') ?>" class="group flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-yellow-300 hover:bg-yellow-50 transition-colors">
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center group-hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-cash-register text-white"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-yellow-600">
                    Kasa İşlemleri
                </span>
            </a>
            <?php endif; ?>

            <?php if ($userRole === 'staff'): ?>
            <a href="<?= base_url('calendar') ?>" class="group flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                    <i class="fas fa-calendar text-white"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-blue-600">
                    Randevu Takvimim
                </span>
            </a>
            
            <a href="<?= base_url('commissions/staff-report') ?>" class="group flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors">
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-green-600">
                    Prim Raporum
                </span>
            </a>
            <?php endif; ?>

            <?php if (in_array($userRole, ['admin', 'manager'])): ?>
            <a href="<?= base_url('reports') ?>" class="group flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors">
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center group-hover:bg-purple-600 transition-colors">
                    <i class="fas fa-chart-bar text-white"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-purple-600">
                    Raporlar
                </span>
            </a>
            <?php endif; ?>

            <?php if ($userRole === 'admin'): ?>
            <a href="<?= base_url('admin') ?>" class="group flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center group-hover:bg-indigo-600 transition-colors">
                    <i class="fas fa-cog text-white"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900 group-hover:text-indigo-600">
                    Admin Panel
                </span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Dashboard specific JavaScript can be added here
    console.log('Dashboard loaded successfully');
</script>
<?= $this->endSection() ?>