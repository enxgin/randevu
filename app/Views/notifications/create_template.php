<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Yeni Mesaj Şablonu</h1>
        <a href="/notifications/templates" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Geri Dön
        </a>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Taraf - Form -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="/notifications/templates/save" method="POST">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şablon Adı *</label>
                            <input type="text" name="template_name" required
                                   value="<?= old('template_name') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Örn: Randevu Hatırlatma">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şablon Anahtarı *</label>
                            <input type="text" name="template_key" required
                                   value="<?= old('template_key') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="appointment_reminder_24h">
                            <p class="text-xs text-gray-500 mt-1">Sadece harf, rakam ve alt çizgi kullanın</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj Türü *</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="template_type" value="sms" 
                                       <?= old('template_type', 'sms') === 'sms' ? 'checked' : '' ?>
                                       class="form-radio h-4 w-4 text-blue-600">
                                <span class="ml-2 text-gray-700">
                                    <i class="fas fa-sms text-blue-500 mr-1"></i>SMS
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="template_type" value="whatsapp"
                                       <?= old('template_type') === 'whatsapp' ? 'checked' : '' ?>
                                       class="form-radio h-4 w-4 text-green-600">
                                <span class="ml-2 text-gray-700">
                                    <i class="fab fa-whatsapp text-green-500 mr-1"></i>WhatsApp
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj İçeriği *</label>
                        <textarea name="template_content" required rows="6"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Sayın {musteri_adi}, yarın saat {randevu_saati} randevunuz bulunmaktadır. {salon_adi}"><?= old('template_content') ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">Değişkenler için sağdaki listeden yararlanabilirsiniz</p>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="/notifications/templates" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            İptal
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-save mr-2"></i>Şablonu Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sağ Taraf - Yardım -->
        <div class="space-y-6">
            <!-- Kullanılabilir Değişkenler -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kullanılabilir Değişkenler</h3>
                
                <div class="space-y-2 text-sm">
                    <?php foreach ($availableVariables as $key => $description): ?>
                        <div class="flex flex-col cursor-pointer hover:bg-gray-50 p-2 rounded" 
                             onclick="insertVariable('{<?= $key ?>}')">
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">
                                {<?= $key ?>}
                            </code>
                            <span class="text-gray-600 text-xs mt-1"><?= $description ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <p class="text-xs text-gray-500 mt-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Değişkenlere tıklayarak mesaj içeriğine ekleyebilirsiniz.
                </p>
            </div>

            <!-- Örnek Şablonlar -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Örnek Şablonlar</h3>
                
                <div class="space-y-4 text-sm">
                    <div class="border-l-4 border-blue-500 pl-3">
                        <h4 class="font-medium text-gray-900">Randevu Hatırlatma</h4>
                        <p class="text-gray-600 text-xs mt-1">
                            Sayın {musteri_adi}, yarın saat {randevu_saati} randevunuz bulunmaktadır. {salon_adi}
                        </p>
                        <button onclick="useTemplate(this.previousElementSibling.textContent)" 
                                class="text-blue-600 text-xs hover:underline mt-1">
                            Bu şablonu kullan
                        </button>
                    </div>

                    <div class="border-l-4 border-green-500 pl-3">
                        <h4 class="font-medium text-gray-900">Paket Uyarısı</h4>
                        <p class="text-gray-600 text-xs mt-1">
                            Sayın {musteri_adi}, {paket_adi} paketinizde son kullanım hakkınız kalmıştır. {salon_adi}
                        </p>
                        <button onclick="useTemplate(this.previousElementSibling.textContent)" 
                                class="text-green-600 text-xs hover:underline mt-1">
                            Bu şablonu kullan
                        </button>
                    </div>

                    <div class="border-l-4 border-yellow-500 pl-3">
                        <h4 class="font-medium text-gray-900">Doğum Günü</h4>
                        <p class="text-gray-600 text-xs mt-1">
                            Sayın {musteri_adi}, doğum gününüz kutlu olsun! Size özel indirimlerimiz için bizi arayın. {salon_adi}
                        </p>
                        <button onclick="useTemplate(this.previousElementSibling.textContent)" 
                                class="text-yellow-600 text-xs hover:underline mt-1">
                            Bu şablonu kullan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function insertVariable(variable) {
    const textarea = document.querySelector('textarea[name="template_content"]');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(textarea.selectionEnd);
    
    textarea.value = textBefore + variable + textAfter;
    textarea.focus();
    textarea.setSelectionRange(cursorPos + variable.length, cursorPos + variable.length);
}

function useTemplate(templateText) {
    document.querySelector('textarea[name="template_content"]').value = templateText.trim();
}

// Şablon anahtarını otomatik oluştur
document.querySelector('input[name="template_name"]').addEventListener('input', function() {
    const name = this.value;
    const key = name.toLowerCase()
                   .replace(/[^a-z0-9\s]/g, '')
                   .replace(/\s+/g, '_')
                   .substring(0, 50);
    
    document.querySelector('input[name="template_key"]').value = key;
});
</script>
<?= $this->endSection() ?>