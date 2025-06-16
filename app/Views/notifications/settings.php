<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Bildirim Ayarları</h1>
        <div class="flex space-x-2">
            <a href="/notifications/templates" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-file-alt mr-2"></i>Mesaj Şablonları
            </a>
            <a href="/notifications/messages" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-history mr-2"></i>Mesaj Geçmişi
            </a>
        </div>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= session('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= session('error') ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Taraf - Ayarlar Formu -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="/notifications/save-settings" method="POST">
                    <?= csrf_field() ?>

                    <!-- SMS Ayarları -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-sms text-blue-500 mr-2"></i>SMS Ayarları
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="sms_enabled" value="1" 
                                           <?= isset($settings['sms_enabled']) && $settings['sms_enabled'] == '1' ? 'checked' : '' ?>
                                           class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-gray-700">SMS Bildirimlerini Aktif Et</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SMS Sağlayıcısı</label>
                                <select name="sms_provider" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="netgsm" <?= isset($settings['sms_provider']) && $settings['sms_provider'] == 'netgsm' ? 'selected' : '' ?>>Netgsm</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gönderici Adı (Max 11 karakter)</label>
                                <input type="text" name="sms_sender_name" maxlength="11" 
                                       value="<?= $settings['sms_sender_name'] ?? '' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="SALON">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">API Kullanıcı Adı</label>
                                <input type="text" name="sms_api_key" 
                                       value="<?= $settings['sms_api_key'] ?? '' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Netgsm kullanıcı adınız">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">API Şifresi</label>
                                <input type="password" name="sms_api_secret" 
                                       value="<?= $settings['sms_api_secret'] ?? '' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Netgsm şifreniz">
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Ayarları -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fab fa-whatsapp text-green-500 mr-2"></i>WhatsApp Ayarları
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="whatsapp_enabled" value="1" 
                                           <?= isset($settings['whatsapp_enabled']) && $settings['whatsapp_enabled'] == '1' ? 'checked' : '' ?>
                                           class="form-checkbox h-5 w-5 text-green-600">
                                    <span class="ml-2 text-gray-700">WhatsApp Bildirimlerini Aktif Et</span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">WAHA API URL</label>
                                <input type="url" name="whatsapp_api_url" 
                                       value="<?= $settings['whatsapp_api_url'] ?? '' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="http://localhost:3000">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">API Token</label>
                                <input type="text" name="whatsapp_api_token" 
                                       value="<?= $settings['whatsapp_api_token'] ?? '' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="WAHA API token">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Session Adı</label>
                                <input type="text" name="whatsapp_session_name" 
                                       value="<?= $settings['whatsapp_session_name'] ?? 'default' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="default">
                            </div>
                        </div>
                    </div>

                    <!-- Kaydet Butonu -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            <i class="fas fa-save mr-2"></i>Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sağ Taraf - İstatistikler ve Test -->
        <div class="space-y-6">
            <!-- Mesaj İstatistikleri -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bu Ay Mesaj İstatistikleri</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Toplam Mesaj:</span>
                        <span class="font-semibold"><?= $messageStats['total'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Gönderilen:</span>
                        <span class="font-semibold text-green-600"><?= $messageStats['sent'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Başarısız:</span>
                        <span class="font-semibold text-red-600"><?= $messageStats['failed'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Bekleyen:</span>
                        <span class="font-semibold text-yellow-600"><?= $messageStats['pending'] ?? 0 ?></span>
                    </div>
                    <hr>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">SMS:</span>
                        <span class="font-semibold"><?= $messageStats['sms'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">WhatsApp:</span>
                        <span class="font-semibold"><?= $messageStats['whatsapp'] ?? 0 ?></span>
                    </div>
                </div>
            </div>

            <!-- Test Mesajı -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Mesajı Gönder</h3>
                
                <form id="testMessageForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefon Numarası</label>
                        <input type="tel" id="testPhone" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="05XX XXX XX XX">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj Türü</label>
                        <select id="testMessageType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-paper-plane mr-2"></i>Test Mesajı Gönder
                    </button>
                </form>
                
                <div id="testResult" class="mt-4 hidden"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('testMessageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const phone = document.getElementById('testPhone').value;
    const messageType = document.getElementById('testMessageType').value;
    const resultDiv = document.getElementById('testResult');
    
    if (!phone) {
        showTestResult('Telefon numarası gerekli', 'error');
        return;
    }
    
    // Loading göster
    showTestResult('Test mesajı gönderiliyor...', 'info');
    
    fetch('/notifications/send-test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `phone=${encodeURIComponent(phone)}&message_type=${messageType}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showTestResult(data.message, 'success');
        } else {
            showTestResult(data.message, 'error');
        }
    })
    .catch(error => {
        showTestResult('Bir hata oluştu: ' + error.message, 'error');
    });
});

function showTestResult(message, type) {
    const resultDiv = document.getElementById('testResult');
    let bgColor = 'bg-blue-100 border-blue-400 text-blue-700';
    
    if (type === 'success') {
        bgColor = 'bg-green-100 border-green-400 text-green-700';
    } else if (type === 'error') {
        bgColor = 'bg-red-100 border-red-400 text-red-700';
    }
    
    resultDiv.className = `border px-4 py-3 rounded ${bgColor}`;
    resultDiv.textContent = message;
    resultDiv.classList.remove('hidden');
}
</script>
<?= $this->endSection() ?>