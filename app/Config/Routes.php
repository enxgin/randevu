<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Ana sayfa - giriş yapmamışsa login'e yönlendir
$routes->get('/', 'Auth::redirectHome');

// Auth rotaları (giriş, çıkış, yetkisiz erişim)
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/unauthorized', 'Auth::unauthorized');

// Dashboard rotaları (auth filter gerekli)
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Takvim/Randevu Yönetimi (auth filter gerekli)
$routes->group('calendar', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Calendar::index');
    $routes->get('events', 'Calendar::getEvents'); // AJAX - takvim eventleri
    $routes->get('create', 'Calendar::create');
    $routes->post('create', 'Calendar::create');
    $routes->get('edit/(:num)', 'Calendar::edit/$1');
    $routes->post('edit/(:num)', 'Calendar::edit/$1');
    $routes->post('update-status', 'Calendar::updateStatus'); // AJAX - durum güncelleme
    $routes->delete('delete/(:num)', 'Calendar::delete/$1'); // AJAX - randevu silme
    $routes->post('check-availability', 'Calendar::checkAvailability'); // AJAX - müsaitlik kontrolü
    $routes->get('service-staff', 'Calendar::getServiceStaff'); // AJAX - hizmet personelleri
    
    // Randevu Sihirbazı için yeni endpoint'ler
    $routes->get('search-customers', 'Calendar::searchCustomers'); // AJAX - müşteri arama
    $routes->post('quick-add-customer', 'Calendar::quickAddCustomer'); // AJAX - hızlı müşteri ekleme
    $routes->get('customer-packages', 'Calendar::getCustomerPackages'); // AJAX - müşteri paketleri
    $routes->get('suggested-staff', 'Calendar::getSuggestedStaff'); // AJAX - akıllı personel önerisi
    $routes->get('available-time-slots', 'Calendar::getAvailableTimeSlots'); // AJAX - uygun saatler
    $routes->post('create-recurring', 'Calendar::createRecurring'); // AJAX - tekrar eden randevu
    
    // Basamak 15 - Gelişmiş Randevu Düzenleme
    $routes->post('update-drag-drop', 'Calendar::updateAppointmentDragDrop'); // AJAX - sürükle-bırak güncelleme
    $routes->post('copy-appointment', 'Calendar::copyAppointment'); // AJAX - randevu kopyalama
    $routes->post('bulk-update', 'Calendar::bulkUpdate'); // AJAX - toplu işlemler
    $routes->get('pending-payments/(:num)', 'Calendar::getPendingPayments/$1', ['filter' => 'auth']);
});

// Ödeme Yönetimi Rotaları (auth filter gerekli)
$routes->group('payments', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Payment::index'); // Ödeme listesi
    $routes->get('create', 'Payment::create'); // Ödeme alma formu
    $routes->post('create', 'Payment::store'); // Ödeme kaydetme
    $routes->get('show/(:num)', 'Payment::show/$1'); // Ödeme detayları
    $routes->get('refund/(:num)', 'Payment::refund/$1'); // İade formu
    $routes->post('refund/(:num)', 'Payment::refund/$1'); // İade işlemi
    $routes->get('debtors', 'Payment::debtors'); // Borçlu müşteriler
    $routes->get('credits', 'Payment::credits'); // Kredi bakiyesi olan müşteriler
    $routes->get('reports', 'Payment::reports'); // Ödeme raporları
    
    // AJAX API'ler
    $routes->get('search-customers', 'Payment::searchCustomers'); // AJAX - müşteri arama
});

// Kasa Yönetimi Rotaları (auth filter gerekli)
$routes->group('cash', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Cash::index'); // Kasa ana sayfa
    $routes->get('open', 'Cash::open'); // Kasa açılış formu
    $routes->post('open', 'Cash::open'); // Kasa açılış işlemi
    $routes->get('close', 'Cash::close'); // Kasa kapanış formu
    $routes->post('close', 'Cash::close'); // Kasa kapanış işlemi
    $routes->get('add-movement', 'Cash::addMovement'); // Manuel hareket formu
    $routes->post('add-movement', 'Cash::addMovement'); // Manuel hareket ekleme
    $routes->get('history', 'Cash::history'); // Kasa hareketleri geçmişi
    $routes->get('reports', 'Cash::reports'); // Kasa raporları
    $routes->delete('delete-movement/(:num)', 'Cash::deleteMovement/$1'); // AJAX - hareket silme
});

// Prim Yönetimi Rotaları (auth filter gerekli)
$routes->group('commissions', ['filter' => 'auth'], function($routes) {
    // Prim Kuralları
    $routes->get('rules', 'Commission::rules'); // Prim kuralları listesi
    $routes->get('rules/create', 'Commission::createRule'); // Yeni prim kuralı formu
    $routes->post('rules/store', 'Commission::storeRule'); // Prim kuralı kaydetme
    $routes->get('rules/edit/(:num)', 'Commission::editRule/$1'); // Prim kuralı düzenleme formu
    $routes->post('rules/update/(:num)', 'Commission::updateRule/$1'); // Prim kuralı güncelleme
    $routes->delete('rules/delete/(:num)', 'Commission::deleteRule/$1'); // AJAX - prim kuralı silme
    
    // Prim Raporları
    $routes->get('reports', 'Commission::reports'); // Prim raporları (admin/yönetici/danışma)
    $routes->get('staff-report', 'Commission::staffReport'); // Personel prim raporu
    
    // AJAX API'ler
    $routes->get('users-by-branch/(:num)', 'Commission::getUsersByBranch/$1'); // Şubeye göre personeller
    $routes->get('services-by-branch/(:num)', 'Commission::getServicesByBranch/$1'); // Şubeye göre hizmetler
});

// Raporlar (auth filter gerekli)
$routes->group('reports', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Reports::index'); // Raporlar ana sayfa
    $routes->get('daily-cash', 'Reports::dailyCashReport'); // Günlük kasa raporu
    $routes->get('cash-history', 'Reports::cashHistory'); // Detaylı kasa geçmişi
    $routes->get('debt-report', 'Reports::debtReport'); // Alacak/Borç raporu
    $routes->get('staff-commission', 'Reports::staffCommissionReport'); // Personel prim raporu
    $routes->get('financial-dashboard', 'Reports::financialDashboard'); // Finansal dashboard
});

// Bildirim Yönetimi Rotaları (auth filter gerekli)
$routes->group('notifications', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Notification::index'); // Bildirim ayarları ana sayfa
    $routes->post('save-settings', 'Notification::saveSettings'); // Ayarları kaydet
    $routes->post('send-test', 'Notification::sendTest'); // Test mesajı gönder
    
    // Mesaj Şablonları
    $routes->get('templates', 'Notification::templates'); // Şablon listesi
    $routes->get('templates/create', 'Notification::createTemplate'); // Yeni şablon formu
    $routes->post('templates/save', 'Notification::saveTemplate'); // Şablon kaydet
    $routes->get('templates/edit/(:num)', 'Notification::editTemplate/$1'); // Şablon düzenle
    $routes->post('templates/update/(:num)', 'Notification::updateTemplate/$1'); // Şablon güncelle
    $routes->delete('templates/delete/(:num)', 'Notification::deleteTemplate/$1'); // Şablon sil
    $routes->post('templates/create-defaults', 'Notification::createDefaultTemplates'); // Varsayılan şablonlar
    
    // Gönderilen Mesajlar
    $routes->get('messages', 'Notification::messages'); // Mesaj geçmişi
    
    // Tetikleyici Kuralları (Basamak 21)
    $routes->get('triggers', 'Notification::triggers'); // Tetikleyici listesi
    $routes->get('triggers/create', 'Notification::createTrigger'); // Yeni tetikleyici formu
    $routes->post('triggers/save', 'Notification::saveTrigger'); // Tetikleyici kaydet
    $routes->get('triggers/edit/(:num)', 'Notification::editTrigger/$1'); // Tetikleyici düzenle
    $routes->post('triggers/update/(:num)', 'Notification::updateTrigger/$1'); // Tetikleyici güncelle
    $routes->delete('triggers/delete/(:num)', 'Notification::deleteTrigger/$1'); // Tetikleyici sil
    $routes->post('triggers/toggle/(:num)', 'Notification::toggleTrigger/$1'); // Tetikleyici aktif/pasif
    $routes->post('triggers/create-defaults', 'Notification::createDefaultTriggers'); // Varsayılan tetikleyiciler
    $routes->post('triggers/test', 'Notification::testTrigger'); // Tetikleyici test et
    
    // Bildirim Kuyruğu
    $routes->get('queue', 'Notification::queue'); // Kuyruk listesi
});

// Profil ve Ayarlar Rotaları (auth filter gerekli)
$routes->group('profile', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Profile::index'); // Profil sayfası
    $routes->post('update', 'Profile::update'); // Profil güncelleme
    $routes->get('change-password', 'Profile::changePassword'); // Şifre değiştirme sayfası
    $routes->post('change-password', 'Profile::updatePassword'); // Şifre güncelleme
    $routes->get('settings', 'Profile::settings'); // Genel ayarlar
    $routes->post('update-settings', 'Profile::updateSettings'); // Ayarları güncelle
    $routes->get('notifications', 'Profile::notifications'); // Bildirimler sayfası
    $routes->post('send-test-notification', 'Profile::sendTestNotification'); // Test bildirimi gönder
    
    // AJAX API'ler
    $routes->post('mark-notification-read', 'Profile::markNotificationRead'); // Bildirimi okundu işaretle
    $routes->post('mark-all-notifications-read', 'Profile::markAllNotificationsRead'); // Tümünü okundu işaretle
    $routes->get('unread-count', 'Profile::getUnreadCount'); // Okunmamış bildirim sayısı
    $routes->get('recent-notifications', 'Profile::getRecentNotifications'); // Son bildirimler
});

// Admin Panel Rotaları (sadece Admin rolü erişebilir)
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    // Ana admin paneli
    $routes->get('/', 'Admin::index');
    $routes->get('dashboard', 'Admin::index');
    
    // Şube Yönetimi
    $routes->get('branches', 'Admin::branches');
    $routes->get('branches/create', 'Admin::createBranch');
    $routes->post('branches/create', 'Admin::createBranch');
    $routes->get('branches/edit/(:num)', 'Admin::editBranch/$1');
    $routes->post('branches/edit/(:num)', 'Admin::editBranch/$1');
    $routes->delete('branches/delete/(:num)', 'Admin::deleteBranch/$1');
    
    // Rol Yönetimi
    $routes->get('roles', 'Admin::roles');
    $routes->get('roles/create', 'Admin::createRole');
    $routes->post('roles/create', 'Admin::createRole');
    $routes->get('roles/edit/(:num)', 'Admin::editRole/$1');
    $routes->post('roles/edit/(:num)', 'Admin::editRole/$1');
    $routes->delete('roles/delete/(:num)', 'Admin::deleteRole/$1');
    
    // İzin Yönetimi
    $routes->get('permissions', 'Admin::permissions');
    $routes->get('permissions/create', 'Admin::createPermission');
    $routes->post('permissions/create', 'Admin::createPermission');
    $routes->get('permissions/edit/(:num)', 'Admin::editPermission/$1');
    $routes->post('permissions/edit/(:num)', 'Admin::editPermission/$1');
    $routes->delete('permissions/delete/(:num)', 'Admin::deletePermission/$1');
    
    // Kullanıcı Yönetimi
    $routes->get('users', 'Admin::users');
    $routes->get('users/create', 'Admin::createUser');
    $routes->post('users/create', 'Admin::createUser');
    $routes->get('users/view/(:num)', 'Admin::viewUser/$1');
    $routes->get('users/edit/(:num)', 'Admin::editUser/$1');
    $routes->post('users/edit/(:num)', 'Admin::editUser/$1');
    $routes->delete('users/delete/(:num)', 'Admin::deleteUser/$1');

    // Müşteri Yönetimi
    $routes->get('customers', 'Admin::customers'); // Müşteri listesi
    $routes->get('customers/create', 'Admin::createCustomer'); // Müşteri oluşturma formu
    $routes->post('customers/create', 'Admin::createCustomer'); // Müşteri oluşturma işlemi
    $routes->get('customers/view/(:num)', 'Admin::viewCustomer/$1'); // Müşteri detayları
    $routes->get('customers/edit/(:num)', 'Admin::editCustomer/$1'); // Müşteri düzenleme formu
    $routes->post('customers/edit/(:num)', 'Admin::editCustomer/$1'); // Müşteri düzenleme işlemi
    $routes->delete('customers/delete/(:num)', 'Admin::deleteCustomer/$1'); // Müşteri silme işlemi

    // Hizmet Kategori Yönetimi
    $routes->get('service-categories', 'Admin::serviceCategories');
    $routes->get('service-categories/create', 'Admin::createServiceCategory');
    $routes->post('service-categories/create', 'Admin::createServiceCategory');
    $routes->get('service-categories/edit/(:num)', 'Admin::editServiceCategory/$1');
    $routes->post('service-categories/edit/(:num)', 'Admin::editServiceCategory/$1');
    $routes->post('service-categories/delete/(:num)', 'Admin::deleteServiceCategory/$1'); // Genellikle POST veya DELETE
    $routes->delete('service-categories/delete/(:num)', 'Admin::deleteServiceCategory/$1');


    // Hizmet Yönetimi
    $routes->get('services', 'Admin::services');
    $routes->get('services/create', 'Admin::createService');
    $routes->post('services/create', 'Admin::createService');
    $routes->get('services/edit/(:num)', 'Admin::editService/$1');
    $routes->post('services/edit/(:num)', 'Admin::editService/$1');
    $routes->post('services/delete/(:num)', 'Admin::deleteService/$1'); // Genellikle POST veya DELETE
    $routes->delete('services/delete/(:num)', 'Admin::deleteService/$1');

    // Paket Yönetimi
    $routes->get('packages', 'Admin::packages');
    $routes->get('packages/create', 'Admin::createPackage');
    $routes->post('packages/create', 'Admin::createPackage');
    $routes->get('packages/view/(:num)', 'Admin::viewPackage/$1');
    $routes->get('packages/edit/(:num)', 'Admin::editPackage/$1');
    $routes->post('packages/edit/(:num)', 'Admin::editPackage/$1');
    $routes->post('packages/delete/(:num)', 'Admin::deletePackage/$1');
    $routes->delete('packages/delete/(:num)', 'Admin::deletePackage/$1');

    // Paket Satışı
    $routes->get('packages/sell', 'Admin::sellPackage');
    $routes->post('packages/sell', 'Admin::sellPackage');
    $routes->get('packages/sales', 'Admin::packageSales');

    // Paket Takibi ve Raporlama (Basamak 16)
    $routes->get('packages/reports', 'Admin::packageReports');
    $routes->post('packages/expire-old', 'Admin::expireOldPackages'); // AJAX - süresi dolmuş paketleri güncelle
    $routes->get('packages/alerts', 'Admin::getPackageAlerts'); // AJAX - paket uyarıları

    // AJAX API'ler
    $routes->get('api/packages/branch/(:num)', 'Admin::getPackagesByBranch/$1');
    $routes->get('api/customer-packages/(:num)', 'Admin::getCustomerPackages/$1');
    
    // Panel İçi Bildirim API'leri
    $routes->post('send-test-notification', 'Admin::sendTestNotification'); // AJAX - test bildirimi gönder
});

// Eski welcome sayfası (geliştirme amaçlı)
$routes->get('/welcome', 'Home::index');

// Test route
$routes->get('/test', function() {
    return 'Test çalışıyor';
});

// Test admin route
$routes->get('/admin-test', 'Admin::test');

// Test session route
$routes->get('/test-session', 'Test::checkSession');

// Test theme route
$routes->get('/test-theme', function() {
    return view('test_theme');
});
