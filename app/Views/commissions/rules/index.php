<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Prim Kuralları</h1>
            <p class="text-gray-600">Personel prim kurallarını yönetin</p>
        </div>
        <a href="/commissions/rules/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Yeni Kural
        </a>
    </div>

    <!-- Flash Messages -->
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <?php if ($userRole === 'admin'): ?>
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
                    <select name="branch_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Tüm Şubeler</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>" <?= (request()->getGet('branch_id') == $branch['id']) ? 'selected' : '' ?>>
                                <?= esc($branch['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kural Tipi</label>
                <select name="rule_type" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Tüm Tipler</option>
                    <option value="general" <?= (request()->getGet('rule_type') == 'general') ? 'selected' : '' ?>>Genel Kural</option>
                    <option value="service_specific" <?= (request()->getGet('rule_type') == 'service_specific') ? 'selected' : '' ?>>Hizmete Özel</option>
                    <option value="user_specific" <?= (request()->getGet('rule_type') == 'user_specific') ? 'selected' : '' ?>>Personele Özel</option>
                </select>
            </div>

            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select name="is_active" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Tümü</option>
                    <option value="1" <?= (request()->getGet('is_active') == '1') ? 'selected' : '' ?>>Aktif</option>
                    <option value="0" <?= (request()->getGet('is_active') == '0') ? 'selected' : '' ?>>Pasif</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-search mr-2"></i>Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Rules Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kural</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hedef</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($rules)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Henüz prim kuralı bulunmamaktadır.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rules as $rule): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php if ($rule['rule_type'] === 'general'): ?>
                                            Genel Kural
                                        <?php elseif ($rule['rule_type'] === 'service_specific'): ?>
                                            <?= esc($rule['service_name']) ?>
                                        <?php else: ?>
                                            <?= esc($rule['first_name'] . ' ' . $rule['last_name']) ?>
                                            <?php if ($rule['service_name']): ?>
                                                <br><span class="text-xs text-gray-500"><?= esc($rule['service_name']) ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($userRole === 'admin'): ?>
                                        <div class="text-xs text-gray-500"><?= esc($rule['branch_name']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php if ($rule['rule_type'] === 'general'): ?>
                                            bg-blue-100 text-blue-800
                                        <?php elseif ($rule['rule_type'] === 'service_specific'): ?>
                                            bg-green-100 text-green-800
                                        <?php else: ?>
                                            bg-purple-100 text-purple-800
                                        <?php endif; ?>">
                                        <?php
                                        $typeLabels = [
                                            'general' => 'Genel',
                                            'service_specific' => 'Hizmete Özel',
                                            'user_specific' => 'Personele Özel'
                                        ];
                                        echo $typeLabels[$rule['rule_type']];
                                        ?>
                                    </span>
                                    <?php if ($rule['is_package_rule']): ?>
                                        <br><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mt-1">
                                            Paket
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php if ($rule['commission_type'] === 'percentage'): ?>
                                            %<?= number_format($rule['commission_value'], 1) ?>
                                        <?php else: ?>
                                            ₺<?= number_format($rule['commission_value'], 2) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= $rule['commission_type'] === 'percentage' ? 'Yüzdesel' : 'Sabit Tutar' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $rule['is_package_rule'] ? 'Paketli Hizmetler' : 'Normal Hizmetler' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?= $rule['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $rule['is_active'] ? 'Aktif' : 'Pasif' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/commissions/rules/edit/<?= $rule['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteRule(<?= $rule['id'] ?>)" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Kuralı Sil</h3>
        <p class="text-sm text-gray-500 mb-4">Bu prim kuralını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                İptal
            </button>
            <button onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                Sil
            </button>
        </div>
    </div>
</div>

<script>
let deleteRuleId = null;

function deleteRule(id) {
    deleteRuleId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
    deleteRuleId = null;
}

function confirmDelete() {
    if (deleteRuleId) {
        fetch(`/commissions/rules/delete/${deleteRuleId}`, {
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
                alert(data.message || 'Bir hata oluştu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
    closeDeleteModal();
}
</script>
<?= $this->endSection() ?>