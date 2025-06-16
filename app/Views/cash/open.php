<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-unlock mr-3 text-green-600"></i>
                    Kasa Açılışı
                </h1>
                <a href="/cash" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Geri Dön
                </a>
            </div>
            <p class="mt-2 text-sm text-gray-600">
                Günlük kasa işlemlerine başlamak için kasayı açınız.
            </p>
        </div>

        <!-- Kasa Açılış Formu -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-calendar-day mr-2 text-blue-600"></i>
                    <?= date('d.m.Y') ?> Tarihli Kasa Açılışı
                </h3>
            </div>
            
            <form action="/cash/open" method="POST" class="p-6 space-y-6">
                <?= csrf_field() ?>
                
                <!-- Açılış Tutarı -->
                <div>
                    <label for="opening_amount" class="block text-sm font-medium text-gray-700">
                        Açılış Tutarı (₺) *
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lira-sign text-gray-400"></i>
                        </div>
                        <input type="number" name="opening_amount" id="opening_amount" 
                               step="0.01" min="0" required
                               class="block w-full pl-10 pr-12 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                               placeholder="0.00">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₺</span>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Kasada bulunan başlangıç tutarını giriniz. Bu tutar günün başlangıç bakiyesi olacaktır.
                    </p>
                </div>

                <!-- Notlar -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        Notlar
                    </label>
                    <div class="mt-1">
                        <textarea name="notes" id="notes" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                  placeholder="Kasa açılışı ile ilgili notlar..."></textarea>
                    </div>
                </div>

                <!-- Uyarı Mesajı -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Önemli Bilgiler</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Kasa açılışı günde sadece bir kez yapılabilir.</li>
                                    <li>Açılış tutarı dikkatli bir şekilde sayılarak girilmelidir.</li>
                                    <li>Bu işlem geri alınamaz, lütfen tutarı kontrol ediniz.</li>
                                    <li>Kasa açıldıktan sonra günlük işlemlere başlayabilirsiniz.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Butonlar -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="/cash" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        İptal
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-unlock mr-2"></i>
                        Kasayı Aç
                    </button>
                </div>
            </form>
        </div>

        <!-- Yardım Bilgileri -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Kasa Açılışı Hakkında
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-blue-600 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Ne Zaman Açılır?</h4>
                            <p class="text-sm text-gray-500">
                                Kasa her iş günü başında, ilk işlem öncesinde açılmalıdır. 
                                Genellikle salon açılışında yapılır.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calculator text-green-600 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Açılış Tutarı Nasıl Belirlenir?</h4>
                            <p class="text-sm text-gray-500">
                                Kasadaki tüm nakit para sayılır ve toplam tutar girilir. 
                                Bu tutar günün başlangıç bakiyesi olur.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shield-alt text-purple-600 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Güvenlik</h4>
                            <p class="text-sm text-gray-500">
                                Kasa açılışı işlemi sistem tarafından kaydedilir ve 
                                kim tarafından yapıldığı takip edilir.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('opening_amount');
    
    // Tutar alanına odaklan
    amountInput.focus();
    
    // Form gönderilmeden önce onay al
    document.querySelector('form').addEventListener('submit', function(e) {
        const amount = parseFloat(amountInput.value);
        
        if (amount < 0) {
            e.preventDefault();
            alert('Açılış tutarı negatif olamaz.');
            return;
        }
        
        if (!confirm(`Kasa ${amount.toFixed(2)} ₺ ile açılacak. Emin misiniz?`)) {
            e.preventDefault();
        }
    });
});
</script>
<?= $this->endSection() ?>