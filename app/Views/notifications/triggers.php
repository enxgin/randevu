<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık ve Butonlar -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bildirim Tetikleyicileri</h1>
            <p class="text-gray-600 mt-1">Otomatik mesaj gönderim kurallarını yönetin</p>
        </div>
        <div class="flex space-x-3">
            <form action="/notifications/triggers/create-defaults" method="post" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-magic mr-2"></i>Varsayılan Tetikleyiciler
                </button>
            </form>
            <a href="/notifications/triggers/create" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>Yeni Tetikleyici
            </a>
        </div>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Tetikleyici Türlerine Göre Gruplandırılmış Liste -->
    <div class="space-y-6">
        <?php 
        $groupedTriggers = [];
        foreach ($triggers as $trigger) {
            $groupedTriggers[$trigger['trigger_type']][] = $trigger;
        }
        
        $typeNames = [
            'appointment_reminder' => 'Randevu Hatırlatma',
            'package_warning' => 'Paket Uyarısı',
            'no_show_notification' => 'Gelmedi Bildirimi',
            'birthday_greeting' => 'Doğum Günü Kutlaması'
        ];
        
        $typeIcons = [
            'appointment_reminder' => 'fas fa-clock',
            'package_warning' => 'fas fa-exclamation-triangle',
            'no_show_notification' => 'fas fa-user-times',
            'birthday_greeting' => 'fas fa-birthday-cake'
        ];
        ?>

        <?php foreach ($typeNames as $type => $typeName): ?>
            <div class="bg-white rounded-lg shadow-md">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="<?= $typeIcons[$type] ?> mr-3 text-blue-600"></i>
                        <?= $typeName ?>
                    </h3>
                </div>
                
                <div class="p-6">
                    <?php if (isset($groupedTriggers[$type]) && !empty($groupedTriggers[$type])): ?>
                        <div class="space-y-4">
                            <?php foreach ($groupedTriggers[$type] as $trigger): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <h4 class="text-lg font-medium text-gray-900"><?= esc($trigger['trigger_name']) ?></h4>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full <?= $trigger['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                    <?= $trigger['is_active'] ? 'Aktif' : 'Pasif' ?>
                                                </span>
                                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                    <?= strtoupper($trigger['message_type']) ?>
                                                </span>
                                            </div>
                                            
                                            <div class="mt-2 text-sm text-gray-600">
                                                <?php if ($trigger['template_name']): ?>
                                                    <span class="inline-flex items-center">
                                                        <i class="fas fa-file-alt mr-1"></i>
                                                        Şablon: <?= esc($trigger['template_name']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if ($trigger['send_before_minutes']): ?>
                                                    <span class="ml-4 inline-flex items-center">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        <?= $trigger['send_before_minutes'] ?> dakika önce
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if ($trigger['send_after_minutes']): ?>
                                                    <span class="ml-4 inline-flex items-center">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        <?= $trigger['send_after_minutes'] ?> dakika sonra
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            <!-- Durum Değiştir -->
                                            <button onclick="toggleTrigger(<?= $trigger['id'] ?>)" 
                                                    class="px-3 py-1 text-sm rounded-md transition duration-200 <?= $trigger['is_active'] ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' ?>">
                                                <?= $trigger['is_active'] ? 'Pasif Yap' : 'Aktif Yap' ?>
                                            </button>
                                            
                                            <!-- Test Et -->
                                            <button onclick="testTrigger(<?= $trigger['id'] ?>)" 
                                                    class="px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-md transition duration-200">
                                                Test Et
                                            </button>
                                            
                                            <!-- Düzenle -->
                                            <a href="/notifications/triggers/edit/<?= $trigger['id'] ?>" 
                                               class="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-md transition duration-200">
                                                Düzenle
                                            </a>
                                            
                                            <!-- Sil -->
                                            <button onclick="deleteTrigger(<?= $trigger['id'] ?>)" 
                                                    class="px-3 py-1 text-sm bg-red-100 text-red-700 hover:bg-red-200 rounded-md transition duration-200">
                                                Sil
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="<?= $typeIcons[$type] ?> text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">Bu kategoride henüz tetikleyici bulunmuyor.</p>
                            <a href="/notifications/triggers/create" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                İlk tetikleyiciyi oluşturun
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Test Modal -->
<div id="testModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tetikleyici Test Et</h3>
            </div>
            <form id="testForm" class="p-6">
                <input type="hidden" id="testTriggerId" name="trigger_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Müşterisi</label>
                    <select id="testCustomerId" name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Müşteri seçin...</option>
                        <!-- AJAX ile doldurulacak -->
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeTestModal()" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition duration-200">
                        İptal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                        Test Gönder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tetikleyici durumunu değiştir
function toggleTrigger(id) {
    if (confirm('Tetikleyici durumunu değiştirmek istediğinizden emin misiniz?')) {
        fetch(`/notifications/triggers/toggle/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
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
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}

// Tetikleyici sil
function deleteTrigger(id) {
    if (confirm('Bu tetikleyiciyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        fetch(`/notifications/triggers/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
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
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}

// Test modal aç
function testTrigger(id) {
    document.getElementById('testTriggerId').value = id;
    document.getElementById('testModal').classList.remove('hidden');
    
    // Müşteri listesini yükle
    loadCustomers();
}

// Test modal kapat
function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
    document.getElementById('testForm').reset();
}

// Müşteri listesini yükle
function loadCustomers() {
    // Bu fonksiyon müşteri listesini AJAX ile yükleyecek
    // Şimdilik basit bir örnek
    const select = document.getElementById('testCustomerId');
    select.innerHTML = '<option value="">Yükleniyor...</option>';
    
    // Gerçek implementasyonda AJAX çağrısı yapılacak
    setTimeout(() => {
        select.innerHTML = `
            <option value="">Müşteri seçin...</option>
            <option value="1">Test Müşteri 1</option>
            <option value="2">Test Müşteri 2</option>
        `;
    }, 500);
}

// Test formu gönder
document.getElementById('testForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/notifications/triggers/test', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test mesajı başarıyla gönderildi!');
            closeTestModal();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
    });
});
</script>
<?= $this->endSection() ?>