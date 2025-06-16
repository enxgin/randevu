<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-cash-register mr-3 text-green-600"></i>
                    Ödeme Al
                </h1>
                <a href="/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Geri Dön
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ödeme Formu -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ödeme Bilgileri</h3>
                    </div>
                    
                    <form action="/payments" method="POST" class="p-6 space-y-6">
                        <?= csrf_field() ?>
                        
                        <!-- Müşteri Seçimi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Müşteri *</label>
                            <div class="mt-1 relative">
                                <?php if ($customer): ?>
                                <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                                <div class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900">
                                    <?= esc($customer['first_name'] . ' ' . $customer['last_name']) ?>
                                    <span class="text-gray-500 ml-2">(<?= esc($customer['phone']) ?>)</span>
                                </div>
                                <?php else: ?>
                                <!-- Müşteri Arama Alanları -->
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <input type="text" id="search_name" placeholder="Ad veya Soyad ara..."
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <input type="text" id="search_phone" placeholder="Telefon numarası ara..."
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>
                                    
                                    <!-- Seçilen Müşteri Bilgisi -->
                                    <div id="selected_customer" class="hidden">
                                        <input type="hidden" name="customer_id" id="customer_id" required>
                                        <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-md">
                                            <div>
                                                <span id="selected_customer_name" class="font-medium text-blue-900"></span>
                                                <span id="selected_customer_phone" class="text-blue-700 ml-2"></span>
                                            </div>
                                            <button type="button" id="clear_customer" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Arama Sonuçları -->
                                    <div id="search_results" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                        <!-- Sonuçlar buraya gelecek -->
                                    </div>
                                    
                                    <!-- Yükleniyor Göstergesi -->
                                    <div id="search_loading" class="hidden text-center py-2">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                        <span class="text-gray-500 ml-2">Aranıyor...</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Randevu Bilgisi (varsa) -->
                        <?php if ($appointment): ?>
                        <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-check text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Randevu Bilgileri</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p><strong>Hizmet:</strong> <?= esc($appointment['service_name']) ?></p>
                                        <p><strong>Tarih:</strong> <?= date('d.m.Y H:i', strtotime($appointment['start_time'])) ?></p>
                                        <p><strong>Toplam Tutar:</strong> <?= number_format($appointment['price'], 2) ?> ₺</p>
                                        <?php if ($appointment['total_paid'] > 0): ?>
                                        <p><strong>Ödenen:</strong> <?= number_format($appointment['total_paid'], 2) ?> ₺</p>
                                        <p><strong>Kalan:</strong> <?= number_format($appointment['remaining_amount'], 2) ?> ₺</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Ödeme Tutarı -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Ödeme Tutarı (₺) *</label>
                            <div class="mt-1">
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01" 
                                       value="<?= $appointment['remaining_amount'] ?? '' ?>" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Ödeme Türü -->
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-gray-700">Ödeme Türü *</label>
                            <div class="mt-1">
                                <select name="payment_type" id="payment_type" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Seçiniz...</option>
                                    <option value="cash">Nakit</option>
                                    <option value="credit_card">Kredi Kartı</option>
                                    <option value="bank_transfer">Havale/EFT</option>
                                    <option value="gift_card">Hediye Çeki</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kredi Kartı Detayları -->
                        <div id="credit_card_details" class="hidden space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="card_last_four" class="block text-sm font-medium text-gray-700">Kartın Son 4 Hanesi</label>
                                    <input type="text" name="card_last_four" id="card_last_four" maxlength="4" pattern="[0-9]{4}"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="card_type" class="block text-sm font-medium text-gray-700">Kart Türü</label>
                                    <select name="card_type" id="card_type" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Seçiniz...</option>
                                        <option value="visa">Visa</option>
                                        <option value="mastercard">Mastercard</option>
                                        <option value="amex">American Express</option>
                                        <option value="other">Diğer</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="transaction_id" class="block text-sm font-medium text-gray-700">İşlem Referans No</label>
                                <input type="text" name="transaction_id" id="transaction_id"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Havale/EFT Detayları -->
                        <div id="bank_transfer_details" class="hidden space-y-4">
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700">Banka Adı</label>
                                <input type="text" name="bank_name" id="bank_name"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700">Referans Numarası</label>
                                <input type="text" name="reference_number" id="reference_number"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Notlar -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notlar</label>
                            <div class="mt-1">
                                <textarea name="notes" id="notes" rows="3" 
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Ödeme ile ilgili notlar..."></textarea>
                            </div>
                        </div>

                        <!-- Butonlar -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                İptal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-check mr-2"></i>
                                Ödemeyi Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Yardım ve Bilgiler -->
            <div class="space-y-6">
                <!-- Ödeme Türleri Bilgisi -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ödeme Türleri</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-money-bill-wave text-green-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Nakit</h4>
                                <p class="text-sm text-gray-500">Kasaya direkt eklenir</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-credit-card text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Kredi Kartı</h4>
                                <p class="text-sm text-gray-500">POS cihazı ile ödeme</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-university text-purple-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Havale/EFT</h4>
                                <p class="text-sm text-gray-500">Banka transferi</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-gift text-pink-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Hediye Çeki</h4>
                                <p class="text-sm text-gray-500">Hediye çeki kullanımı</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hızlı İşlemler -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Hızlı İşlemler</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="/payments/debtors" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            Borçlu Müşteriler
                        </a>
                        <a href="/payments/credits" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-piggy-bank text-green-600 mr-2"></i>
                            Kredi Bakiyesi Olan Müşteriler
                        </a>
                        <a href="/cash" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-cash-register text-green-600 mr-2"></i>
                            Kasa Yönetimi
                        </a>
                        <a href="/payments/reports" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                            Ödeme Raporları
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentTypeSelect = document.getElementById('payment_type');
    const creditCardDetails = document.getElementById('credit_card_details');
    const bankTransferDetails = document.getElementById('bank_transfer_details');

    paymentTypeSelect.addEventListener('change', function() {
        // Tüm detay alanlarını gizle
        creditCardDetails.classList.add('hidden');
        bankTransferDetails.classList.add('hidden');

        // Seçilen türe göre ilgili alanı göster
        if (this.value === 'credit_card') {
            creditCardDetails.classList.remove('hidden');
        } else if (this.value === 'bank_transfer') {
            bankTransferDetails.classList.remove('hidden');
        }
    });

    // Müşteri arama sistemi (eğer müşteri önceden seçilmemişse)
    <?php if (!$customer): ?>
    const searchName = document.getElementById('search_name');
    const searchPhone = document.getElementById('search_phone');
    const searchResults = document.getElementById('search_results');
    const searchLoading = document.getElementById('search_loading');
    const selectedCustomer = document.getElementById('selected_customer');
    const customerIdInput = document.getElementById('customer_id');
    const selectedCustomerName = document.getElementById('selected_customer_name');
    const selectedCustomerPhone = document.getElementById('selected_customer_phone');
    const clearCustomerBtn = document.getElementById('clear_customer');

    let searchTimeout;

    // Arama fonksiyonu
    function searchCustomers() {
        const name = searchName.value.trim();
        const phone = searchPhone.value.trim();

        // En az 2 karakter gerekli
        if (name.length < 2 && phone.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        // Önceki timeout'u temizle
        clearTimeout(searchTimeout);

        // 300ms bekle (debounce)
        searchTimeout = setTimeout(() => {
            performSearch(name, phone);
        }, 300);
    }

    // AJAX arama isteği
    function performSearch(name, phone) {
        searchLoading.classList.remove('hidden');
        searchResults.classList.add('hidden');

        const params = new URLSearchParams();
        if (name) params.append('name', name);
        if (phone) params.append('phone', phone);

        fetch(`/payments/search-customers?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                displaySearchResults(data.customers || []);
            })
            .catch(error => {
                console.error('Arama hatası:', error);
                searchLoading.classList.add('hidden');
                searchResults.classList.add('hidden');
            });
    }

    // Arama sonuçlarını göster
    function displaySearchResults(customers) {
        if (customers.length === 0) {
            searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Müşteri bulunamadı</div>';
        } else {
            searchResults.innerHTML = customers.map(customer => `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 customer-result"
                     data-id="${customer.id}"
                     data-name="${customer.first_name} ${customer.last_name}"
                     data-phone="${customer.phone}">
                    <div class="font-medium text-gray-900">${customer.first_name} ${customer.last_name}</div>
                    <div class="text-sm text-gray-500">${customer.phone}</div>
                    ${customer.email ? `<div class="text-sm text-gray-400">${customer.email}</div>` : ''}
                </div>
            `).join('');

            // Sonuç tıklama olaylarını ekle
            searchResults.querySelectorAll('.customer-result').forEach(result => {
                result.addEventListener('click', function() {
                    selectCustomer(
                        this.dataset.id,
                        this.dataset.name,
                        this.dataset.phone
                    );
                });
            });
        }

        searchResults.classList.remove('hidden');
    }

    // Müşteri seç
    function selectCustomer(id, name, phone) {
        customerIdInput.value = id;
        selectedCustomerName.textContent = name;
        selectedCustomerPhone.textContent = `(${phone})`;
        
        // Arama alanlarını temizle
        searchName.value = '';
        searchPhone.value = '';
        
        // Görünümleri güncelle
        searchResults.classList.add('hidden');
        selectedCustomer.classList.remove('hidden');
    }

    // Müşteri seçimini temizle
    clearCustomerBtn.addEventListener('click', function() {
        customerIdInput.value = '';
        selectedCustomerName.textContent = '';
        selectedCustomerPhone.textContent = '';
        selectedCustomer.classList.add('hidden');
        searchName.focus();
    });

    // Arama alanları için event listener'lar
    searchName.addEventListener('input', searchCustomers);
    searchPhone.addEventListener('input', searchCustomers);

    // Dışarı tıklandığında sonuçları gizle
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#search_results') &&
            !e.target.closest('#search_name') &&
            !e.target.closest('#search_phone')) {
            searchResults.classList.add('hidden');
        }
    });

    // Enter tuşu ile arama
    searchName.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstResult = searchResults.querySelector('.customer-result');
            if (firstResult) {
                firstResult.click();
            }
        }
    });

    searchPhone.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstResult = searchResults.querySelector('.customer-result');
            if (firstResult) {
                firstResult.click();
            }
        }
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>