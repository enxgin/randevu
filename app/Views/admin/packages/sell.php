<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= $pageTitle ?></h1>
            <p class="text-gray-600 mt-1">Müşteriye paket satışı yapın</p>
        </div>
        <div class="flex space-x-3">
            <a href="/admin/packages" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Paket Listesi
            </a>
            <a href="/admin/packages/sales" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-chart-line mr-2"></i>
                Satış Raporu
            </a>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Kolon - Satış Formu -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Paket Satış Formu</h2>
                
                <form action="/admin/packages/sell" method="POST" id="sellForm">
                    <div class="space-y-6">
                        <!-- Müşteri Seçimi -->
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Müşteri *</label>
                            <select name="customer_id" id="customer_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="loadCustomerPackages()">
                                <option value="">Müşteri Seçin</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" <?= (isset($formData['customer_id']) && $formData['customer_id'] == $customer['id']) ? 'selected' : '' ?>>
                                        <?= esc($customer['first_name'] . ' ' . $customer['last_name']) ?> - <?= esc($customer['phone']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Paket Seçimi -->
                        <div>
                            <label for="package_id" class="block text-sm font-medium text-gray-700 mb-2">Paket *</label>
                            <select name="package_id" id="package_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="showPackageDetails()">
                                <option value="">Paket Seçin</option>
                                <?php foreach ($packages as $package): ?>
                                    <option value="<?= $package['id'] ?>" 
                                            data-type="<?= $package['type'] ?>"
                                            data-sessions="<?= $package['total_sessions'] ?>"
                                            data-minutes="<?= $package['total_minutes'] ?>"
                                            data-price="<?= $package['price'] ?>"
                                            data-validity="<?= $package['validity_months'] ?>"
                                            <?= (isset($formData['package_id']) && $formData['package_id'] == $package['id']) ? 'selected' : '' ?>>
                                        <?= esc($package['name']) ?> - ₺<?= number_format($package['price'], 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Satış Tarihi -->
                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">Satış Tarihi</label>
                            <input type="datetime-local" name="purchase_date" id="purchase_date" 
                                   value="<?= isset($formData['purchase_date']) ? $formData['purchase_date'] : date('Y-m-d\TH:i') ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Notlar -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Satış ile ilgili notlar..."><?= esc($formData['notes'] ?? '') ?></textarea>
                        </div>

                        <!-- Form Butonları -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="/admin/packages" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                İptal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Paketi Sat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sağ Kolon - Bilgi Panelleri -->
        <div class="space-y-6">
            <!-- Seçilen Paket Detayları -->
            <div id="packageDetails" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Paket Detayları</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paket Adı:</span>
                        <span id="packageName" class="font-medium text-gray-900"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tür:</span>
                        <span id="packageType" class="font-medium text-gray-900"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Miktar:</span>
                        <span id="packageAmount" class="font-medium text-gray-900"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fiyat:</span>
                        <span id="packagePrice" class="font-medium text-green-600 text-lg"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Geçerlilik:</span>
                        <span id="packageValidity" class="font-medium text-gray-900"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bitiş Tarihi:</span>
                        <span id="packageExpiry" class="font-medium text-gray-900"></span>
                    </div>
                </div>
            </div>

            <!-- Müşteri Mevcut Paketleri -->
            <div id="customerPackages" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Müşterinin Mevcut Paketleri</h3>
                <div id="customerPackagesList">
                    <!-- AJAX ile doldurulacak -->
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hızlı İşlemler</h3>
                <div class="space-y-3">
                    <a href="/admin/customers/create" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Yeni Müşteri Ekle
                    </a>
                    <a href="/admin/packages/create" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-box mr-2"></i>
                        Yeni Paket Oluştur
                    </a>
                    <a href="/admin/packages/sales" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>
                        Satış Raporları
                    </a>
                </div>
            </div>

            <!-- Satış İpuçları -->
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                <h4 class="font-medium text-blue-900 mb-2">💡 Satış İpuçları</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Müşterinin geçmiş paket kullanımını kontrol edin</li>
                    <li>• Paket geçerlilik süresini açıklayın</li>
                    <li>• Hangi hizmetlerde kullanılabileceğini belirtin</li>
                    <li>• Ödeme planı seçeneklerini sunun</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function showPackageDetails() {
    const packageSelect = document.getElementById('package_id');
    const selectedOption = packageSelect.options[packageSelect.selectedIndex];
    const detailsDiv = document.getElementById('packageDetails');
    
    if (selectedOption.value) {
        const type = selectedOption.dataset.type;
        const sessions = selectedOption.dataset.sessions;
        const minutes = selectedOption.dataset.minutes;
        const price = parseFloat(selectedOption.dataset.price);
        const validity = selectedOption.dataset.validity;
        
        document.getElementById('packageName').textContent = selectedOption.text.split(' - ')[0];
        document.getElementById('packageType').textContent = type === 'session' ? 'Adet Bazlı' : 'Dakika Bazlı';
        document.getElementById('packageAmount').textContent = type === 'session' ? 
            `${parseInt(sessions).toLocaleString()} Seans` : 
            `${parseInt(minutes).toLocaleString()} Dakika`;
        document.getElementById('packagePrice').textContent = `₺${price.toLocaleString('tr-TR', {minimumFractionDigits: 2})}`;
        document.getElementById('packageValidity').textContent = `${validity} Ay`;
        
        // Bitiş tarihini hesapla
        const purchaseDate = new Date(document.getElementById('purchase_date').value || new Date());
        const expiryDate = new Date(purchaseDate);
        expiryDate.setMonth(expiryDate.getMonth() + parseInt(validity));
        document.getElementById('packageExpiry').textContent = expiryDate.toLocaleDateString('tr-TR');
        
        detailsDiv.classList.remove('hidden');
    } else {
        detailsDiv.classList.add('hidden');
    }
}

function loadCustomerPackages() {
    const customerId = document.getElementById('customer_id').value;
    const packagesDiv = document.getElementById('customerPackages');
    const packagesList = document.getElementById('customerPackagesList');
    
    if (customerId) {
        fetch(`/admin/api/customer-packages/${customerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let html = '<div class="space-y-2">';
                    data.forEach(pkg => {
                        const remaining = pkg.type === 'session' ? 
                            `${pkg.remaining_sessions} seans` : 
                            `${pkg.remaining_minutes} dakika`;
                        const statusColor = pkg.status === 'active' ? 'green' : 
                                          pkg.status === 'expired' ? 'red' : 'gray';
                        
                        html += `
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">${pkg.name}</h4>
                                        <p class="text-sm text-gray-600">Kalan: ${remaining}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800">
                                        ${pkg.status === 'active' ? 'Aktif' : pkg.status === 'expired' ? 'Süresi Dolmuş' : 'Tamamlandı'}
                                    </span>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    packagesList.innerHTML = html;
                    packagesDiv.classList.remove('hidden');
                } else {
                    packagesList.innerHTML = '<p class="text-gray-500 text-center py-4">Bu müşterinin aktif paketi bulunmuyor</p>';
                    packagesDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                packagesList.innerHTML = '<p class="text-red-500 text-center py-4">Paketler yüklenirken hata oluştu</p>';
                packagesDiv.classList.remove('hidden');
            });
    } else {
        packagesDiv.classList.add('hidden');
    }
}

// Satış tarihi değiştiğinde paket detaylarını güncelle
document.getElementById('purchase_date').addEventListener('change', function() {
    if (document.getElementById('package_id').value) {
        showPackageDetails();
    }
});

// Sayfa yüklendiğinde seçili değerler varsa detayları göster
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('package_id').value) {
        showPackageDetails();
    }
    if (document.getElementById('customer_id').value) {
        loadCustomerPackages();
    }
});
</script>
<?= $this->endSection() ?>