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

        <!-- Ana Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Ödeme Bilgileri</h3>
            </div>

            <form id="paymentForm" action="/payments/create" method="POST" class="p-6 space-y-6">
                <?= csrf_field() ?>

                <!-- Müşteri Seçimi -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Müşteri *</label>
                    <div class="mt-1">
                        <select id="customer_id" name="customer_id" class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Müşteri Seçin</option>
                        </select>
                    </div>
                </div>

                <!-- Randevu Seçimi -->
                <div>
                    <label for="appointment_id" class="block text-sm font-medium text-gray-700">Randevu</label>
                    <div class="mt-1">
                        <select id="appointment_id" name="appointment_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Randevu Seçin</option>
                        </select>
                    </div>
                </div>

                <!-- Ödeme Bilgileri -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tutar -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Tutar *</label>
                        <div class="mt-1">
                            <input type="number" step="0.01" min="0" id="amount" name="amount" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Ödeme Tipi -->
                    <div>
                        <label for="payment_type" class="block text-sm font-medium text-gray-700">Ödeme Tipi *</label>
                        <div class="mt-1">
                            <select id="payment_type" name="payment_type" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Seçin</option>
                                <option value="cash">Nakit</option>
                                <option value="credit_card">Kredi Kartı</option>
                                <option value="bank_transfer">Banka Havalesi</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Açıklama -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Açıklama</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        İptal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Ödeme Al
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Müşteri seçimi için select2
    $('#customer_id').select2({
        dropdownParent: $('#paymentForm'),
        ajax: {
            url: '/payments/search-customers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(customer) {
                        return {
                            id: customer.id,
                            text: customer.first_name + ' ' + customer.last_name + ' (' + customer.phone + ')'
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3,
        placeholder: 'Müşteri Ara (Ad, Soyad veya Telefon)',
        language: {
            inputTooShort: function() {
                return 'Lütfen en az 3 karakter girin';
            },
            noResults: function() {
                return 'Sonuç bulunamadı';
            },
            searching: function() {
                return 'Aranıyor...';
            }
        }
    });

    // Müşteri seçildiğinde randevuları yükle
    $('#customer_id').on('change', function() {
        var customerId = $(this).val();
        if (customerId) {
            // Bekleyen ödemeli randevuları getir
            $.get('/calendar/pending-payments/' + customerId, function(appointments) {
                var $select = $('#appointment_id');
                $select.empty();
                $select.append($('<option>', {
                    value: '',
                    text: 'Randevu Seçin'
                }));
                
                appointments.forEach(function(appointment) {
                    var appointmentDate = moment(appointment.appointment_date).format('DD/MM/YYYY');
                    var remainingAmount = appointment.price - appointment.paid_amount;
                    
                    var optionText = appointmentDate + ' - ' + 
                                   appointment.service_name + 
                                   ' (Kalan: ' + remainingAmount.toFixed(2) + ' TL)';
                    
                    $select.append($('<option>', {
                        value: appointment.id,
                        text: optionText,
                        'data-remaining': remainingAmount
                    }));
                });
            });
        } else {
            $('#appointment_id').empty().append($('<option>', {
                value: '',
                text: 'Randevu Seçin'
            }));
        }
    });

    // Randevu seçildiğinde kalan tutarı otomatik doldur
    $('#appointment_id').on('change', function() {
        var $selected = $(this).find('option:selected');
        var remaining = $selected.data('remaining');
        if (remaining) {
            $('#amount').val(remaining);
        }
    });

    // Form submit öncesi Select2 değerlerini inputlara yaz
    $('#paymentForm').on('submit', function(e) {
        // Select2 değerlerini güncelle
        $('#customer_id').val($('#customer_id').val()).trigger('change');
        
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: response.message,
                        confirmButtonText: 'Tamam'
                    }).then((result) => {
                        window.location.href = '/payments';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: response.message,
                        confirmButtonText: 'Tamam'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Bir hata oluştu. Lütfen tekrar deneyin.',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
