<?php
// Kullanıcının rolünü al
$userRole = session()->get('role_name');
$isLoggedIn = session()->get('is_logged_in');

// Eğer kullanıcı giriş yapmamışsa hiçbir menü gösterme
if (!$isLoggedIn) {
    return;
}
?>

<!-- Dashboard - Tüm roller için -->
<a href="/dashboard" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
    <i class="fas fa-chart-line text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
    Dashboard
</a>

<?php if ($userRole === 'admin'): ?>
    <!-- ADMIN MENÜLERI -->
    
    <!-- Randevular - Tüm şubelerin randevularını görebilir -->
    <a href="/calendar" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-calendar-alt text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Tüm Randevular
    </a>

    <!-- Müşteriler - Tüm şubelerin müşterilerini görebilir -->
    <a href="/admin/customers" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-users text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Müşteri Yönetimi
    </a>
 
    <!-- Hizmetler -->
    <div class="space-y-1">
        <button type="button" class="services-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-spa text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Hizmet Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="services-submenu space-y-1 pl-8 hidden">
            <a href="<?= base_url('admin/services') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Hizmet Listesi
            </a>
            <a href="<?= base_url('admin/service-categories') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Hizmet Kategorileri
            </a>
        </div>
    </div>

    <!-- Paketler -->
    <div class="space-y-1">
        <button type="button" class="packages-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-box text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Paket Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="packages-submenu space-y-1 pl-8 hidden">
            <a href="<?= base_url('admin/packages') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Paket Listesi
            </a>
            <a href="<?= base_url('admin/packages/create') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Yeni Paket
            </a>
            <a href="<?= base_url('admin/packages/sell') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Paket Sat
            </a>
            <a href="<?= base_url('admin/packages/sales') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Satış Raporu
            </a>
        </div>
    </div>

    <!-- Kasa & Finans - Tüm şubelerin finansal verilerini görebilir -->
    <div class="space-y-1">
        <button type="button" class="finance-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-cash-register text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Finans Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="finance-submenu space-y-1 pl-8 hidden">
            <a href="/cash" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Kasa Yönetimi
            </a>
            <a href="/payments" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Ödemeler
            </a>
            <a href="/payments/debtors" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Alacaklar
            </a>
        </div>
    </div>

    <!-- Prim Yönetimi -->
    <div class="space-y-1">
        <button type="button" class="commission-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-percentage text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Prim Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="commission-submenu space-y-1 pl-8 hidden">
            <a href="/commissions/rules" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Prim Kuralları
            </a>
            <a href="/commissions/reports" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Prim Raporları
            </a>
        </div>
    </div>

    <!-- Raporlar - Tüm şubelerin raporlarını görebilir -->
    <div class="space-y-1">
        <button type="button" class="reports-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-chart-bar text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Raporlar</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="reports-submenu space-y-1 pl-8 hidden">
            <a href="/reports" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Raporlar Ana Sayfa
            </a>
            <a href="/reports/daily-cash" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Günlük Kasa Raporu
            </a>
            <a href="/reports/cash-history" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Kasa Geçmişi
            </a>
            <a href="/reports/debt-report" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Alacak/Borç Raporu
            </a>
            <a href="/reports/staff-commission" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Personel Prim Raporu
            </a>
            <a href="/reports/financial-dashboard" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Finansal Dashboard
            </a>
        </div>
    </div>

    <!-- Bildirim Yönetimi -->
    <div class="space-y-1">
        <button type="button" class="notifications-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-bell text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Bildirim Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="notifications-submenu space-y-1 pl-8 hidden">
            <a href="/notifications" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-cog mr-2"></i>
                Bildirim Ayarları
            </a>
            <a href="/notifications/templates" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-file-alt mr-2"></i>
                Mesaj Şablonları
            </a>
            <a href="/notifications/messages" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-history mr-2"></i>
                Mesaj Geçmişi
            </a>
            <a href="/notifications/triggers" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-bolt mr-2"></i>
                Tetikleyici Kuralları
            </a>
            <a href="/notifications/queue" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-clock mr-2"></i>
                Bildirim Kuyruğu
            </a>
        </div>
    </div>

<?php elseif ($userRole === 'manager'): ?>
    <!-- YÖNETİCİ MENÜLERI (Sadece kendi şubesi) -->
    
    <!-- Randevular - Sadece kendi şubesinin randevularını görebilir -->
    <a href="/calendar" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-calendar-alt text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Randevu Takvimi
    </a>

    <!-- Müşteriler - Sadece kendi şubesinin müşterilerini görebilir -->
    <a href="/admin/customers" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-users text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Müşteri Yönetimi
    </a>
 
    <!-- Personel Yönetimi - Sadece kendi şubesinin personelini yönetebilir -->
    <a href="/admin/users" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-user-tie text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Personel Yönetimi
    </a>

    <!-- Hizmetler -->
    <div class="space-y-1">
        <button type="button" class="services-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-spa text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Hizmet Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="services-submenu space-y-1 pl-8 hidden">
            <a href="<?= base_url('admin/services') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Hizmet Listesi
            </a>
            <a href="<?= base_url('admin/service-categories') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Hizmet Kategorileri
            </a>
        </div>
    </div>

    <!-- Paketler -->
    <div class="space-y-1">
        <button type="button" class="packages-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-box text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Paket Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="packages-submenu space-y-1 pl-8 hidden">
            <a href="<?= base_url('admin/packages') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Paket Listesi
            </a>
            <a href="<?= base_url('admin/packages/create') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Yeni Paket
            </a>
            <a href="<?= base_url('admin/packages/sell') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Paket Sat
            </a>
            <a href="<?= base_url('admin/packages/sales') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Satış Raporu
            </a>
        </div>
    </div>

    <!-- Kasa & Finans - Sadece kendi şubesi -->
    <div class="space-y-1">
        <button type="button" class="finance-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-cash-register text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Kasa & Finans</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="finance-submenu space-y-1 pl-8" style="display: none;">
            <a href="/cash" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Kasa Yönetimi
            </a>
            <a href="/payments" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Ödemeler
            </a>
            <a href="/payments/debtors" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Alacaklar
            </a>
        </div>
    </div>

    <!-- Prim Yönetimi - Sadece kendi şubesi -->
    <div class="space-y-1">
        <button type="button" class="commission-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-percentage text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Prim Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="commission-submenu space-y-1 pl-8" style="display: none;">
            <a href="/commissions/rules" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Prim Kuralları
            </a>
            <a href="/commissions/reports" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Prim Raporları
            </a>
        </div>
    </div>

    <!-- Raporlar - Sadece kendi şubesi -->
    <div class="space-y-1">
        <button type="button" class="reports-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-chart-bar text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Raporlar</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="reports-submenu space-y-1 pl-8" style="display: none;">
            <a href="/reports" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Raporlar Ana Sayfa
            </a>
            <a href="/reports/daily-cash" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Günlük Kasa Raporu
            </a>
            <a href="/reports/cash-history" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Kasa Geçmişi
            </a>
            <a href="/reports/debt-report" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Alacak/Borç Raporu
            </a>
            <a href="/reports/staff-commission" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Personel Prim Raporu
            </a>
        </div>
    </div>

    <!-- Bildirim Yönetimi - Yönetici -->
    <div class="space-y-1">
        <button type="button" class="notifications-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-bell text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Bildirim Yönetimi</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="notifications-submenu space-y-1 pl-8 hidden">
            <a href="/notifications" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-cog mr-2"></i>
                Bildirim Ayarları
            </a>
            <a href="/notifications/templates" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-file-alt mr-2"></i>
                Mesaj Şablonları
            </a>
            <a href="/notifications/messages" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-history mr-2"></i>
                Mesaj Geçmişi
            </a>
            <a href="/notifications/triggers" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-bolt mr-2"></i>
                Tetikleyici Kuralları
            </a>
            <a href="/notifications/queue" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-clock mr-2"></i>
                Bildirim Kuyruğu
            </a>
        </div>
    </div>

<?php elseif ($userRole === 'receptionist'): ?>
    <!-- DANIŞMA MENÜLERI (Resepsiyon) -->
    
    <!-- Randevu Yönetimi - Oluşturma, düzenleme, silme -->
    <a href="/calendar" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-calendar-alt text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Randevu Yönetimi
    </a>

    <!-- Müşteri Yönetimi -->
    <a href="/admin/customers" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-users text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Müşteri Yönetimi
    </a>
 
    <!-- Hizmetler - Sadece görüntüleme -->
    <a href="/services" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-spa text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Hizmetler
    </a>

    <!-- Paketler - Satış yapabilir -->
    <a href="/admin/packages/sell" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-box text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Paket Satışı
    </a>

    <!-- Ödemeler - Ödeme alma, fatura oluşturma -->
    <div class="space-y-1">
        <button type="button" class="payments-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-cash-register text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Ödemeler</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="payments-submenu space-y-1 pl-8 hidden">
            <a href="/payments/create" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Ödeme Al
            </a>
            <a href="/payments" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Ödeme Geçmişi
            </a>
            <a href="/payments/debtors" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Borçlu Müşteriler
            </a>
            <a href="/cash" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                Kasa Yönetimi
            </a>
        </div>
    </div>

<?php elseif ($userRole === 'staff'): ?>
    <!-- PERSONEL MENÜLERI (Uzman/Terapist) -->
    
    <!-- Randevu Takvimim - Sadece kendi randevularını görebilir -->
    <a href="/calendar" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-calendar-user text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Randevu Takvimim
    </a>

    <!-- Prim Raporum -->
    <a href="/commissions/staff-report" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-chart-line text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Prim Raporum
    </a>

    <!-- Hizmetlerim - Sadece kendi verebileceği hizmetler -->
    <a href="/services/my-services" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        <i class="fas fa-spa text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
        Hizmetlerim
    </a>

<?php endif; ?>

<!-- Ortak Menüler (Tüm roller için) -->
<div class="pt-4 mt-4 border-t border-gray-200">
    
    <!-- Admin Panel (sadece admin rolü için görünür) -->
    <?php if ($userRole === 'admin'): ?>
        <div class="space-y-1">
            <button type="button" class="admin-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 hover:text-red-900 focus:outline-none" aria-expanded="false">
                <i class="fas fa-user-shield text-red-400 group-hover:text-red-500 mr-3 flex-shrink-0 h-6 w-6"></i>
                <span class="flex-1">Admin Panel</span>
                <i class="fas fa-chevron-right text-red-400 group-hover:text-red-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
            </button>
            <div class="admin-submenu space-y-1 pl-8 hidden">
                <a href="/admin" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-500 hover:text-red-700 hover:bg-red-50">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Admin Dashboard
                </a>
                <a href="/admin/branches" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-500 hover:text-red-700 hover:bg-red-50">
                    <i class="fas fa-building mr-2"></i>
                    Şube Yönetimi
                </a>
                <a href="/admin/roles" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-500 hover:text-red-700 hover:bg-red-50">
                    <i class="fas fa-users-cog mr-2"></i>
                    Rol Yönetimi
                </a>
                <a href="/admin/permissions" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-500 hover:text-red-700 hover:bg-red-50">
                    <i class="fas fa-key mr-2"></i>
                    İzin Yönetimi
                </a>
                <a href="/admin/users" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-500 hover:text-red-700 hover:bg-red-50">
                    <i class="fas fa-users mr-2"></i>
                    Kullanıcı Yönetimi
                </a>
                <a href="/admin/customers" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-500 hover:text-red-700 hover:bg-red-50">
                    <i class="fas fa-address-book mr-2"></i>
                    Müşteri Yönetimi (Admin)
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Ayarlar -->
    <div class="space-y-1">
        <button type="button" class="settings-menu group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none" aria-expanded="false">
            <i class="fas fa-cog text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            <span class="flex-1">Ayarlar</span>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-500 ml-3 h-5 w-5 transform transition-colors duration-150"></i>
        </button>
        <div class="settings-submenu space-y-1 pl-8 hidden">
            <a href="<?= base_url('profile') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-user mr-2"></i>
                Profil
            </a>
            <a href="<?= base_url('profile/settings') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-palette mr-2"></i>
                Tema & Bildirimler
            </a>
            <a href="<?= base_url('profile/notifications') ?>" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-bell mr-2"></i>
                Bildirimlerim
            </a>
        </div>
    </div>
    
    <!-- Kullanıcı Bilgileri ve Çıkış -->
    <div class="mt-4 pt-4 border-t border-gray-200">
        <div class="px-2 py-2 text-xs text-gray-500">
            <div class="flex items-center">
                <i class="fas fa-user-circle mr-2"></i>
                <div>
                    <div class="font-medium"><?= session()->get('first_name') . ' ' . session()->get('last_name') ?></div>
                    <div class="text-gray-400"><?= session()->get('role_name') ?> • <?= session()->get('branch_name') ?></div>
                </div>
            </div>
        </div>
        <a href="/logout" class="group flex items-center px-2 py-2 mt-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 hover:text-red-900 transition-colors">
            <i class="fas fa-sign-out-alt text-red-400 group-hover:text-red-500 mr-3 flex-shrink-0 h-6 w-6"></i>
            Çıkış Yap
        </a>
    </div>
    
</div>