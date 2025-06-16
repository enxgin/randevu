<?= $this->extend('layouts/app') ?>

<?= $this->section('head') ?>
<style>
    .notification-item {
        transition: all 0.3s ease;
    }
    .notification-item:hover {
        background-color: #f9fafb;
    }
    .notification-unread {
        background-color: #eff6ff;
        border-left: 4px solid #3b82f6;
    }
    .notification-read {
        background-color: #ffffff;
        border-left: 4px solid #e5e7eb;
    }
    .notification-type-info { border-left-color: #3b82f6; }
    .notification-type-success { border-left-color: #10b981; }
    .notification-type-warning { border-left-color: #f59e0b; }
    .notification-type-error { border-left-color: #ef4444; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <!-- Başlık -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Bildirimler</h1>
                <p class="mt-2 text-gray-600">Panel içi bildirimlerinizi görüntüleyin ve yönetin</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="markAllAsRead()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                    <i class="fas fa-check-double mr-2"></i>
                    Tümünü Okundu İşaretle
                </button>
                <a href="<?= base_url('profile') ?>" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Geri Dön
                </a>
            </div>
        </div>
    </div>

    <!-- Bildirim Filtreleri -->
    <div class="mb-6 bg-white shadow rounded-lg p-4">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Filtrele:</label>
                <select id="typeFilter" class="border-gray-300 rounded-md text-sm focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Tüm Türler</option>
                    <option value="info">Bilgi</option>
                    <option value="success">Başarı</option>
                    <option value="warning">Uyarı</option>
                    <option value="error">Hata</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <select id="statusFilter" class="border-gray-300 rounded-md text-sm focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Tüm Durumlar</option>
                    <option value="unread">Okunmamış</option>
                    <option value="read">Okunmuş</option>
                </select>
            </div>
            <button onclick="clearFilters()" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-times mr-1"></i>
                Filtreleri Temizle
            </button>
        </div>
    </div>

    <!-- Bildirimler Listesi -->
    <div class="space-y-4" id="notificationsList">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
            <div class="notification-item notification-<?= $notification['is_read'] ? 'read' : 'unread' ?> notification-type-<?= $notification['type'] ?> rounded-lg shadow-sm border p-4"
                 data-id="<?= $notification['id'] ?>" 
                 data-type="<?= $notification['type'] ?>" 
                 data-status="<?= $notification['is_read'] ? 'read' : 'unread' ?>">
                
                <div class="flex items-start space-x-4">
                    <!-- İkon -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    <?php 
                                    $iconColors = [
                                        'info' => 'bg-blue-100 text-blue-600',
                                        'success' => 'bg-green-100 text-green-600',
                                        'warning' => 'bg-yellow-100 text-yellow-600',
                                        'error' => 'bg-red-100 text-red-600'
                                    ];
                                    echo $iconColors[$notification['type']] ?? 'bg-gray-100 text-gray-600';
                                    ?>">
                            <?php
                            $icons = [
                                'info' => 'fas fa-info-circle',
                                'success' => 'fas fa-check-circle',
                                'warning' => 'fas fa-exclamation-triangle',
                                'error' => 'fas fa-times-circle'
                            ];
                            ?>
                            <i class="<?= $icons[$notification['type']] ?? 'fas fa-bell' ?>"></i>
                        </div>
                    </div>

                    <!-- İçerik -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-900 truncate">
                                <?= esc($notification['title']) ?>
                                <?php if (!$notification['is_read']): ?>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Yeni
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500">
                                    <?= timeAgo($notification['created_at']) ?>
                                </span>
                                <?php if (!$notification['is_read']): ?>
                                    <button onclick="markAsRead(<?= $notification['id'] ?>)" 
                                            class="text-xs text-blue-600 hover:text-blue-800">
                                        Okundu İşaretle
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <p class="mt-1 text-sm text-gray-600">
                            <?= esc($notification['message']) ?>
                        </p>

                        <?php if ($notification['action_type'] && $notification['action_id']): ?>
                            <div class="mt-2">
                                <a href="<?= getActionUrl($notification['action_type'], $notification['action_id']) ?>" 
                                   class="text-xs text-pink-600 hover:text-pink-800 font-medium">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    Detayları Görüntüle
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($notification['read_at']): ?>
                            <div class="mt-2 text-xs text-gray-400">
                                Okunma: <?= date('d.m.Y H:i', strtotime($notification['read_at'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bell-slash text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz bildirim yok</h3>
                <p class="text-gray-500">Yeni bildirimler burada görünecek</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sayfalama (gelecekte eklenebilir) -->
    <?php if (count($notifications) >= 50): ?>
    <div class="mt-8 flex justify-center">
        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors">
            Daha Fazla Yükle
        </button>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Bildirimi okundu olarak işaretle
    function markAsRead(notificationId) {
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
                const notificationElement = document.querySelector(`[data-id="${notificationId}"]`);
                if (notificationElement) {
                    notificationElement.classList.remove('notification-unread');
                    notificationElement.classList.add('notification-read');
                    notificationElement.setAttribute('data-status', 'read');
                    
                    // "Yeni" etiketini kaldır
                    const newBadge = notificationElement.querySelector('.bg-blue-100.text-blue-800');
                    if (newBadge) {
                        newBadge.remove();
                    }
                    
                    // "Okundu İşaretle" butonunu kaldır
                    const markButton = notificationElement.querySelector('button[onclick*="markAsRead"]');
                    if (markButton) {
                        markButton.remove();
                    }
                    
                    // Okunma tarihini ekle
                    const contentDiv = notificationElement.querySelector('.flex-1.min-w-0');
                    const readAtDiv = document.createElement('div');
                    readAtDiv.className = 'mt-2 text-xs text-gray-400';
                    readAtDiv.textContent = `Okunma: ${new Date().toLocaleString('tr-TR')}`;
                    contentDiv.appendChild(readAtDiv);
                }
            } else {
                alert('Bildirim güncellenirken bir hata oluştu.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu.');
        });
    }

    // Tüm bildirimleri okundu olarak işaretle
    function markAllAsRead() {
        if (!confirm('Tüm bildirimler okundu olarak işaretlenecek. Emin misiniz?')) {
            return;
        }

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
                location.reload();
            } else {
                alert('Bildirimler güncellenirken bir hata oluştu.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu.');
        });
    }

    // Filtreleme
    function filterNotifications() {
        const typeFilter = document.getElementById('typeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const notifications = document.querySelectorAll('.notification-item');

        notifications.forEach(notification => {
            const type = notification.getAttribute('data-type');
            const status = notification.getAttribute('data-status');
            
            let showType = !typeFilter || type === typeFilter;
            let showStatus = !statusFilter || status === statusFilter;
            
            if (showType && showStatus) {
                notification.style.display = 'block';
            } else {
                notification.style.display = 'none';
            }
        });
    }

    // Filtreleri temizle
    function clearFilters() {
        document.getElementById('typeFilter').value = '';
        document.getElementById('statusFilter').value = '';
        filterNotifications();
    }

    // Event listener'lar
    document.getElementById('typeFilter').addEventListener('change', filterNotifications);
    document.getElementById('statusFilter').addEventListener('change', filterNotifications);
</script>
<?= $this->endSection() ?>

<?php
// Helper fonksiyonlar
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Az önce';
    if ($time < 3600) return floor($time/60) . ' dakika önce';
    if ($time < 86400) return floor($time/3600) . ' saat önce';
    if ($time < 2592000) return floor($time/86400) . ' gün önce';
    
    return date('d.m.Y', strtotime($datetime));
}

function getActionUrl($actionType, $actionId) {
    $urls = [
        'appointment' => base_url("calendar/edit/{$actionId}"),
        'customer' => base_url("admin/customers/view/{$actionId}"),
        'package' => base_url("admin/packages/view/{$actionId}"),
        'payment' => base_url("payments/show/{$actionId}"),
        'user' => base_url("admin/users/view/{$actionId}")
    ];
    
    return $urls[$actionType] ?? '#';
}
?>