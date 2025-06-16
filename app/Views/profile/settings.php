<?= $this->extend('layouts/app') ?>

<?= $this->section('head') ?>
<style>
    .setting-card {
        transition: all 0.3s ease;
    }
    .setting-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .toggle-slider {
        background-color: #ec4899;
    }
    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <!-- Başlık -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Genel Ayarlar</h1>
        <p class="mt-2 text-gray-600">Tema, bildirim ve diğer tercihlerinizi yönetin</p>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?= session()->getFlashdata('success') ?></span>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('profile/update-settings') ?>" method="POST" id="settingsForm">
        <?= csrf_field() ?>
        
        <div class="space-y-6">
            <!-- Tema Ayarları -->
            <div class="setting-card bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-palette text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-gray-900">Tema Ayarları</h3>
                            <p class="text-sm text-gray-500">Arayüz görünümünü özelleştirin</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Tema Modu</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="theme_mode" value="light" 
                                           <?= ($settings['theme_mode'] === 'light') ? 'checked' : '' ?>
                                           class="sr-only peer">
                                    <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-pink-500 peer-checked:bg-pink-50 transition-all">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-6 h-6 bg-white border border-gray-300 rounded-full flex items-center justify-center">
                                                <i class="fas fa-sun text-yellow-500"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Açık Tema</div>
                                                <div class="text-sm text-gray-500">Klasik beyaz arayüz</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="theme_mode" value="dark" 
                                           <?= ($settings['theme_mode'] === 'dark') ? 'checked' : '' ?>
                                           class="sr-only peer">
                                    <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-pink-500 peer-checked:bg-pink-50 transition-all">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-6 h-6 bg-gray-800 border border-gray-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-moon text-blue-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">Koyu Tema</div>
                                                <div class="text-sm text-gray-500">Göz dostu koyu arayüz</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bildirim Ayarları -->
            <div class="setting-card bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bell text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-gray-900">Bildirim Ayarları</h3>
                            <p class="text-sm text-gray-500">Hangi bildirimleri almak istediğinizi seçin</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Genel Bildirimler -->
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Panel İçi Bildirimler</h4>
                                <p class="text-sm text-gray-500">Yeni randevu, müşteri ve diğer sistem bildirimleri</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notifications_enabled" value="1" 
                                       <?= ($settings['notifications_enabled'] === '1') ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <!-- Ses Bildirimleri -->
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Ses Bildirimleri</h4>
                                <p class="text-sm text-gray-500">Yeni bildirimler için ses çalma</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notification_sound" value="1" 
                                       <?= ($settings['notification_sound'] === '1') ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <!-- Masaüstü Bildirimleri -->
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Masaüstü Bildirimleri</h4>
                                <p class="text-sm text-gray-500">Tarayıcı masaüstü bildirimleri (izin gerekli)</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notification_desktop" value="1" 
                                       <?= ($settings['notification_desktop'] === '1') ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <!-- E-posta Bildirimleri -->
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">E-posta Bildirimleri</h4>
                                <p class="text-sm text-gray-500">Önemli güncellemeler için e-posta gönderimi</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notification_email" value="1" 
                                       <?= ($settings['notification_email'] === '1') ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bildirim Test Alanı -->
            <div class="setting-card bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-vial text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-gray-900">Bildirim Testi</h3>
                            <p class="text-sm text-gray-500">Bildirim ayarlarınızı test edin</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button type="button" onclick="testNotification('info')"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                            <i class="fas fa-info-circle mr-2"></i>
                            Bilgi Bildirimi Test Et
                        </button>
                        <button type="button" onclick="testNotification('success')"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                            <i class="fas fa-check-circle mr-2"></i>
                            Başarı Bildirimi Test Et
                        </button>
                        <button type="button" onclick="sendRealTestNotification()"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                            <i class="fas fa-bell mr-2"></i>
                            Gerçek Bildirim Gönder
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kaydet Butonu -->
        <div class="mt-8 flex items-center justify-between">
            <a href="<?= base_url('profile') ?>" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
            <button type="submit" 
                    class="bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                <i class="fas fa-save mr-2"></i>
                Ayarları Kaydet
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Masaüstü bildirim izni kontrolü
    document.addEventListener('DOMContentLoaded', function() {
        const desktopNotificationCheckbox = document.querySelector('input[name="notification_desktop"]');
        
        if (desktopNotificationCheckbox) {
            desktopNotificationCheckbox.addEventListener('change', function() {
                if (this.checked && Notification.permission === 'default') {
                    Notification.requestPermission().then(function(permission) {
                        if (permission !== 'granted') {
                            desktopNotificationCheckbox.checked = false;
                            alert('Masaüstü bildirimleri için tarayıcı izni gereklidir.');
                        }
                    });
                }
            });
        }
    });

    // Test bildirimi gönder
    function testNotification(type) {
        const titles = {
            'info': 'Bilgi Bildirimi',
            'success': 'Başarı Bildirimi',
            'warning': 'Uyarı Bildirimi',
            'error': 'Hata Bildirimi'
        };

        const messages = {
            'info': 'Bu bir test bilgi bildirimidir.',
            'success': 'Test başarıyla tamamlandı!',
            'warning': 'Bu bir test uyarı bildirimidir.',
            'error': 'Bu bir test hata bildirimidir.'
        };

        // Panel içi bildirim simülasyonu
        showInAppNotification(titles[type], messages[type], type);

        // Masaüstü bildirimi (eğer izin varsa)
        if (Notification.permission === 'granted') {
            new Notification(titles[type], {
                body: messages[type],
                icon: '/favicon.ico'
            });
        }
    }

    // Panel içi bildirim göster
    function showInAppNotification(title, message, type) {
        const colors = {
            'info': 'bg-blue-100 border-blue-400 text-blue-700',
            'success': 'bg-green-100 border-green-400 text-green-700',
            'warning': 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'error': 'bg-red-100 border-red-400 text-red-700'
        };

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 max-w-sm w-full ${colors[type]} border px-4 py-3 rounded shadow-lg z-50`;
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

        // 5 saniye sonra otomatik kaldır
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Gerçek panel içi bildirim gönder
    function sendRealTestNotification() {
        const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]')?.value;
        
        fetch('<?= base_url('profile/send-test-notification') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `<?= csrf_token() ?>=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showInAppNotification('Test Başarılı', data.message, 'success');
                // Bildirim sayacını güncelle
                if (window.loadUnreadCount) {
                    window.loadUnreadCount();
                }
                // Bildirim dropdown'unu güncelle
                if (window.loadRecentNotifications) {
                    window.loadRecentNotifications();
                }
            } else {
                showInAppNotification('Test Hatası', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Test bildirimi hatası:', error);
            showInAppNotification('Bağlantı Hatası', 'Test bildirimi gönderilemedi.', 'error');
        });
    }

    // Form validasyonu
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
        // Tema seçimi kontrolü
        const themeSelected = document.querySelector('input[name="theme_mode"]:checked');
        if (!themeSelected) {
            e.preventDefault();
            alert('Lütfen bir tema seçin.');
            return false;
        }
    });

    // Tema değişikliğini canlı önizleme
    document.querySelectorAll('input[name="theme_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'dark') {
                document.body.classList.add('dark');
                document.body.classList.remove('bg-gray-50');
                document.body.classList.add('bg-gray-900');
            } else {
                document.body.classList.remove('dark');
                document.body.classList.remove('bg-gray-900');
                document.body.classList.add('bg-gray-50');
            }
        });
    });
</script>
<?= $this->endSection() ?>