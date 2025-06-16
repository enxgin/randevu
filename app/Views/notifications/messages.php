<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gönderilen Mesajlar</h1>
        <a href="/notifications" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Geri Dön
        </a>
    </div>

    <!-- Filtreleme -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form method="GET" action="/notifications/messages" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Bekleyen</option>
                    <option value="sent" <?= $filters['status'] === 'sent' ? 'selected' : '' ?>>Gönderilen</option>
                    <option value="failed" <?= $filters['status'] === 'failed' ? 'selected' : '' ?>>Başarısız</option>
                    <option value="delivered" <?= $filters['status'] === 'delivered' ? 'selected' : '' ?>>Teslim Edilen</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj Türü</label>
                <select name="message_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="sms" <?= $filters['message_type'] === 'sms' ? 'selected' : '' ?>>SMS</option>
                    <option value="whatsapp" <?= $filters['message_type'] === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tetikleyici</label>
                <select name="trigger_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="manual" <?= $filters['trigger_type'] === 'manual' ? 'selected' : '' ?>>Manuel</option>
                    <option value="auto" <?= $filters['trigger_type'] === 'auto' ? 'selected' : '' ?>>Otomatik</option>
                    <option value="test" <?= $filters['trigger_type'] === 'test' ? 'selected' : '' ?>>Test</option>
                    <option value="appointment_reminder" <?= $filters['trigger_type'] === 'appointment_reminder' ? 'selected' : '' ?>>Randevu Hatırlatma</option>
                    <option value="package_warning" <?= $filters['trigger_type'] === 'package_warning' ? 'selected' : '' ?>>Paket Uyarısı</option>
                    <option value="no_show" <?= $filters['trigger_type'] === 'no_show' ? 'selected' : '' ?>>Gelmedi</option>
                    <option value="birthday" <?= $filters['trigger_type'] === 'birthday' ? 'selected' : '' ?>>Doğum Günü</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                <input type="date" name="date_from" value="<?= $filters['date_from'] ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
                <input type="date" name="date_to" value="<?= $filters['date_to'] ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-5 flex justify-end space-x-2">
                <a href="/notifications/messages" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Temizle
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-search mr-2"></i>Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Mesaj Listesi -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Mesaj Geçmişi</h3>
        </div>

        <?php if (empty($messages)): ?>
            <div class="px-6 py-8 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <p>Henüz mesaj gönderilmemiş.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Müşteri
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Telefon
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tür
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tetikleyici
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durum
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tarih
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($messages as $message): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= esc($message['first_name'] . ' ' . $message['last_name']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($message['phone']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $message['message_type'] === 'whatsapp' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                                        <?php if ($message['message_type'] === 'whatsapp'): ?>
                                            <i class="fab fa-whatsapp mr-1"></i>WhatsApp
                                        <?php else: ?>
                                            <i class="fas fa-sms mr-1"></i>SMS
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($message['trigger_type']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'sent' => 'bg-green-100 text-green-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'delivered' => 'bg-blue-100 text-blue-800'
                                    ];
                                    $statusTexts = [
                                        'pending' => 'Bekleyen',
                                        'sent' => 'Gönderildi',
                                        'failed' => 'Başarısız',
                                        'delivered' => 'Teslim Edildi'
                                    ];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$message['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                        <?= $statusTexts[$message['status']] ?? $message['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d.m.Y H:i', strtotime($message['created_at'])) ?>
                                    </div>
                                    <?php if ($message['sent_at']): ?>
                                        <div class="text-xs text-gray-500">
                                            Gönderildi: <?= date('d.m.Y H:i', strtotime($message['sent_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="showMessageDetails(<?= $message['id'] ?>)" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pager): ?>
                <div class="px-6 py-4 bg-gray-50 border-t">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Mesaj Detay Modal'ı -->
<div id="messageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Mesaj Detayları</h3>
                <button onclick="closeMessageModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="messageDetails" class="space-y-4">
                <!-- Mesaj detayları buraya yüklenecek -->
            </div>
        </div>
    </div>
</div>

<script>
function showMessageDetails(messageId) {
    // Bu örnekte sadece basit bir modal gösteriyoruz
    // Gerçek uygulamada AJAX ile mesaj detaylarını çekebilirsiniz
    const modal = document.getElementById('messageModal');
    const details = document.getElementById('messageDetails');
    
    // Örnek detay gösterimi
    details.innerHTML = `
        <div class="bg-gray-50 p-4 rounded">
            <h4 class="font-medium mb-2">Mesaj İçeriği:</h4>
            <p class="text-sm text-gray-700">Mesaj detayları AJAX ile yüklenecek...</p>
        </div>
        <div class="bg-blue-50 p-4 rounded">
            <h4 class="font-medium mb-2">Sağlayıcı Yanıtı:</h4>
            <p class="text-sm text-gray-700">API yanıt detayları...</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.add('hidden');
}

// Modal dışına tıklandığında kapat
document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMessageModal();
    }
});
</script>
<?= $this->endSection() ?>