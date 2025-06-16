<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Yeni Prim Kuralı</h1>
            <p class="text-gray-600">Personel prim kuralı oluşturun</p>
        </div>
        <a href="/commissions/rules" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Geri Dön
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="/commissions/rules/store" method="POST" id="ruleForm">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Şube -->
                <?php if ($userRole === 'admin'): ?>
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Şube <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id" id="branch_id" required 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Şube Seçiniz</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= old('branch_id') == $branch['id'] ? 'selected' : '' ?>>
                                    <?= esc($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="branch_id" value="<?= $branches[0]['id'] ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Şube</label>
                        <div class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50">
                            <?= esc($branches[0]['name']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Kural Tipi -->
                <div>
                    <label for="rule_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Kural Tipi <span class="text-red-500">*</span>
                    </label>
                    <select name="rule_type" id="rule_type" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Kural Tipi Seçiniz</option>
                        <?php foreach ($ruleTypes as $key => $label): ?>
                            <option value="<?= $key ?>" <?= old('rule_type') == $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Genel: Tüm personel için, Hizmete Özel: Belirli hizmet için, Personele Özel: Belirli personel için
                    </p>
                </div>

                <!-- Personel Seçimi -->
                <div id="userSelection" style="display: none;">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Personel <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Personel Seçiniz</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['role_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Hizmet Seçimi -->
                <div id="serviceSelection" style="display: none;">
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Hizmet <span class="text-red-500">*</span>
                    </label>
                    <select name="service_id" id="service_id" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Hizmet Seçiniz</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['id'] ?>" <?= old('service_id') == $service['id'] ? 'selected' : '' ?>>
                                <?= esc($service['name']) ?> (₺<?= number_format($service['price'], 2) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Prim Tipi -->
                <div>
                    <label for="commission_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Prim Tipi <span class="text-red-500">*</span>
                    </label>
                    <select name="commission_type" id="commission_type" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Prim Tipi Seçiniz</option>
                        <?php foreach ($commissionTypes as $key => $label): ?>
                            <option value="<?= $key ?>" <?= old('commission_type') == $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Prim Değeri -->
                <div>
                    <label for="commission_value" class="block text-sm font-medium text-gray-700 mb-2">
                        Prim Değeri <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="commission_value" id="commission_value" 
                               step="0.01" min="0" required
                               value="<?= old('commission_value') ?>"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div id="commissionUnit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                            %
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="commissionHelp">
                        Yüzdesel prim için 0-100 arası değer giriniz
                    </p>
                </div>

                <!-- Hizmet Türü -->
                <div>
                    <label for="is_package_rule" class="block text-sm font-medium text-gray-700 mb-2">
                        Hizmet Türü <span class="text-red-500">*</span>
                    </label>
                    <select name="is_package_rule" id="is_package_rule" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="0" <?= old('is_package_rule') == '0' ? 'selected' : '' ?>>Normal Hizmetler</option>
                        <option value="1" <?= old('is_package_rule') == '1' ? 'selected' : '' ?>>Paketli Hizmetler</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Normal hizmetler ve paketli hizmetler için farklı prim oranları belirleyebilirsiniz
                    </p>
                </div>

                <!-- Durum -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                        Durum <span class="text-red-500">*</span>
                    </label>
                    <select name="is_active" id="is_active" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1" <?= old('is_active') == '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= old('is_active') == '0' ? 'selected' : '' ?>>Pasif</option>
                    </select>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="/commissions/rules" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md">
                    İptal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                    Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ruleTypeSelect = document.getElementById('rule_type');
    const userSelection = document.getElementById('userSelection');
    const serviceSelection = document.getElementById('serviceSelection');
    const userSelect = document.getElementById('user_id');
    const serviceSelect = document.getElementById('service_id');
    const commissionTypeSelect = document.getElementById('commission_type');
    const commissionUnit = document.getElementById('commissionUnit');
    const commissionHelp = document.getElementById('commissionHelp');
    const branchSelect = document.getElementById('branch_id');

    // Kural tipine göre alanları göster/gizle
    function toggleFields() {
        const ruleType = ruleTypeSelect.value;
        
        if (ruleType === 'user_specific') {
            userSelection.style.display = 'block';
            serviceSelection.style.display = 'block';
            userSelect.required = true;
            serviceSelect.required = false; // Personele özel genel kural da olabilir
        } else if (ruleType === 'service_specific') {
            userSelection.style.display = 'none';
            serviceSelection.style.display = 'block';
            userSelect.required = false;
            serviceSelect.required = true;
        } else {
            userSelection.style.display = 'none';
            serviceSelection.style.display = 'none';
            userSelect.required = false;
            serviceSelect.required = false;
        }
    }

    // Prim tipine göre birimi değiştir
    function updateCommissionUnit() {
        const commissionType = commissionTypeSelect.value;
        
        if (commissionType === 'percentage') {
            commissionUnit.textContent = '%';
            commissionHelp.textContent = 'Yüzdesel prim için 0-100 arası değer giriniz';
        } else if (commissionType === 'fixed_amount') {
            commissionUnit.textContent = '₺';
            commissionHelp.textContent = 'Sabit tutar için TL cinsinden değer giriniz';
        }
    }

    // Şube değiştiğinde personel ve hizmetleri güncelle
    function updateBranchData() {
        const branchId = branchSelect ? branchSelect.value : <?= $branches[0]['id'] ?>;
        
        if (branchId) {
            // Personelleri güncelle
            fetch(`/commissions/users-by-branch/${branchId}`)
                .then(response => response.json())
                .then(users => {
                    userSelect.innerHTML = '<option value="">Personel Seçiniz</option>';
                    users.forEach(user => {
                        userSelect.innerHTML += `<option value="${user.id}">${user.first_name} ${user.last_name} (${user.role_name})</option>`;
                    });
                });

            // Hizmetleri güncelle
            fetch(`/commissions/services-by-branch/${branchId}`)
                .then(response => response.json())
                .then(services => {
                    serviceSelect.innerHTML = '<option value="">Hizmet Seçiniz</option>';
                    services.forEach(service => {
                        serviceSelect.innerHTML += `<option value="${service.id}">${service.name} (₺${parseFloat(service.price).toFixed(2)})</option>`;
                    });
                });
        }
    }

    // Event listeners
    ruleTypeSelect.addEventListener('change', toggleFields);
    commissionTypeSelect.addEventListener('change', updateCommissionUnit);
    
    if (branchSelect) {
        branchSelect.addEventListener('change', updateBranchData);
    }

    // Sayfa yüklendiğinde çalıştır
    toggleFields();
    updateCommissionUnit();
    updateBranchData();
});
</script>
<?= $this->endSection() ?>