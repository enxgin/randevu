<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bildirim Kuyruğu</h1>
            <p class="text-gray-600 mt-1">Planlanmış ve gönderilmiş mesajları görüntüleyin</p>
        </div>
        <a href="/notifications" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Bildirim Ayarları
        </a>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Bekleyen</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['pending'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Gönderildi</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['sent'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Başarısız</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['failed'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-list text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Toplam</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreleme -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Bekleyen</option>
                    <option value="sent" <?= $filters['status'] === 'sent' ? 'selected' : '' ?>>Gönderildi</option>
                    <option value="failed" <?= $filters['status'] === 'failed' ? 'selected' : '' ?>>Başarısız</option>
                    <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>İptal Edildi</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj Türü</label>
                <select name="message_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="sms" <?= $filters['message_type'] === 'sms' ? 'selected' : '' ?>>SMS</option>
                    <option value="whatsapp" <?= $filters['message_type'] === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                <input type="date" name="date_from" value="<?= $filters['date_from'] ?>" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
                <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-4 flex justify-end space-x-3">
                <a href="/notifications/queue" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition duration-200">
                    Temizle
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                    <i class="fas fa-filter mr-2"></i>Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Kuyruk Listesi -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Mesaj Kuyruğu</h3>
        </div>

        <?php if (!empty($queueMessages)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Müşteri
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tetikleyici
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mesaj Türü
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Planlanma Zamanı
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durum
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($queueMessages as $message): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($message['first_name'] . ' ' . $message['last_name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= esc($message['recipient_phone']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($message['trigger_name']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $message['message_type'] === 'sms' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= strtoupper($message['message_type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d.m.Y H:i', strtotime($message['scheduled_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'sent' => 'bg-green-100 text-green-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Bekleyen',
                                        'sent' => 'Gönderildi',
                                        'failed' => 'Başarısız',
                                        'cancelled' => 'İptal Edildi'
                                    ];
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClasses[$message['status']] ?>">
                                        <?= $statusLabels[$message['status']] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="showMessageDetails(<?= $message['id'] ?>)" 
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                        Detay
                                    </button>
                                    <?php if ($message['status'] === 'pending'): ?>
                                        <button onclick="cancelMessage(<?= $message['id'] ?>)" 
                                                class="text-red-600 hover:text-red-900">
                                            İptal
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Sayfalama -->
            <?php if ($pager): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500 text-lg">Henüz kuyrukta mesaj bulunmuyor.</p>
                <p class="text-gray-400 mt-2">Tetikleyici kuralları oluşturduğunuzda mesajlar burada görünecektir.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Mesaj Detay Modal -->
<div id="messageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Mesaj Detayları</h3>
            </div>
            <div id="messageContent" class="p-6">
                <!-- AJAX ile doldurulacak -->
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button onclick="closeMessageModal()" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition duration-200">
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showMessageDetails(id) {
    // Bu fonksiyon mesaj detaylarını AJAX ile yükleyecek
    document.getElementById('messageContent').innerHTML = `
        <div class="animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
            <div class="h-20 bg-gray-200 rounded mb-4"></div>
        </div>
    `;
    document.getElementById('messageModal').classList.remove('hidden');
    
    // Gerçek implementasyonda AJAX çağrısı yapılacak
    setTimeout(() => {
        document.getElementById('messageContent').innerHTML = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mesaj İçeriği:</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md">
                        <p class="text-sm text-gray-900">Örnek mesaj içeriği...</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Oluşturulma:</label>
                        <p class="text-sm text-gray-900">${new Date().toLocaleString('tr-TR')}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gönderilme:</label>
                        <p class="text-sm text-gray-900">-</p>
                    </div>
                </div>
            </div>
        `;
    }, 500);
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.add('hidden');
}

function cancelMessage(id) {
    if (confirm('Bu mesajı iptal etmek istediğinizden emin misiniz?')) {
        // AJAX çağrısı yapılacak
        alert('Mesaj iptal edildi (demo)');
        location.reload();
    }
}
</script>
<?= $this->endSection() ?>