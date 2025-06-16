<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tetikleyici Düzenle</h1>
            <p class="text-gray-600 mt-1">Otomatik mesaj gönderim kuralını düzenleyin</p>
        </div>
        <a href="/notifications/triggers" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Geri Dön
        </a>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md">
        <form action="/notifications/triggers/update/<?= $trigger['id'] ?>" method="post" class="p-6">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tetikleyici Türü (Salt Okunur) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tetikleyici Türü
                    </label>
                    <div class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-600">
                        <?= esc($triggerTypes[$trigger['trigger_type']] ?? $trigger['trigger_type']) ?>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Tetikleyici türü değiştirilemez. Yeni bir tetikleyici oluşturun.</p>
                </div>

                <!-- Tetikleyici Adı -->
                <div class="md:col-span-2">
                    <label for="trigger_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tetikleyici Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="trigger_name" name="trigger_name" value="<?= old('trigger_name', $trigger['trigger_name']) ?>" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Örn: Randevu Hatırlatma - 24 Saat Önce" required>
                </div>

                <!-- Mesaj Şablonu -->
                <div>
                    <label for="message_template_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Mesaj Şablonu <span class="text-red-500">*</span>
                    </label>
                    <select id="message_template_id" name="message_template_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Şablon seçin...</option>
                        <?php foreach ($templates as $template): ?>
                            <option value="<?= $template['id'] ?>" <?= old('message_template_id', $trigger['message_template_id']) == $template['id'] ? 'selected' : '' ?>>
                                <?= esc($template['template_name']) ?> (<?= strtoupper($template['template_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Mesaj Türü -->
                <div>
                    <label for="message_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Mesaj Türü <span class="text-red-500">*</span>
                    </label>
                    <select id="message_type" name="message_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Mesaj türü seçin...</option>
                        <option value="sms" <?= old('message_type', $trigger['message_type']) === 'sms' ? 'selected' : '' ?>>SMS</option>
                        <option value="whatsapp" <?= old('message_type', $trigger['message_type']) === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                        <option value="both" <?= old('message_type', $trigger['message_type']) === 'both' ? 'selected' : '' ?>>Her İkisi</option>
                    </select>
                </div>

                <!-- Zamanlama Alanları -->
                <div id="timing_fields" class="md:col-span-2 <?= in_array($trigger['trigger_type'], ['appointment_reminder', 'no_show_notification']) ? '' : 'hidden' ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Önceden Gönder -->
                        <div id="before_field" class="<?= $trigger['trigger_type'] === 'appointment_reminder' ? '' : 'hidden' ?>">
                            <label for="send_before_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                Kaç Dakika Önce Gönderilsin?
                            </label>
                            <select id="send_before_minutes" name="send_before_minutes" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seçin...</option>
                                <option value="30" <?= old('send_before_minutes', $trigger['send_before_minutes']) == '30' ? 'selected' : '' ?>>30 Dakika</option>
                                <option value="60" <?= old('send_before_minutes', $trigger['send_before_minutes']) == '60' ? 'selected' : '' ?>>1 Saat</option>
                                <option value="120" <?= old('send_before_minutes', $trigger['send_before_minutes']) == '120' ? 'selected' : '' ?>>2 Saat</option>
                                <option value="360" <?= old('send_before_minutes', $trigger['send_before_minutes']) == '360' ? 'selected' : '' ?>>6 Saat</option>
                                <option value="720" <?= old('send_before_minutes', $trigger['send_before_minutes']) == '720' ? 'selected' : '' ?>>12 Saat</option>
                                <option value="1440" <?= old('send_before_minutes', $trigger['send_before_minutes']) == '1440' ? 'selected' : '' ?>>24 Saat</option>
                            </select>
                        </div>

                        <!-- Sonradan Gönder -->
                        <div id="after_field" class="<?= $trigger['trigger_type'] === 'no_show_notification' ? '' : 'hidden' ?>">
                            <label for="send_after_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                Kaç Dakika Sonra Gönderilsin?
                            </label>
                            <select id="send_after_minutes" name="send_after_minutes" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seçin...</option>
                                <option value="15" <?= old('send_after_minutes', $trigger['send_after_minutes']) == '15' ? 'selected' : '' ?>>15 Dakika</option>
                                <option value="30" <?= old('send_after_minutes', $trigger['send_after_minutes']) == '30' ? 'selected' : '' ?>>30 Dakika</option>
                                <option value="60" <?= old('send_after_minutes', $trigger['send_after_minutes']) == '60' ? 'selected' : '' ?>>1 Saat</option>
                                <option value="120" <?= old('send_after_minutes', $trigger['send_after_minutes']) == '120' ? 'selected' : '' ?>>2 Saat</option>
                                <option value="360" <?= old('send_after_minutes', $trigger['send_after_minutes']) == '360' ? 'selected' : '' ?>>6 Saat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Durum -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                               <?= old('is_active', $trigger['is_active']) ? 'checked' : '' ?>>
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Tetikleyici aktif
                        </label>
                    </div>
                </div>
            </div>

            <!-- Açıklama Kutuları -->
            <div id="description_boxes" class="mt-6 space-y-4">
                <!-- Randevu Hatırlatma Açıklaması -->
                <div id="appointment_reminder_desc" class="<?= $trigger['trigger_type'] === 'appointment_reminder' ? '' : 'hidden' ?> bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-2">Randevu Hatırlatma</h4>
                    <p class="text-blue-800 text-sm">
                        Bu tetikleyici, randevu saatinden belirtilen süre önce müşterilere otomatik hatırlatma mesajı gönderir. 
                        Randevu oluşturulduğunda veya güncellendiğinde otomatik olarak planlanır.
                    </p>
                </div>

                <!-- Paket Uyarısı Açıklaması -->
                <div id="package_warning_desc" class="<?= $trigger['trigger_type'] === 'package_warning' ? '' : 'hidden' ?> bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-medium text-yellow-900 mb-2">Paket Uyarısı</h4>
                    <p class="text-yellow-800 text-sm">
                        Bu tetikleyici, müşterinin paketinde son seans veya az dakika kaldığında uyarı mesajı gönderir. 
                        Randevu tamamlandığında otomatik olarak kontrol edilir.
                    </p>
                </div>

                <!-- No-Show Bildirimi Açıklaması -->
                <div id="no_show_notification_desc" class="<?= $trigger['trigger_type'] === 'no_show_notification' ? '' : 'hidden' ?> bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-medium text-red-900 mb-2">Gelmedi Bildirimi</h4>
                    <p class="text-red-800 text-sm">
                        Bu tetikleyici, randevu "Gelmedi" olarak işaretlendiğinde belirtilen süre sonra müşteriye bildirim gönderir. 
                        Müşteri ilişkilerini korumak için kullanılır.
                    </p>
                </div>

                <!-- Doğum Günü Kutlaması Açıklaması -->
                <div id="birthday_greeting_desc" class="<?= $trigger['trigger_type'] === 'birthday_greeting' ? '' : 'hidden' ?> bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-medium text-green-900 mb-2">Doğum Günü Kutlaması</h4>
                    <p class="text-green-800 text-sm">
                        Bu tetikleyici, müşterilerin doğum günlerinde otomatik kutlama mesajı gönderir. 
                        Her gün sabah kontrol edilir ve o gün doğum günü olan müşterilere mesaj gönderilir.
                    </p>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="/notifications/triggers" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition duration-200">
                    İptal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                    <i class="fas fa-save mr-2"></i>Tetikleyici Güncelle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Sayfa yüklendiğinde mevcut tetikleyici türüne göre alanları göster
document.addEventListener('DOMContentLoaded', function() {
    const triggerType = '<?= $trigger['trigger_type'] ?>';
    
    // Açıklama kutusunu göster
    const descBox = document.getElementById(triggerType + '_desc');
    if (descBox) {
        descBox.classList.remove('hidden');
    }
    
    // Zamanlama alanlarını göster
    if (triggerType === 'appointment_reminder') {
        document.getElementById('timing_fields').classList.remove('hidden');
        document.getElementById('before_field').classList.remove('hidden');
        document.getElementById('send_before_minutes').required = true;
    } else if (triggerType === 'no_show_notification') {
        document.getElementById('timing_fields').classList.remove('hidden');
        document.getElementById('after_field').classList.remove('hidden');
        document.getElementById('send_after_minutes').required = true;
    }
});
</script>
<?= $this->endSection() ?>