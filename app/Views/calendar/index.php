<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                    <p class="text-gray-600">Randevuları görüntüleyin ve yönetin</p>
                </div>
                <?php if ($userRole !== 'staff'): ?>
                <div class="flex space-x-3">
                    <a href="/calendar/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Yeni Randevu
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filtreler -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Şube Filtresi -->
                <?php if ($userRole === 'admin' && count($branches) > 1): ?>
                <div>
                    <label for="branch-filter" class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
                    <select id="branch-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tüm Şubeler</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= esc($branch['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Personel Filtresi -->
                <?php if ($userRole !== 'staff'): ?>
                <div>
                    <label for="staff-filter" class="block text-sm font-medium text-gray-700 mb-1">Personel</label>
                    <select id="staff-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tüm Personeller</option>
                        <?php foreach ($staff as $person): ?>
                        <option value="<?= $person['id'] ?>"><?= esc($person['first_name'] . ' ' . $person['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Görünüm Seçenekleri -->
                <div>
                    <label for="view-filter" class="block text-sm font-medium text-gray-700 mb-1">Görünüm</label>
                    <select id="view-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="dayGridMonth">Aylık</option>
                        <option value="timeGridWeek">Haftalık</option>
                        <option value="timeGridDay">Günlük</option>
                        <option value="resourceTimeGridDay">Personel Bazlı</option>
                    </select>
                </div>
            </div>
            
            <!-- Toplu İşlemler -->
            <?php if ($userRole !== 'staff'): ?>
            <div id="bulk-actions" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-blue-900">
                            <span id="selected-count">0</span> randevu seçildi
                        </span>
                        <div class="flex space-x-2">
                            <select id="bulk-action" class="border border-blue-300 rounded px-3 py-1 text-sm">
                                <option value="">İşlem Seçin</option>
                                <option value="status">Durum Değiştir</option>
                                <option value="staff">Personel Değiştir</option>
                                <option value="delete">Sil</option>
                            </select>
                            <select id="bulk-value" class="border border-blue-300 rounded px-3 py-1 text-sm hidden">
                                <!-- Dinamik olarak doldurulacak -->
                            </select>
                            <button id="apply-bulk" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                Uygula
                            </button>
                        </div>
                    </div>
                    <button id="clear-selection" class="text-blue-600 hover:text-blue-800 text-sm">
                        Seçimi Temizle
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Takvim -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Randevu Detay Modal -->
<div id="appointment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Randevu Detayları</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4">
                <div id="modal-content">
                    <!-- Dinamik içerik buraya gelecek -->
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <?php if ($userRole !== 'staff'): ?>
                <button id="edit-appointment" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>Düzenle
                </button>
                <button id="delete-appointment" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-trash mr-2"></i>Sil
                </button>
                <?php endif; ?>
                <button id="close-modal-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Durum Güncelleme Modal -->
<div id="status-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Randevu Durumu Güncelle</h3>
            </div>
            <form id="status-form">
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label for="status-select" class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                        <select id="status-select" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="pending">Onay Bekliyor</option>
                            <option value="confirmed">Onaylandı</option>
                            <option value="completed">Tamamlandı</option>
                            <option value="cancelled">İptal Edildi</option>
                            <option value="no_show">Gelmedi</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="status-notes" class="block text-sm font-medium text-gray-700 mb-2">Not (Opsiyonel)</label>
                        <textarea id="status-notes" name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Durum değişikliği ile ilgili not..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="cancel-status" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                        İptal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sağ Tık Menüsü -->
<div id="context-menu" class="fixed bg-white border border-gray-200 rounded-lg shadow-lg py-2 hidden z-50" style="min-width: 180px;">
    <?php if ($userRole !== 'staff'): ?>
    <button class="context-menu-item w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" data-action="edit">
        <i class="fas fa-edit mr-2 text-blue-600"></i>Düzenle
    </button>
    <button class="context-menu-item w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" data-action="copy">
        <i class="fas fa-copy mr-2 text-green-600"></i>Kopyala
    </button>
    <hr class="my-1">
    <button class="context-menu-item w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" data-action="status-confirmed">
        <i class="fas fa-check mr-2 text-blue-600"></i>Onayla
    </button>
    <button class="context-menu-item w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" data-action="status-completed">
        <i class="fas fa-check-double mr-2 text-green-600"></i>Tamamlandı
    </button>
    <button class="context-menu-item w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" data-action="status-cancelled">
        <i class="fas fa-times mr-2 text-red-600"></i>İptal Et
    </button>
    <button class="context-menu-item w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" data-action="status-no_show">
        <i class="fas fa-user-times mr-2 text-gray-600"></i>Gelmedi
    </button>
    <hr class="my-1">
    <button class="context-menu-item w-full text-left px-4 py-2 hover:bg-gray-100 text-sm text-red-600" data-action="delete">
        <i class="fas fa-trash mr-2"></i>Sil
    </button>
    <?php else: ?>
    <button class="context-menu-item w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" data-action="view">
        <i class="fas fa-eye mr-2 text-blue-600"></i>Görüntüle
    </button>
    <?php endif; ?>
</div>

<!-- Randevu Kopyalama Modal -->
<div id="copy-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Randevu Kopyala</h3>
            </div>
            <form id="copy-form">
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label for="copy-date" class="block text-sm font-medium text-gray-700 mb-2">Yeni Tarih</label>
                        <input type="date" id="copy-date" name="new_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="copy-time" class="block text-sm font-medium text-gray-700 mb-2">Yeni Saat</label>
                        <input type="time" id="copy-time" name="new_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" id="cancel-copy" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                        İptal
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Kopyala
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global değişkenler
let calendar;
let currentAppointment = null;
let selectedAppointments = new Set();
let contextMenuEvent = null;

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    setupEventListeners();
    setupContextMenu();
    setupBulkActions();
});

// Takvimi başlat
function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'tr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '00:30:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6], // Pazartesi-Cumartesi
            startTime: '09:00',
            endTime: '18:00'
        },
        events: {
            url: '/calendar/events',
            method: 'GET',
            extraParams: function() {
                return {
                    branch_id: document.getElementById('branch-filter')?.value || '',
                    staff_id: document.getElementById('staff-filter')?.value || ''
                };
            },
            failure: function() {
                alert('Randevular yüklenirken bir hata oluştu.');
            }
        },
        eventClick: function(info) {
            // Ctrl/Cmd tuşu ile çoklu seçim
            if (info.jsEvent.ctrlKey || info.jsEvent.metaKey) {
                <?php if ($userRole !== 'staff'): ?>
                toggleAppointmentSelection(info.event);
                <?php endif; ?>
            } else {
                showAppointmentModal(info.event);
            }
        },
        eventDidMount: function(info) {
            // Sağ tık menüsü için event listener ekle
            info.el.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                contextMenuEvent = info.event;
                showContextMenu(e.pageX, e.pageY);
            });
            
            // Seçili randevuları görsel olarak işaretle
            if (selectedAppointments.has(info.event.id)) {
                info.el.style.border = '3px solid #3B82F6';
                info.el.style.boxShadow = '0 0 10px rgba(59, 130, 246, 0.5)';
            }
        },
        eventDrop: function(info) {
            updateAppointmentDragDrop(info.event, info.event.start, info.event.end);
        },
        eventResize: function(info) {
            updateAppointmentDragDrop(info.event, info.event.start, info.event.end);
        },
        editable: <?= $userRole !== 'staff' ? 'true' : 'false' ?>,
        selectable: <?= $userRole !== 'staff' ? 'true' : 'false' ?>,
        select: function(info) {
            <?php if ($userRole !== 'staff'): ?>
            const date = info.startStr.split('T')[0]; // Sadece tarih kısmını al
            const time = info.start.toTimeString().slice(0,5); // HH:MM formatında saat
            window.location.href = `/calendar/create?date=${date}&time=${time}`;
            <?php endif; ?>
        }
    });
    
    calendar.render();
}

// Event listener'ları ayarla
function setupEventListeners() {
    // Filtre değişiklikleri
    const branchFilter = document.getElementById('branch-filter');
    const staffFilter = document.getElementById('staff-filter');
    const viewFilter = document.getElementById('view-filter');
    
    if (branchFilter) {
        branchFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    }
    
    if (staffFilter) {
        staffFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    }
    
    if (viewFilter) {
        viewFilter.addEventListener('change', function() {
            calendar.changeView(this.value);
        });
    }
    
    // Modal event listener'ları
    document.getElementById('close-modal').addEventListener('click', hideAppointmentModal);
    document.getElementById('close-modal-btn').addEventListener('click', hideAppointmentModal);
    
    document.getElementById('edit-appointment').addEventListener('click', function() {
        if (currentAppointment) {
            window.location.href = `/calendar/edit/${currentAppointment.id}`;
        }
    });
    
    document.getElementById('delete-appointment').addEventListener('click', function() {
        if (currentAppointment && confirm('Bu randevuyu silmek istediğinizden emin misiniz?')) {
            deleteAppointment(currentAppointment.id);
        }
    });
    
    // Durum güncelleme modal'ı
    document.getElementById('cancel-status').addEventListener('click', hideStatusModal);
    document.getElementById('status-form').addEventListener('submit', updateAppointmentStatus);
}

// Randevu modal'ını göster
function showAppointmentModal(event) {
    currentAppointment = event;
    const props = event.extendedProps;
    
    const content = `
        <div class="space-y-3">
            <div>
                <span class="font-medium text-gray-700">Müşteri:</span>
                <span class="text-gray-900">${props.customer_name}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Telefon:</span>
                <span class="text-gray-900">${props.customer_phone || 'Belirtilmemiş'}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Hizmet:</span>
                <span class="text-gray-900">${props.service_name}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Personel:</span>
                <span class="text-gray-900">${props.staff_name}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Tarih & Saat:</span>
                <span class="text-gray-900">${event.start.toLocaleDateString('tr-TR')} ${event.start.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'})}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Süre:</span>
                <span class="text-gray-900">${props.duration} dakika</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Durum:</span>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusBadgeClass(props.status)}">
                    ${getStatusLabel(props.status)}
                </span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Fiyat:</span>
                <span class="text-gray-900">${props.price} ₺</span>
            </div>
            ${props.notes ? `
            <div>
                <span class="font-medium text-gray-700">Notlar:</span>
                <p class="text-gray-900 mt-1">${props.notes}</p>
            </div>
            ` : ''}
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <button onclick="showStatusModal()" class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-edit mr-1"></i>Durumu Güncelle
            </button>
        </div>
    `;
    
    document.getElementById('modal-content').innerHTML = content;
    document.getElementById('appointment-modal').classList.remove('hidden');
}

// Randevu modal'ını gizle
function hideAppointmentModal() {
    document.getElementById('appointment-modal').classList.add('hidden');
    currentAppointment = null;
}

// Durum modal'ını göster
function showStatusModal() {
    if (currentAppointment) {
        document.getElementById('status-select').value = currentAppointment.extendedProps.status;
        document.getElementById('status-notes').value = '';
        document.getElementById('status-modal').classList.remove('hidden');
    }
}

// Durum modal'ını gizle
function hideStatusModal() {
    document.getElementById('status-modal').classList.add('hidden');
}

// Randevu durumunu güncelle
function updateAppointmentStatus(e) {
    e.preventDefault();
    
    if (!currentAppointment) return;
    
    const formData = new FormData(e.target);
    formData.append('id', currentAppointment.id);
    
    fetch('/calendar/update-status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideStatusModal();
            hideAppointmentModal();
            calendar.refetchEvents();
            showAlert('Randevu durumu güncellendi', 'success');
        } else {
            showAlert(data.message || 'Güncelleme sırasında bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Güncelleme sırasında bir hata oluştu', 'error');
    });
}

// Randevu saatini güncelle (sürükle-bırak)
function updateAppointmentTime(event, newStart, newEnd) {
    const duration = Math.round((newEnd - newStart) / (1000 * 60)); // dakika cinsinden
    
    const formData = new FormData();
    formData.append('id', event.id);
    formData.append('appointment_date', newStart.toISOString().split('T')[0]);
    formData.append('start_time', newStart.toTimeString().slice(0, 8));
    formData.append('duration', duration);
    
    fetch(`/calendar/edit/${event.id}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Randevu saati güncellendi', 'success');
        } else {
            calendar.refetchEvents(); // Hata durumunda eski haline döndür
            showAlert(data.message || 'Güncelleme sırasında bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        calendar.refetchEvents(); // Hata durumunda eski haline döndür
        showAlert('Güncelleme sırasında bir hata oluştu', 'error');
    });
}

// Randevu sil
function deleteAppointment(appointmentId) {
    fetch(`/calendar/delete/${appointmentId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideAppointmentModal();
            calendar.refetchEvents();
            showAlert('Randevu silindi', 'success');
        } else {
            showAlert(data.message || 'Silme sırasında bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Silme sırasında bir hata oluştu', 'error');
    });
}

// Durum etiket sınıfı
function getStatusBadgeClass(status) {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'no_show': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Durum etiketi
function getStatusLabel(status) {
    const labels = {
        'pending': 'Onay Bekliyor',
        'confirmed': 'Onaylandı',
        'completed': 'Tamamlandı',
        'cancelled': 'İptal Edildi',
        'no_show': 'Gelmedi'
    };
    return labels[status] || status;
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

// Gelişmiş sürükle-bırak güncelleme (çakışma kontrolü ile)
function updateAppointmentDragDrop(event, newStart, newEnd) {
    const duration = Math.round((newEnd - newStart) / (1000 * 60)); // dakika cinsinden
    
    const formData = new FormData();
    formData.append('id', event.id);
    formData.append('appointment_date', newStart.toISOString().split('T')[0]);
    formData.append('start_time', newStart.toTimeString().slice(0, 8));
    formData.append('duration', duration);
    
    fetch('/calendar/update-drag-drop', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message || 'Randevu güncellendi', 'success');
        } else {
            calendar.refetchEvents(); // Hata durumunda eski haline döndür
            if (data.conflict) {
                showAlert('Çakışma tespit edildi! ' + data.message, 'error');
            } else {
                showAlert(data.message || 'Güncelleme sırasında bir hata oluştu', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        calendar.refetchEvents(); // Hata durumunda eski haline döndür
        showAlert('Güncelleme sırasında bir hata oluştu', 'error');
    });
}

// Sağ tık menüsü kurulumu
function setupContextMenu() {
    // Sayfa herhangi bir yerine tıklandığında menüyü gizle
    document.addEventListener('click', function() {
        hideContextMenu();
    });
    
    // Context menu item'larına event listener ekle
    document.querySelectorAll('.context-menu-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            const action = this.getAttribute('data-action');
            handleContextMenuAction(action);
            hideContextMenu();
        });
    });
}

// Sağ tık menüsünü göster
function showContextMenu(x, y) {
    const menu = document.getElementById('context-menu');
    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
    menu.classList.remove('hidden');
}

// Sağ tık menüsünü gizle
function hideContextMenu() {
    document.getElementById('context-menu').classList.add('hidden');
}

// Sağ tık menüsü aksiyonlarını işle
function handleContextMenuAction(action) {
    if (!contextMenuEvent) return;
    
    switch (action) {
        case 'edit':
            window.location.href = `/calendar/edit/${contextMenuEvent.id}`;
            break;
        case 'copy':
            showCopyModal();
            break;
        case 'view':
            showAppointmentModal(contextMenuEvent);
            break;
        case 'delete':
            if (confirm('Bu randevuyu silmek istediğinizden emin misiniz?')) {
                deleteAppointment(contextMenuEvent.id);
            }
            break;
        default:
            // Durum değişiklikleri (status-confirmed, status-completed, vb.)
            if (action.startsWith('status-')) {
                const status = action.replace('status-', '');
                quickUpdateStatus(contextMenuEvent.id, status);
            }
    }
}

// Hızlı durum güncelleme
function quickUpdateStatus(appointmentId, status) {
    const formData = new FormData();
    formData.append('id', appointmentId);
    formData.append('status', status);
    
    fetch('/calendar/update-status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            calendar.refetchEvents();
            showAlert('Randevu durumu güncellendi', 'success');
        } else {
            showAlert(data.message || 'Güncelleme sırasında bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Güncelleme sırasında bir hata oluştu', 'error');
    });
}

// Randevu kopyalama modal'ını göster
function showCopyModal() {
    if (contextMenuEvent) {
        currentAppointment = contextMenuEvent;
        document.getElementById('copy-date').value = '';
        document.getElementById('copy-time').value = '';
        document.getElementById('copy-modal').classList.remove('hidden');
    }
}

// Randevu kopyalama modal'ını gizle
function hideCopyModal() {
    document.getElementById('copy-modal').classList.add('hidden');
}

// Randevu kopyalama
function copyAppointment(e) {
    e.preventDefault();
    
    if (!currentAppointment) return;
    
    const formData = new FormData(e.target);
    formData.append('id', currentAppointment.id);
    
    fetch('/calendar/copy-appointment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideCopyModal();
            calendar.refetchEvents();
            showAlert('Randevu başarıyla kopyalandı', 'success');
        } else {
            showAlert(data.message || 'Kopyalama sırasında bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Kopyalama sırasında bir hata oluştu', 'error');
    });
}

// Çoklu seçim işlemleri
function toggleAppointmentSelection(event) {
    if (selectedAppointments.has(event.id)) {
        selectedAppointments.delete(event.id);
    } else {
        selectedAppointments.add(event.id);
    }
    
    updateBulkActionsVisibility();
    calendar.refetchEvents(); // Görsel güncellemeler için
}

// Toplu işlemler arayüzünü güncelle
function updateBulkActionsVisibility() {
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (selectedAppointments.size > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.textContent = selectedAppointments.size;
    } else {
        bulkActions.classList.add('hidden');
    }
}

// Toplu işlemler kurulumu
function setupBulkActions() {
    // Seçimi temizle
    document.getElementById('clear-selection')?.addEventListener('click', function() {
        selectedAppointments.clear();
        updateBulkActionsVisibility();
        calendar.refetchEvents();
    });
    
    // Toplu işlem türü değiştiğinde
    document.getElementById('bulk-action')?.addEventListener('change', function() {
        const bulkValue = document.getElementById('bulk-value');
        const action = this.value;
        
        if (action === 'status') {
            bulkValue.innerHTML = `
                <option value="confirmed">Onaylandı</option>
                <option value="completed">Tamamlandı</option>
                <option value="cancelled">İptal Edildi</option>
                <option value="no_show">Gelmedi</option>
            `;
            bulkValue.classList.remove('hidden');
        } else if (action === 'staff') {
            // Personel listesini yükle
            loadStaffForBulkAction();
            bulkValue.classList.remove('hidden');
        } else {
            bulkValue.classList.add('hidden');
        }
    });
    
    // Toplu işlemi uygula
    document.getElementById('apply-bulk')?.addEventListener('click', function() {
        const action = document.getElementById('bulk-action').value;
        const value = document.getElementById('bulk-value').value;
        
        if (!action) {
            showAlert('Lütfen bir işlem seçin', 'error');
            return;
        }
        
        if ((action === 'status' || action === 'staff') && !value) {
            showAlert('Lütfen bir değer seçin', 'error');
            return;
        }
        
        if (action === 'delete' && !confirm(`${selectedAppointments.size} randevuyu silmek istediğinizden emin misiniz?`)) {
            return;
        }
        
        applyBulkAction(action, value);
    });
    
    // Kopyalama modal event listener'ları
    document.getElementById('cancel-copy')?.addEventListener('click', hideCopyModal);
    document.getElementById('copy-form')?.addEventListener('submit', copyAppointment);
}

// Personel listesini toplu işlemler için yükle
function loadStaffForBulkAction() {
    const branchId = document.getElementById('branch-filter')?.value || '';
    
    fetch(`/calendar/service-staff?branch_id=${branchId}`)
    .then(response => response.json())
    .then(staff => {
        const bulkValue = document.getElementById('bulk-value');
        bulkValue.innerHTML = staff.map(person =>
            `<option value="${person.id}">${person.first_name} ${person.last_name}</option>`
        ).join('');
    })
    .catch(error => {
        console.error('Error loading staff:', error);
        showAlert('Personel listesi yüklenirken hata oluştu', 'error');
    });
}

// Toplu işlemi uygula
function applyBulkAction(action, value) {
    const formData = new FormData();
    formData.append('appointment_ids', JSON.stringify(Array.from(selectedAppointments)));
    formData.append('action', action);
    if (value) formData.append('value', value);
    
    fetch('/calendar/bulk-update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            selectedAppointments.clear();
            updateBulkActionsVisibility();
            calendar.refetchEvents();
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message || 'Toplu işlem sırasında bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Toplu işlem sırasında bir hata oluştu', 'error');
    });
}
</script>

<!-- FullCalendar CSS ve JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/tr.global.min.js"></script>

<?= $this->endSection() ?>