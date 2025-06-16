<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Mesaj Şablonu Düzenle</h1>
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
                <form action="/notifications/templates/update/<?= $template['id'] ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şablon Adı *</label>
                            <input type="text" name="template_name" required
                                   value="<?= old('template_name', $template['template_name']) ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Örn: Randevu Hatırlatma">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şablon Anahtarı</label>
                            <input type="text" readonly
                                   value="<?= $template['template_key'] ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed"
                                   title="Şablon anahtarı değiştirilemez">
                            <p class="text-xs text-gray-500 mt-1">Şablon anahtarı değiştirilemez</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj Türü *</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="template_type" value="sms" 
                                       <?= old('template_type', $template['template_type']) === 'sms' ? 'checked' : '' ?>
                                       class="form-radio h-4 w-4 text-blue-600">
                                <span class="ml-2 text-gray-700">
                                    <i class="fas fa-sms text-blue-500 mr-1"></i>SMS
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="template_type" value="whatsapp"
                                       <?= old('template_type', $template['template_type']) === 'whatsapp' ? 'checked' : '' ?>
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
                                  placeholder="Sayın {musteri_adi}, yarın saat {randevu_saati} randevunuz bulunmaktadır. {salon_adi}"><?= old('template_content', $template['template_content']) ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">Değişkenler için sağdaki listeden yararlanabilirsiniz</p>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="/notifications/templates" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            İptal
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-save mr-2"></i>Değişiklikleri Kaydet
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

            <!-- Şablon Bilgileri -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Şablon Bilgileri</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Oluşturulma:</span>
                        <span class="font-medium"><?= date('d.m.Y H:i', strtotime($template['created_at'])) ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Son Güncelleme:</span>
                        <span class="font-medium"><?= date('d.m.Y H:i', strtotime($template['updated_at'])) ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Durum:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $template['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $template['is_active'] ? 'Aktif' : 'Pasif' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Önizleme -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Önizleme</h3>
                
                <div id="preview" class="bg-gray-50 p-3 rounded text-sm border">
                    <div class="text-gray-500 italic">Mesaj içeriğini değiştirdikçe önizleme burada görünecek...</div>
                </div>
                
                <button onclick="updatePreview()" class="mt-2 text-blue-600 text-xs hover:underline">
                    Önizlemeyi Güncelle
                </button>
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
    updatePreview();
}

function updatePreview() {
    const content = document.querySelector('textarea[name="template_content"]').value;
    const preview = document.getElementById('preview');
    
    // Örnek değişkenlerle değiştir
    const sampleData = {
        'musteri_adi': 'Ayşe Yılmaz',
        'musteri_telefon': '0532 123 45 67',
        'randevu_tarihi': '15.06.2025',
        'randevu_saati': '14:30',
        'hizmet_adi': 'Cilt Bakımı',
        'personel_adi': 'Elif Hanım',
        'salon_adi': 'Güzellik Salonu',
        'salon_telefon': '0212 123 45 67',
        'paket_adi': '10 Seans Lazer Paketi',
        'kalan_seans': '2',
        'kalan_dakika': '120'
    };
    
    let previewContent = content;
    for (const [key, value] of Object.entries(sampleData)) {
        previewContent = previewContent.replace(new RegExp(`{${key}}`, 'g'), value);
    }
    
    if (previewContent.trim()) {
        preview.innerHTML = `<div class="whitespace-pre-wrap">${previewContent}</div>`;
    } else {
        preview.innerHTML = '<div class="text-gray-500 italic">Mesaj içeriği boş...</div>';
    }
}

// Sayfa yüklendiğinde önizlemeyi güncelle
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
    
    // Textarea değiştiğinde önizlemeyi güncelle
    document.querySelector('textarea[name="template_content"]').addEventListener('input', updatePreview);
});
</script>
<?= $this->endSection() ?>