<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Mesaj Şablonları</h1>
        <div class="flex space-x-2">
            <a href="/notifications" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Geri Dön
            </a>
            <a href="/notifications/templates/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>Yeni Şablon
            </a>
            <form action="/notifications/templates/create-defaults" method="POST" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-magic mr-2"></i>Varsayılan Şablonlar
                </button>
            </form>
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

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sol Taraf - Şablon Listesi -->
        <div class="lg:col-span-3">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Mevcut Şablonlar</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    <?php if (empty($templates)): ?>
                        <div class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-file-alt text-4xl mb-4"></i>
                            <p>Henüz mesaj şablonu oluşturulmamış.</p>
                            <p class="text-sm mt-2">Varsayılan şablonları oluşturmak için yukarıdaki butonu kullanabilirsiniz.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($templates as $template): ?>
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <?php if ($template['template_type'] === 'whatsapp'): ?>
                                                    <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-sms text-blue-500 text-xl"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900 truncate">
                                                    <?= esc($template['template_name']) ?>
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    Anahtar: <code class="bg-gray-100 px-1 rounded"><?= esc($template['template_key']) ?></code>
                                                </p>
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                                    <?= esc(substr($template['template_content'], 0, 100)) ?>
                                                    <?= strlen($template['template_content']) > 100 ? '...' : '' ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $template['template_type'] === 'whatsapp' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                                            <?= $template['template_type'] === 'whatsapp' ? 'WhatsApp' : 'SMS' ?>
                                        </span>
                                        <a href="/notifications/templates/edit/<?= $template['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteTemplate(<?= $template['id'] ?>)" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sağ Taraf - Kullanılabilir Değişkenler -->
        <div>
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kullanılabilir Değişkenler</h3>
                
                <div class="space-y-2 text-sm">
                    <?php foreach ($availableVariables as $key => $description): ?>
                        <div class="flex flex-col">
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">
                                {<?= $key ?>}
                            </code>
                            <span class="text-gray-600 text-xs mt-1"><?= $description ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Örnek Kullanım:</h4>
                    <p class="text-xs text-blue-800">
                        Sayın {musteri_adi}, yarın saat {randevu_saati} randevunuz bulunmaktadır. {salon_adi}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Silme Onay Modal'ı -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Şablonu Sil</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Bu şablonu silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmDelete" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600">
                    Sil
                </button>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600">
                    İptal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let templateToDelete = null;

function deleteTemplate(templateId) {
    templateToDelete = templateId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    templateToDelete = null;
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (templateToDelete) {
        fetch(`/notifications/templates/delete/${templateToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            alert('Bir hata oluştu: ' + error.message);
        });
    }
    closeDeleteModal();
});

// Modal dışına tıklandığında kapat
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
<?= $this->endSection() ?>