<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-plus mr-3 text-blue-600"></i>
                    Manuel Kasa Hareketi
                </h1>
                <a href="/cash" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Geri Dön
                </a>
            </div>
            <p class="mt-2 text-sm text-gray-600">
                Kasaya manuel gelir veya gider hareketi ekleyiniz.
            </p>
        </div>

        <!-- Manuel Hareket Formu -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Hareket Bilgileri</h3>
            </div>
            
            <form action="/cash/add-movement" method="POST" class="p-6 space-y-6">
                <?= csrf_field() ?>
                
                <!-- Hareket Türü -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Hareket Türü *</label>
                    <div class="mt-1">
                        <select name="type" id="type" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seçiniz...</option>
                            <option value="income">Gelir (+)</option>
                            <option value="expense">Gider (-)</option>
                        </select>
                    </div>
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Kategori *</label>
                    <div class="mt-1">
                        <select name="category" id="category" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Önce hareket türünü seçiniz...</option>
                        </select>
                    </div>
                </div>

                <!-- Tutar -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Tutar (₺) *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lira-sign text-gray-400"></i>
                        </div>
                        <input type="number" name="amount" id="amount" 
                               step="0.01" min="0.01" required
                               class="block w-full pl-10 pr-12 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="0.00">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₺</span>
                        </div>
                    </div>
                </div>

                <!-- Açıklama -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Açıklama *</label>
                    <div class="mt-1">
                        <textarea name="description" id="description" rows="3" required
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Hareket açıklaması..."></textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Hareketin detaylı açıklamasını giriniz. Bu bilgi raporlarda görünecektir.
                    </p>
                </div>

                <!-- Uyarı Mesajı -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Dikkat</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Manuel hareketler kasa bakiyesini direkt etkiler.</li>
                                    <li>Gelir hareketleri bakiyeyi artırır, gider hareketleri azaltır.</li>
                                    <li>Bu işlem geri alınamaz, lütfen dikkatli olunuz.</li>
                                    <li>Tüm hareketler sistem tarafından kaydedilir.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Butonlar -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="/cash" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        İptal
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Hareketi Kaydet
                    </button>
                </div>
            </form>
        </div>

        <!-- Kategori Bilgileri -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Kategori Açıklamaları
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Gelir Kategorileri -->
                    <div>
                        <h4 class="text-sm font-medium text-green-900 mb-3">
                            <i class="fas fa-arrow-up text-green-600 mr-2"></i>
                            Gelir Kategorileri
                        </h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div><strong>Ek Gelir:</strong> Salon dışı gelirler</div>
                            <div><strong>Sermaye Ekleme:</strong> İşletmeye eklenen para</div>
                            <div><strong>Alınan Borç:</strong> Kredi, borç alımları</div>
                            <div><strong>Diğer Gelir:</strong> Tanımlanmamış gelirler</div>
                        </div>
                    </div>

                    <!-- Gider Kategorileri -->
                    <div>
                        <h4 class="text-sm font-medium text-red-900 mb-3">
                            <i class="fas fa-arrow-down text-red-600 mr-2"></i>
                            Gider Kategorileri
                        </h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div><strong>Kira:</strong> Salon kira ödemeleri</div>
                            <div><strong>Faturalar:</strong> Elektrik, su, telefon vb.</div>
                            <div><strong>Malzeme:</strong> Salon malzeme alımları</div>
                            <div><strong>Personel Avansı:</strong> Personele verilen avanslar</div>
                            <div><strong>Bakım-Onarım:</strong> Cihaz ve salon bakımları</div>
                            <div><strong>Pazarlama:</strong> Reklam ve tanıtım giderleri</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category');
    
    // Kategori seçenekleri
    const categories = <?= json_encode($categories) ?>;
    
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        // Kategori seçeneklerini temizle
        categorySelect.innerHTML = '<option value="">Kategori seçiniz...</option>';
        
        if (selectedType && categories[selectedType]) {
            // Seçilen türe göre kategorileri ekle
            Object.entries(categories[selectedType]).forEach(([value, text]) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = text;
                categorySelect.appendChild(option);
            });
        }
    });
    
    // Form gönderilmeden önce onay al
    document.querySelector('form').addEventListener('submit', function(e) {
        const type = typeSelect.value;
        const amount = parseFloat(document.getElementById('amount').value);
        const typeText = type === 'income' ? 'Gelir' : 'Gider';
        
        if (!confirm(`${typeText} hareketi olarak ${amount.toFixed(2)} ₺ kaydedilecek. Emin misiniz?`)) {
            e.preventDefault();
        }
    });
});
</script>
<?= $this->endSection() ?>