<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Güzellik Salonu Yönetim Sistemi</title>
    <meta name="description" content="Güzellik salonu randevu ve yönetim sistemi">
    
    <!-- TailwindCSS -->
    <link rel="stylesheet" href="/assets/css/output.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .beauty-gradient {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
        }
        .sidebar-shadow {
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    
    <?= $this->renderSection('head') ?>
</head>
<body class="<?= session('theme_mode') === 'dark' ? 'dark bg-gray-900' : 'bg-gray-50' ?> font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside id="sidebar" class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto bg-white dark:bg-gray-800 sidebar-shadow">
                    
                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0 px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 beauty-gradient rounded-lg flex items-center justify-center">
                                <i class="fas fa-spa text-white text-sm"></i>
                            </div>
                            <h1 class="ml-3 text-xl font-bold text-gray-900 dark:text-white">
                                Beauty<span class="text-pink-500">Pro</span>
                            </h1>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="mt-8 flex-1 px-2 space-y-1">
                        <?= $this->include('layouts/partials/sidebar') ?>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            
            <!-- Top Navigation -->
            <header class="relative z-10 flex-shrink-0 flex h-16 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <button id="sidebar-toggle" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-pink-500 lg:hidden">
                    <span class="sr-only">Menüyü aç</span>
                    <i class="fas fa-bars h-6 w-6"></i>
                </button>
                
                <div class="flex-1 px-4 flex justify-between items-center">
                    <!-- Page Title -->
                    <div class="flex-1">
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                            <?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?>
                        </h1>
                        <?php if (isset($breadcrumb)): ?>
                            <nav class="flex" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-4">
                                    <?php foreach ($breadcrumb as $index => $item): ?>
                                        <li>
                                            <div class="flex items-center">
                                                <?php if ($index > 0): ?>
                                                    <i class="fas fa-chevron-right text-gray-400 mr-4"></i>
                                                <?php endif; ?>
                                                <?php if (isset($item['url']) && $index < count($breadcrumb) - 1): ?>
                                                    <a href="<?= $item['url'] ?>" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                                        <?= $item['title'] ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-sm font-medium text-gray-500">
                                                        <?= $item['title'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Top Navigation Items -->
                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        
                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notifications-button" class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 relative">
                                <span class="sr-only">Bildirimleri görüntüle</span>
                                <i class="fas fa-bell h-6 w-6"></i>
                                <span id="notification-badge" class="hidden absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">0</span>
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div id="notifications-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="py-1">
                                    <!-- Header -->
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Bildirimler</h3>
                                            <button onclick="markAllNotificationsRead()" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                Tümünü Okundu İşaretle
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Notifications List -->
                                    <div id="notifications-list" class="max-h-96 overflow-y-auto">
                                        <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-bell-slash text-2xl mb-2"></i>
                                            <p class="text-sm">Yeni bildirim yok</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-600">
                                        <a href="<?= base_url('profile/notifications') ?>" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            Tüm bildirimleri görüntüle
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="ml-3 relative">
                            <div>
                                <button id="user-menu-button" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Kullanıcı menüsünü aç</span>
                                    <div class="h-8 w-8 rounded-full bg-pink-500 flex items-center justify-center">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300 font-medium">
                                        <?= session('first_name') . ' ' . session('last_name') ?>
                                    </span>
                                    <i class="fas fa-chevron-down ml-2 text-gray-400 text-xs"></i>
                                </button>
                            </div>
                            
                            <!-- User Dropdown Menu -->
                            <div id="user-menu" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <a href="<?= base_url('profile') ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-user-circle mr-2"></i>Profilim
                                </a>
                                <a href="<?= base_url('profile/settings') ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-cog mr-2"></i>Ayarlar
                                </a>
                                <a href="<?= base_url('profile/notifications') ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-bell mr-2"></i>Bildirimler
                                </a>
                                <div class="border-t border-gray-100 dark:border-gray-600"></div>
                                <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Çıkış Yap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        
                        <!-- Flash Messages -->
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-800">
                                            <?= session()->getFlashdata('success') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-800">
                                            <?= session()->getFlashdata('error') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session()->getFlashdata('warning')): ?>
                            <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-800">
                                            <?= session()->getFlashdata('warning') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Page Content -->
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 z-40 lg:hidden">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button id="sidebar-close" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Menüyü kapat</span>
                    <i class="fas fa-times text-white h-6 w-6"></i>
                </button>
            </div>
            
            <!-- Mobile Sidebar Content -->
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 beauty-gradient rounded-lg flex items-center justify-center">
                            <i class="fas fa-spa text-white text-sm"></i>
                        </div>
                        <h1 class="ml-3 text-xl font-bold text-gray-900 dark:text-white">
                            Beauty<span class="text-pink-500">Pro</span>
                        </h1>
                    </div>
                </div>
                <nav class="mt-5 px-2 space-y-1">
                    <?= $this->include('layouts/partials/sidebar') ?>
                </nav>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Sidebar Toggle Functionality
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        });
        
        document.getElementById('sidebar-close').addEventListener('click', function() {
            document.getElementById('sidebar-overlay').classList.add('hidden');
        });
        
        document.getElementById('sidebar-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // User Menu Toggle
        document.getElementById('user-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        });
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(e) {
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
    
<script>
    // Menü toggle fonksiyonları
    document.addEventListener('DOMContentLoaded', function() {
        // Admin menü toggle (sadece admin kullanıcılar için)
        <?php if (session()->get('role_name') === 'admin'): ?>
        const adminMenu = document.querySelector('.admin-menu');
        if (adminMenu && !adminMenu.dataset.listenerAttached) {
            adminMenu.addEventListener('click', function() {
                toggleSubmenu('admin');
            });
            adminMenu.dataset.listenerAttached = 'true';
        }
        <?php endif; ?>

        // Diğer menü toggle'ları
        const menuButtons = [
            { button: '.services-menu', submenu: 'services' },
            { button: '.packages-menu', submenu: 'packages' },
            { button: '.finance-menu', submenu: 'finance' },
            { button: '.commission-menu', submenu: 'commission' },
            { button: '.reports-menu', submenu: 'reports' },
            { button: '.payments-menu', submenu: 'payments' },
            { button: '.notifications-menu', submenu: 'notifications' },
            { button: '.settings-menu', submenu: 'settings' }
        ];

        menuButtons.forEach(menu => {
            const button = document.querySelector(menu.button);
            // Her iki sidebar'da da (desktop ve mobile) aynı class'lara sahip butonlar olabileceği için
            // querySelectorAll kullanıp her birine listener ekleyelim.
            const buttons = document.querySelectorAll(menu.button);
            buttons.forEach(btn => {
                if (btn && !btn.dataset.listenerAttached) {
                    btn.addEventListener('click', function() {
                        // Hangi submenu'nun toggle edileceğini belirlemek için
                        // butonun en yakın `.space-y-1` parent'ını bulup onun içindeki submenu'yü hedefleyebiliriz
                        // veya daha basitçe, class'lar unique ise doğrudan menu.submenu ile devam edebiliriz.
                        // Şimdilik mevcut yapıya güveniyoruz.
                        toggleSubmenu(menu.submenu, btn);
                    });
                    btn.dataset.listenerAttached = 'true';
                }
            });
        });

        function toggleSubmenu(menuName, clickedButton) {
            // Eğer tıklanan buton bir submenu içindeyse (yani mobil menüdeki ana buton değilse) işlem yapma.
            // Bu, mobil ve desktop menülerinin aynı anda açılıp kapanmasını engellemek için bir önlem olabilir,
            // ancak şu anki yapıda her iki menü de aynı class'ları kullandığı için daha dikkatli bir seçici gerekebilir.
            // Şimdilik bu kontrolü basitleştiriyoruz.

            // Seçicileri düzelt
            const desktopSubmenu = document.querySelector(`#sidebar .${menuName}-submenu`);
            const desktopButton = document.querySelector(`#sidebar .${menuName}-menu`);
            
            const mobileSubmenu = document.querySelector(`#sidebar-overlay .${menuName}-submenu`);
            const mobileButton = document.querySelector(`#sidebar-overlay .${menuName}-menu`);

            let targetSubmenu = null;
            let targetButton = null;

            if (clickedButton && clickedButton.closest('#sidebar-overlay')) {
                targetSubmenu = mobileSubmenu;
                targetButton = mobileButton;
            } else if (clickedButton && clickedButton.closest('#sidebar')) {
                targetSubmenu = desktopSubmenu;
                targetButton = desktopButton;
            } else {
                // Eğer tıklanan buton spesifik bir sidebar'a ait değilse (bu durum olmamalı)
                // veya clickedButton null ise (admin menu için eski çağrı)
                // her ikisini de deneyebiliriz veya birincil olanı (desktop) hedefleyebiliriz.
                // Bu senaryoyu daha iyi ele almak için adminMenu listener'ını da güncellemek gerekebilir.
                // Şimdilik, eğer admin menüsü ise her iki versiyonu da toggle etmeyi deneyelim.
                 // Admin menüsü için özel seçiciler (yukarıda tanımlandı)
                const desktopAdminSubmenu = document.querySelector('#sidebar .admin-submenu');
                const desktopAdminButton = document.querySelector('#sidebar .admin-menu');
                const mobileAdminSubmenu = document.querySelector('#sidebar-overlay .admin-submenu');
                const mobileAdminButton = document.querySelector('#sidebar-overlay .admin-menu');

                if (menuName === 'admin') {
                    // clickedButton burada null olabilir, bu yüzden doğrudan desktop/mobile kontrolü yapalım
                    // veya tıklanan butona göre (eğer varsa)
                    if (clickedButton && clickedButton.closest('#sidebar-overlay')) {
                         if (mobileAdminSubmenu && mobileAdminButton) toggleSingle(mobileAdminSubmenu, mobileAdminButton);
                    } else { // Varsayılan olarak desktop veya genel admin çağrısı
                         if (desktopAdminSubmenu && desktopAdminButton) toggleSingle(desktopAdminSubmenu, desktopAdminButton);
                    }
                    // Eğer mobil menü de varsa ve admin ise onu da toggle etmeyi deneyebiliriz,
                    // ama bu genellikle istenmez. Şimdilik sadece birini hedefleyelim.
                    return;
                }
                // Diğer menüler için, tıklanan butona göre hedef belirle (yukarıda yapıldı)
                // Eğer clickedButton yoksa (eski yapı), desktop'ı varsayalım
                targetSubmenu = desktopSubmenu; // Varsayılan olarak desktop
                targetButton = desktopButton;
            }
            
            if (targetSubmenu && targetButton) {
                toggleSingle(targetSubmenu, targetButton);
            }
        }

        function toggleSingle(submenu, button) {
            const chevron = button.querySelector('.fa-chevron-right');
            const isHidden = submenu.classList.contains('hidden');

            if (isHidden) {
                submenu.classList.remove('hidden');
                if (chevron) chevron.style.transform = 'rotate(90deg)';
                button.setAttribute('aria-expanded', 'true');
            } else {
                submenu.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
                button.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Bildirim sistemi
    document.addEventListener('DOMContentLoaded', function() {
        const notificationsButton = document.getElementById('notifications-button');
        const notificationsDropdown = document.getElementById('notifications-dropdown');
        const notificationBadge = document.getElementById('notification-badge');
        const notificationsList = document.getElementById('notifications-list');

        // Bildirim dropdown toggle
        if (notificationsButton) {
            notificationsButton.addEventListener('click', function() {
                notificationsDropdown.classList.toggle('hidden');
                if (!notificationsDropdown.classList.contains('hidden')) {
                    loadRecentNotifications();
                }
            });
        }

        // Dropdown dışına tıklandığında kapat
        document.addEventListener('click', function(e) {
            if (!notificationsButton.contains(e.target) && !notificationsDropdown.contains(e.target)) {
                notificationsDropdown.classList.add('hidden');
            }
        });

        // Okunmamış bildirim sayısını yükle
        window.loadUnreadCount = function() {
            fetch('<?= base_url('profile/unread-count') ?>', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(data.count);
                }
            })
            .catch(error => console.error('Error loading unread count:', error));
        }

        // Son bildirimleri yükle
        window.loadRecentNotifications = function() {
            fetch('<?= base_url('profile/recent-notifications') ?>', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayNotifications(data.notifications);
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
        }

        // Bildirimleri görüntüle
        function displayNotifications(notifications) {
            if (notifications.length === 0) {
                notificationsList.innerHTML = `
                    <div class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-2xl mb-2"></i>
                        <p class="text-sm">Yeni bildirim yok</p>
                    </div>
                `;
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                const iconClass = getNotificationIcon(notification.type);
                const colorClass = getNotificationColor(notification.type);
                const timeAgo = formatTimeAgo(notification.created_at);

                html += `
                    <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer" onclick="markNotificationRead(${notification.id})">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full ${colorClass} flex items-center justify-center">
                                    <i class="${iconClass} text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    ${notification.title}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    ${notification.message}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    ${timeAgo}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });

            notificationsList.innerHTML = html;
        }

        // Bildirim badge'ini güncelle
        function updateNotificationBadge(count) {
            if (count > 0) {
                notificationBadge.textContent = count > 99 ? '99+' : count;
                notificationBadge.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
            }
        }

        // Bildirim ikonunu getir
        function getNotificationIcon(type) {
            const icons = {
                'info': 'fas fa-info-circle',
                'success': 'fas fa-check-circle',
                'warning': 'fas fa-exclamation-triangle',
                'error': 'fas fa-times-circle'
            };
            return icons[type] || 'fas fa-bell';
        }

        // Bildirim rengini getir
        function getNotificationColor(type) {
            const colors = {
                'info': 'bg-blue-100 text-blue-600',
                'success': 'bg-green-100 text-green-600',
                'warning': 'bg-yellow-100 text-yellow-600',
                'error': 'bg-red-100 text-red-600'
            };
            return colors[type] || 'bg-gray-100 text-gray-600';
        }

        // Zaman formatla
        function formatTimeAgo(datetime) {
            const now = new Date();
            const time = new Date(datetime);
            const diff = Math.floor((now - time) / 1000);

            if (diff < 60) return 'Az önce';
            if (diff < 3600) return Math.floor(diff / 60) + ' dakika önce';
            if (diff < 86400) return Math.floor(diff / 3600) + ' saat önce';
            if (diff < 2592000) return Math.floor(diff / 86400) + ' gün önce';

            return time.toLocaleDateString('tr-TR');
        }

        // Sayfa yüklendiğinde okunmamış sayıyı yükle
        loadUnreadCount();

        // Her 30 saniyede bir okunmamış sayıyı güncelle
        setInterval(loadUnreadCount, 30000);
    });

    // Global fonksiyonlar
    function markNotificationRead(notificationId) {
        fetch('<?= base_url('profile/mark-notification-read') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `notification_id=${notificationId}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Okunmamış sayıyı güncelle
                const badge = document.getElementById('notification-badge');
                const currentCount = parseInt(badge.textContent) || 0;
                if (currentCount > 1) {
                    badge.textContent = currentCount - 1;
                } else {
                    badge.classList.add('hidden');
                }
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    function markAllNotificationsRead() {
        fetch('<?= base_url('profile/mark-all-notifications-read') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('notification-badge').classList.add('hidden');
                document.getElementById('notifications-list').innerHTML = `
                    <div class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-2xl mb-2"></i>
                        <p class="text-sm">Yeni bildirim yok</p>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }

    // Panel içi bildirim göster
    function showInAppNotification(title, message, type = 'info') {
        const colors = {
            'info': 'bg-blue-100 border-blue-400 text-blue-700',
            'success': 'bg-green-100 border-green-400 text-green-700',
            'warning': 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'error': 'bg-red-100 border-red-400 text-red-700'
        };

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 max-w-sm w-full ${colors[type]} border px-4 py-3 rounded shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
        notification.innerHTML = `
            <div class="flex">
                <div class="flex-1">
                    <strong class="font-bold">${title}</strong>
                    <span class="block sm:inline"> ${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Animasyon için kısa bir gecikme
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // 5 saniye sonra otomatik kaldır
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>