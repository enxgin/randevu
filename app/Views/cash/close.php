<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Başlık -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-lock mr-3 text-red-600"></i>
                    Kasa Kapanışı
                </h1>
                <a href="/cash" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Geri Dön
                </a>
            </div>
            <p class="mt-2 text-sm text-gray-600">
                Günlük kasa işlemlerini tamamlamak için kasayı kapatınız.
            </p>
        </div>

        <!-- Günlük Özet -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                    Günlük Özet
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            <?= number_format($dailySummary['opening_balance'], 2) ?> ₺
                        </div>
                        <div class="text-sm text-gray-500">Açılış Bakiyesi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            +<?= number_format($dailySummary['total_income'], 2) ?> ₺
                        </div>
                        <div class="text-sm text-gray-500">Toplam Gelir</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">
                            -<?= number_format($dailySummary['total_expense'], 2) ?> ₺
                        </div>
                        <div class="text-sm text-gray-500">Toplam Gider</div>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <div class="text-3xl font-bold text-gray-900">
                        <?= number_format($currentBalance, 2) ?> ₺
                    </div>
                    <div class="text-sm text-gray-500">Sistem Bakiyesi</div>
                </div>
            </div>
        </div>

        <!-- Kasa Kapanış Formu -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-calendar-day mr-2 text-red-600"></i>
                    <?= date('d.m.Y') ?> Tarihli Kasa Kapanışı
                </h3>
            </div>
            
            <form action="/cash/close" method="POST" class="p-6 space-y-6">
                <?= csrf_field() ?>
                
                <!-- Fiili Tutar -->
                <div>
                    <label for="actual_amount" class="block text-sm font-medium text-gray-700">
                        Kasadaki Fiili Tutar (₺) *
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lira-sign text-gray-400"></i>
                        </div>
                        <input type="number" name="actual_amount" id="actual_amount" 
                               step="0.01" min="0" required
                               class="block w-full pl-10 pr-12 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="0.00">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₺</span>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Kasada fiilen bulunan parayı sayarak giriniz. Sistem bakiyesi ile karşılaştırılacaktır.
                    </p>
                </div>

                <!-- Fark Gösterimi -->
                <div id="difference_display" class="hidden">
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i id="difference_icon" class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">Fark Analizi</h3>
                                <div class="mt-2 text-sm">
                                    <div>Sistem Bakiyesi: <span class="font-medium"><?= number_format($currentBalance, 2) ?> ₺</span></div>
                                    <div>Fiili Tutar: <span id="actual_display" class="font-medium">0.00 ₺</span></div>
                                    <div id="difference_text" class="font-medium"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notlar -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        Notlar
                    </label>
                    <div class="mt-1">
                        <textarea name="notes" id="notes" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                  placeholder="Kasa kapanışı ile ilgili notlar..."></textarea>
                    </div>
                </div>

                <!-- Uyarı Mesajı -->
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Önemli Bilgiler</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Kasa kapanışı günde sadece bir kez yapılabilir.</li>
                                    <li>Fiili tutar dikkatli bir şekilde sayılarak girilmelidir.</li>
                                    <li>Fark varsa otomatik düzeltme hareketi oluşturulacaktır.</li>
                                    <li>Bu işlem geri alınamaz, lütfen tutarı kontrol ediniz.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Butonlar -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="/cash" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        İptal
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-lock mr-2"></i>
                        Kasayı Kapat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const actualAmountInput = document.getElementById('actual_amount');
    const differenceDisplay = document.getElementById('difference_display');
    const differenceIcon = document.getElementById('difference_icon');
    const actualDisplay = document.getElementById('actual_display');
    const differenceText = document.getElementById('difference_text');
    const systemBalance = <?= $currentBalance ?>;
    
    actualAmountInput.addEventListener('input', function() {
        const actualAmount = parseFloat(this.value) || 0;
        const difference = actualAmount - systemBalance;
        
        if (this.value) {
            differenceDisplay.classList.remove('hidden');
            actualDisplay.textContent = actualAmount.toFixed(2) + ' ₺';
            
            if (Math.abs(difference) < 0.01) {
                // Fark yok
                differenceIcon.className = 'fas fa-check-circle text-green-400';
                differenceText.innerHTML = 'Fark: <span class="text-green-600">0.00 ₺ (Uyumlu)</span>';
                differenceDisplay.querySelector('.bg-gray-50').className = 'bg-green-50 border border-green-200 rounded-md p-4';
            } else if (difference > 0) {
                // Fazla
                differenceIcon.className = 'fas fa-arrow-up text-blue-400';
                differenceText.innerHTML = 'Fark: <span class="text-blue-600">+' + difference.toFixed(2) + ' ₺ (Fazla)</span>';
                differenceDisplay.querySelector('.bg-green-50, .bg-red-50').className = 'bg-blue-50 border border-blue-200 rounded-md p-4';
            } else {
                // Eksik
                differenceIcon.className = 'fas fa-arrow-down text-red-400';
                differenceText.innerHTML = 'Fark: <span class="text-red-600">' + difference.toFixed(2) + ' ₺ (Eksik)</span>';
                differenceDisplay.querySelector('.bg-green-50, .bg-blue-50').className = 'bg-red-50 border border-red-200 rounded-md p-4';
            }
        } else {
            differenceDisplay.classList.add('hidden');
        }
    });
    
    // Tutar alanına odaklan
    actualAmountInput.focus();
    
    // Form gönderilmeden önce onay al
    document.querySelector('form').addEventListener('submit', function(e) {
        const actualAmount = parseFloat(actualAmountInput.value) || 0;
        const difference = actualAmount - systemBalance;
        
        let message = `Kasa ${actualAmount.toFixed(2)} ₺ ile kapatılacak.`;
        if (Math.abs(difference) > 0.01) {
            message += `\nFark: ${difference.toFixed(2)} ₺`;
        }
        message += '\nEmin misiniz?';
        
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});
</script>
<?= $this->endSection() ?>