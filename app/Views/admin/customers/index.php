<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= esc($pageTitle ?? 'Müşteri Yönetimi') ?></h1>
                    <p class="text-gray-600">Müşteri bilgilerini görüntüleyin ve yönetin</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?= site_url('/admin/customers/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Yeni Müşteri
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex">
                <i class="fas fa-check-circle mr-2 mt-0.5"></i>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex">
                <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Arama ve Filtreler -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Arama -->
                <div class="md:col-span-2">
                    <label for="customer-search" class="block text-sm font-medium text-gray-700 mb-1">Müşteri Ara</label>
                    <div class="relative">
                        <input type="text" id="customer-search" placeholder="Ad, soyad, telefon veya e-posta ile ara..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <div id="search-loading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Şube Filtresi -->
                <div>
                    <label for="branch-filter" class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
                    <select id="branch-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tüm Şubeler</option>
                        <?php if (isset($branches)): ?>
                            <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= esc($branch['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Müşteri Listesi -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    Müşteriler
                    <span id="customer-count" class="text-sm text-gray-500">
                        (<?= count($customers ?? []) ?> müşteri)
                    </span>
                </h3>
            </div>
            
            <div id="customers-container">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Müşteri
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    İletişim
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Şube
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Etiketler
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kayıt Tarihi
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody id="customers-table-body" class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-blue-600">
                                                            <?= strtoupper(substr($customer['first_name'], 0, 1) . substr($customer['last_name'], 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?= esc($customer['first_name']) ?> <?= esc($customer['last_name']) ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        ID: <?= esc($customer['id']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <div class="flex items-center mb-1">
                                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                                    <?= esc($customer['phone']) ?>
                                                </div>
                                                <?php if (!empty($customer['email'])): ?>
                                                <div class="flex items-center">
                                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                                    <?= esc($customer['email']) ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <?= esc($customer['branch_name'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (!empty($customer['tags']) && is_array($customer['tags'])): ?>
                                                <div class="flex flex-wrap gap-1">
                                                    <?php foreach ($customer['tags'] as $tag): ?>
                                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                            <?= esc(trim($tag)) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('d.m.Y', strtotime($customer['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-2">
                                                <a href="<?= site_url('/admin/customers/view/' . $customer['id']) ?>"
                                                   class="text-blue-600 hover:text-blue-900 p-1 rounded" title="Görüntüle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= site_url('/admin/customers/edit/' . $customer['id']) ?>"
                                                   class="text-yellow-600 hover:text-yellow-900 p-1 rounded" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete('<?= site_url('/admin/customers/delete/' . $customer['id']) ?>')"
                                                        class="text-red-600 hover:text-red-900 p-1 rounded" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-users text-4xl mb-4"></i>
                                            <p class="text-lg font-medium">Kayıtlı müşteri bulunamadı</p>
                                            <p class="text-sm">Yeni müşteri eklemek için yukarıdaki butonu kullanın</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global değişkenler
let searchTimeout;
let currentSearchQuery = '';

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    setupSearch();
    setupFilters();
});

// Arama işlevini ayarla
function setupSearch() {
    const searchInput = document.getElementById('customer-search');
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        // Minimum 2 karakter veya boş arama
        if (query.length >= 2 || query.length === 0) {
            showSearchLoading(true);
            
            searchTimeout = setTimeout(() => {
                searchCustomers(query);
            }, 300); // 300ms debounce
        }
    });
}

// Filtreleri ayarla
function setupFilters() {
    const branchFilter = document.getElementById('branch-filter');
    
    branchFilter.addEventListener('change', function() {
        const searchQuery = document.getElementById('customer-search').value.trim();
        searchCustomers(searchQuery, this.value);
    });
}

// Müşteri arama
function searchCustomers(query = '', branchId = '') {
    const branchFilter = document.getElementById('branch-filter');
    const selectedBranch = branchId || branchFilter.value;
    
    const params = new URLSearchParams();
    if (query) params.append('search', query);
    if (selectedBranch) params.append('branch_id', selectedBranch);
    
    fetch(`/admin/customers?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCustomersTable(data.customers);
            updateCustomerCount(data.customers.length);
        } else {
            showAlert('Arama sırasında bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Arama hatası:', error);
        showAlert('Arama sırasında bir ağ hatası oluştu', 'error');
    })
    .finally(() => {
        showSearchLoading(false);
    });
}

// Arama loading göster/gizle
function showSearchLoading(show) {
    const loadingIcon = document.getElementById('search-loading');
    if (show) {
        loadingIcon.classList.remove('hidden');
    } else {
        loadingIcon.classList.add('hidden');
    }
}

// Müşteri tablosunu güncelle
function updateCustomersTable(customers) {
    const tbody = document.getElementById('customers-table-body');
    
    if (customers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <i class="fas fa-search text-4xl mb-4"></i>
                        <p class="text-lg font-medium">Arama sonucu bulunamadı</p>
                        <p class="text-sm">Farklı anahtar kelimeler deneyin</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = customers.map(customer => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-600">
                                ${(customer.first_name.charAt(0) + customer.last_name.charAt(0)).toUpperCase()}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                            ${escapeHtml(customer.first_name)} ${escapeHtml(customer.last_name)}
                        </div>
                        <div class="text-sm text-gray-500">
                            ID: ${customer.id}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                        ${escapeHtml(customer.phone)}
                    </div>
                    ${customer.email ? `
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                        ${escapeHtml(customer.email)}
                    </div>
                    ` : ''}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                    ${escapeHtml(customer.branch_name || 'N/A')}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${customer.tags && customer.tags.length > 0 ? `
                    <div class="flex flex-wrap gap-1">
                        ${customer.tags.map(tag => `
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                ${escapeHtml(tag.trim())}
                            </span>
                        `).join('')}
                    </div>
                ` : '<span class="text-gray-400">-</span>'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${new Date(customer.created_at).toLocaleDateString('tr-TR')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                <div class="flex justify-center space-x-2">
                    <a href="/admin/customers/view/${customer.id}"
                       class="text-blue-600 hover:text-blue-900 p-1 rounded" title="Görüntüle">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/admin/customers/edit/${customer.id}"
                       class="text-yellow-600 hover:text-yellow-900 p-1 rounded" title="Düzenle">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="confirmDelete('/admin/customers/delete/${customer.id}')"
                            class="text-red-600 hover:text-red-900 p-1 rounded" title="Sil">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Müşteri sayısını güncelle
function updateCustomerCount(count) {
    const countElement = document.getElementById('customer-count');
    countElement.textContent = `(${count} müşteri)`;
}

// HTML escape
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Alert göster
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Silme onayı
function confirmDelete(url) {
    if (confirm('Bu müşteriyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                // Mevcut arama/filtre ile yenile
                const searchQuery = document.getElementById('customer-search').value.trim();
                const branchId = document.getElementById('branch-filter').value;
                searchCustomers(searchQuery, branchId);
            } else {
                showAlert(data.message || 'Müşteri silinirken bir sorun oluştu.', 'error');
            }
        })
        .catch(error => {
            console.error('Silme hatası:', error);
            showAlert('Müşteri silinirken bir ağ hatası oluştu.', 'error');
        });
    }
}
</script>
<?= $this->endSection() ?>