<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tema Test</title>
    <link rel="stylesheet" href="/assets/css/output.css">
    <style>
        .dark .bg-white {
            background-color: #1f2937 !important;
        }
        .dark .text-gray-900 {
            color: #f9fafb !important;
        }
        .dark .text-gray-700 {
            color: #d1d5db !important;
        }
    </style>
</head>
<body class="<?= session('theme_mode') === 'dark' ? 'dark bg-gray-900' : 'bg-gray-50' ?>">
    <div class="min-h-screen p-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Tema Test Sayfası</h1>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Mevcut Tema Bilgileri</h2>
                <p class="text-gray-700 dark:text-gray-300">Session Theme Mode: <strong><?= session('theme_mode') ?: 'null' ?></strong></p>
                <p class="text-gray-700 dark:text-gray-300">User ID: <strong><?= session('user_id') ?: 'null' ?></strong></p>
                <p class="text-gray-700 dark:text-gray-300">Body Class: <strong><?= session('theme_mode') === 'dark' ? 'dark bg-gray-900' : 'bg-gray-50' ?></strong></p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Tema Değiştirme Testi</h2>
                <form action="/profile/update-settings" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tema Seçin:</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="theme_mode" value="light"
                                           <?= session('theme_mode') === 'light' ? 'checked' : '' ?>
                                           class="mr-2">
                                    <span class="text-gray-700 dark:text-gray-300">Açık Tema</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="theme_mode" value="dark"
                                           <?= session('theme_mode') === 'dark' ? 'checked' : '' ?>
                                           class="mr-2">
                                    <span class="text-gray-700 dark:text-gray-300">Koyu Tema</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Diğer ayarlar -->
                        <input type="hidden" name="notifications_enabled" value="1">
                        <input type="hidden" name="notification_sound" value="1">
                        <input type="hidden" name="notification_desktop" value="1">
                        <input type="hidden" name="notification_email" value="1">
                        
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Temayı Değiştir
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Test Bildirimi</h2>
                <button onclick="testNotification()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Test Bildirimi Gönder
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Sayfa yüklendiğinde tema değişikliği dinleyicilerini ekle
        document.addEventListener('DOMContentLoaded', function() {
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
        });

        function testNotification() {
            // CSRF token'ı form'dan al
            const form = document.querySelector('form');
            const csrfToken = form.querySelector('input[name="<?= csrf_token() ?>"]')?.value;
            
            if (!csrfToken) {
                alert('CSRF token bulunamadı!');
                return;
            }
            
            fetch('/profile/send-test-notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `<?= csrf_token() ?>=${csrfToken}`
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    alert('Test bildirimi başarıyla gönderildi: ' + data.message);
                } else {
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bağlantı hatası: ' + error.message);
            });
        }
    </script>
</body>
</html>