document.addEventListener('DOMContentLoaded', function() {
    // Sihirbaz değişkenleri
    let currentStep = 1;
    const totalSteps = 4;
    let selectedCustomer = null;
    let selectedService = null;
    let selectedStaff = null;
    
    // DOM elementleri
    const steps = document.querySelectorAll('.step-content');
    const nextBtn = document.getElementById('next-step');
    const prevBtn = document.getElementById('prev-step');
    const submitBtn = document.getElementById('submit-btn');
    
    // Müşteri arama elementleri
    const customerSearch = document.getElementById('customer-search');
    const customerSearchResults = document.getElementById('customer-search-results');
    const selectedCustomerDiv = document.getElementById('selected-customer');
    const clearCustomerBtn = document.getElementById('clear-customer');
    
    // Yeni müşteri ekleme elementleri
    const addNewCustomerBtn = document.getElementById('add-new-customer');
    const newCustomerInputs = {
        firstName: document.getElementById('new-customer-first-name'),
        lastName: document.getElementById('new-customer-last-name'),
        phone: document.getElementById('new-customer-phone'),
        email: document.getElementById('new-customer-email')
    };
    
    // Hizmet seçimi elementleri
    const serviceSelect = document.getElementById('service_id');
    const customerPackagesList = document.getElementById('customer-packages-list');
    const selectedServiceDetails = document.getElementById('selected-service-details');
    
    // Personel seçimi elementleri
    const staffSelection = document.getElementById('staff-selection');
    
    // Tarih/saat elementleri
    const appointmentDate = document.getElementById('appointment_date');
    const startTime = document.getElementById('start_time');
    const availableTimeSlots = document.getElementById('available-time-slots');
    const recurringCheckbox = document.getElementById('recurring-appointment');
    const recurringOptions = document.getElementById('recurring-options');

    // Event Listeners
    nextBtn.addEventListener('click', nextStep);
    prevBtn.addEventListener('click', prevStep);
    clearCustomerBtn.addEventListener('click', clearSelectedCustomer);
    addNewCustomerBtn.addEventListener('click', addNewCustomer);
    serviceSelect.addEventListener('change', onServiceChange);
    appointmentDate.addEventListener('change', loadAvailableTimeSlots);
    recurringCheckbox.addEventListener('change', function() {
        recurringOptions.classList.toggle('hidden', !this.checked);
    });

    // Sihirbaz navigasyonu
    function updateStepIndicators() {
        for (let i = 1; i <= totalSteps; i++) {
            const indicator = document.getElementById(`step-${i}-indicator`);
            const circle = indicator.querySelector('div');
            const text = indicator.querySelector('span');
            const line = document.getElementById(`line-${i}`);
            
            if (i < currentStep) {
                // Tamamlanmış adım
                circle.className = 'flex items-center justify-center w-8 h-8 rounded-full bg-green-600 text-white text-sm font-medium';
                circle.innerHTML = '<i class="fas fa-check"></i>';
                text.className = 'ml-2 text-sm font-medium text-green-600';
                if (line) line.className = 'flex-1 h-0.5 bg-green-600';
            } else if (i === currentStep) {
                // Aktif adım
                circle.className = 'flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-medium';
                circle.textContent = i;
                text.className = 'ml-2 text-sm font-medium text-gray-900';
                if (line) line.className = 'flex-1 h-0.5 bg-gray-200';
            } else {
                // Gelecek adım
                circle.className = 'flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-gray-600 text-sm font-medium';
                circle.textContent = i;
                text.className = 'ml-2 text-sm font-medium text-gray-500';
                if (line) line.className = 'flex-1 h-0.5 bg-gray-200';
            }
        }
    }
    
    function showStep(step) {
        steps.forEach((stepDiv, index) => {
            stepDiv.classList.toggle('hidden', index + 1 !== step);
        });
        
        // Buton görünürlüğü
        prevBtn.classList.toggle('hidden', step === 1);
        nextBtn.classList.toggle('hidden', step === totalSteps);
        submitBtn.classList.toggle('hidden', step !== totalSteps);
        
        updateStepIndicators();
    }
    
    function nextStep() {
        if (validateCurrentStep()) {
            currentStep++;
            showStep(currentStep);
            loadStepData();
        }
    }
    
    function prevStep() {
        currentStep--;
        showStep(currentStep);
    }
    
    function validateCurrentStep() {
        switch (currentStep) {
            case 1:
                if (!selectedCustomer) {
                    showAlert('Lütfen bir müşteri seçin veya yeni müşteri ekleyin.', 'warning');
                    return false;
                }
                break;
            case 2:
                if (!selectedService && !document.getElementById('customer_package_id').value) {
                    showAlert('Lütfen bir hizmet veya paket seçin.', 'warning');
                    return false;
                }
                break;
            case 3:
                if (!selectedStaff) {
                    showAlert('Lütfen bir personel seçin.', 'warning');
                    return false;
                }
                break;
            case 4:
                if (!appointmentDate.value || !startTime.value) {
                    showAlert('Lütfen tarih ve saat seçin.', 'warning');
                    return false;
                }
                break;
        }
        return true;
    }
    
    function loadStepData() {
        switch (currentStep) {
            case 2:
                loadCustomerPackages();
                break;
            case 3:
                loadAvailableStaff();
                break;
            case 4:
                loadAvailableTimeSlots();
                break;
        }
    }

    // Müşteri arama
    let searchTimeout;
    customerSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            customerSearchResults.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchCustomers(query);
        }, 300);
    });
    
    function searchCustomers(query) {
        const branchId = document.getElementById('branch_id')?.value || window.userBranchId;
        
        fetch(`/calendar/search-customers?q=${encodeURIComponent(query)}&branch_id=${branchId}`)
            .then(response => response.json())
            .then(customers => {
                displaySearchResults(customers);
            })
            .catch(error => {
                console.error('Müşteri arama hatası:', error);
                showAlert('Müşteri arama sırasında hata oluştu.', 'error');
            });
    }
    
    function displaySearchResults(customers) {
        if (customers.length === 0) {
            customerSearchResults.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-search text-2xl mb-2"></i>
                    <p>Müşteri bulunamadı</p>
                </div>
            `;
        } else {
            customerSearchResults.innerHTML = customers.map(customer => `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                     onclick="selectCustomer(${customer.id}, '${customer.first_name}', '${customer.last_name}', '${customer.phone}', '${customer.email || ''}')">
                    <div class="font-medium text-gray-900">${customer.first_name} ${customer.last_name}</div>
                    <div class="text-sm text-gray-600">${customer.phone}</div>
                    ${customer.email ? `<div class="text-sm text-gray-500">${customer.email}</div>` : ''}
                </div>
            `).join('');
        }
        
        customerSearchResults.classList.remove('hidden');
    }
    
    window.selectCustomer = function(id, firstName, lastName, phone, email) {
        selectedCustomer = { id, firstName, lastName, phone, email };
        
        document.getElementById('selected-customer-name').textContent = `${firstName} ${lastName}`;
        document.getElementById('selected-customer-phone').textContent = phone;
        document.getElementById('customer_id').value = id;
        
        selectedCustomerDiv.classList.remove('hidden');
        customerSearchResults.classList.add('hidden');
        customerSearch.value = '';
        
        // Yeni müşteri formunu temizle
        clearNewCustomerForm();
    };
    
    function clearSelectedCustomer() {
        selectedCustomer = null;
        selectedCustomerDiv.classList.add('hidden');
        document.getElementById('customer_id').value = '';
        customerSearch.value = '';
    }
    
    function clearNewCustomerForm() {
        Object.values(newCustomerInputs).forEach(input => input.value = '');
    }
    
    function addNewCustomer() {
        const firstName = newCustomerInputs.firstName.value.trim();
        const lastName = newCustomerInputs.lastName.value.trim();
        const phone = newCustomerInputs.phone.value.trim();
        const email = newCustomerInputs.email.value.trim();
        
        if (!firstName || !lastName || !phone) {
            showAlert('Ad, soyad ve telefon alanları zorunludur.', 'warning');
            return;
        }
        
        const branchId = document.getElementById('branch_id')?.value || window.userBranchId;
        
        const formData = new FormData();
        formData.append('branch_id', branchId);
        formData.append('first_name', firstName);
        formData.append('last_name', lastName);
        formData.append('phone', phone);
        formData.append('email', email);
        
        addNewCustomerBtn.disabled = true;
        addNewCustomerBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Ekleniyor...';
        
        fetch('/calendar/quick-add-customer', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectCustomer(data.customer.id, data.customer.first_name, data.customer.last_name, data.customer.phone, data.customer.email || '');
                showAlert('Müşteri başarıyla eklendi.', 'success');
            } else {
                showAlert(data.message || 'Müşteri eklenirken hata oluştu.', 'error');
            }
        })
        .catch(error => {
            console.error('Müşteri ekleme hatası:', error);
            showAlert('Müşteri ekleme sırasında hata oluştu.', 'error');
        })
        .finally(() => {
            addNewCustomerBtn.disabled = false;
            addNewCustomerBtn.innerHTML = '<i class="fas fa-plus mr-2"></i>Müşteri Ekle';
        });
    }
    
    // Müşteri paketlerini yükle
    function loadCustomerPackages() {
        if (!selectedCustomer) return;
        
        customerPackagesList.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
                <p class="text-gray-600">Paketler yükleniyor...</p>
            </div>
        `;
        
        fetch(`/calendar/customer-packages?customer_id=${selectedCustomer.id}`)
            .then(response => response.json())
            .then(packages => {
                displayCustomerPackages(packages);
            })
            .catch(error => {
                console.error('Paket yükleme hatası:', error);
                customerPackagesList.innerHTML = `
                    <div class="text-center py-4 text-red-600">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Paketler yüklenirken hata oluştu</p>
                    </div>
                `;
            });
    }
    
    function displayCustomerPackages(packages) {
        if (packages.length === 0) {
            customerPackagesList.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-3xl mb-2"></i>
                    <p>Aktif paket bulunamadı</p>
                </div>
            `;
        } else {
            customerPackagesList.innerHTML = packages.map(pkg => `
                <div class="border border-gray-200 rounded-lg p-4 mb-3 cursor-pointer hover:border-blue-500 transition-colors package-option"
                     data-package-id="${pkg.id}" data-real-package-id="${pkg.package_id}" data-type="${pkg.type}">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium text-gray-900">${pkg.name}</h4>
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Aktif</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        ${pkg.type === 'session'
                            ? `Kalan: ${pkg.remaining_sessions} seans`
                            : `Kalan: ${pkg.remaining_minutes} dakika`
                        }
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Son kullanım: ${new Date(pkg.expiry_date).toLocaleDateString('tr-TR')}
                    </div>
                </div>
            `).join('');
            
            // Paket seçimi event listener'ları
            document.querySelectorAll('.package-option').forEach(option => {
                option.addEventListener('click', function() {
                    selectPackage(this.dataset.packageId, this.dataset.type);
                });
            });
        }
    }
    
    function selectPackage(customerPackageId, type) {
        // Diğer seçimleri temizle
        document.querySelectorAll('.package-option').forEach(option => {
            option.classList.remove('border-blue-500', 'bg-blue-50');
        });
        serviceSelect.value = '';
        selectedServiceDetails.classList.add('hidden');
        
        // Seçilen paketi vurgula
        const selectedOption = document.querySelector(`[data-package-id="${customerPackageId}"]`);
        selectedOption.classList.add('border-blue-500', 'bg-blue-50');
        
        // Paket ID'sini data attribute'dan al
        const packageId = selectedOption.dataset.realPackageId;
        
        // Form alanlarını doldur
        document.getElementById('customer_package_id').value = customerPackageId;
        document.getElementById('duration').value = type === 'session' ? '60' : '30'; // Varsayılan değerler
        document.getElementById('price').value = '0'; // Paket kullanımı ücretsiz
        
        selectedService = { isPackage: true, packageId: packageId, customerPackageId: customerPackageId };
    }
    
    function onServiceChange() {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        
        if (serviceSelect.value) {
            // Paket seçimini temizle
            document.querySelectorAll('.package-option').forEach(option => {
                option.classList.remove('border-blue-500', 'bg-blue-50');
            });
            document.getElementById('customer_package_id').value = '';
            
            // Hizmet detaylarını göster
            const duration = selectedOption.dataset.duration;
            const price = selectedOption.dataset.price;
            
            document.getElementById('service-duration').textContent = duration;
            document.getElementById('service-price').textContent = price;
            document.getElementById('duration').value = duration;
            document.getElementById('price').value = price;
            
            selectedServiceDetails.classList.remove('hidden');
            selectedService = { 
                id: serviceSelect.value, 
                duration: duration, 
                price: price,
                isPackage: false 
            };
        } else {
            selectedServiceDetails.classList.add('hidden');
            selectedService = null;
        }
    }
    
    // Personel yükleme
    function loadAvailableStaff() {
        if (!selectedService) return;
        
        staffSelection.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
                <p class="text-gray-600">Personeller yükleniyor...</p>
            </div>
        `;
        
        const branchId = document.getElementById('branch_id')?.value || window.userBranchId;
        let url = `/calendar/suggested-staff?branch_id=${branchId}`;
        
        if (selectedService.isPackage) {
            // Paket seçildi
            url += `&package_id=${selectedService.packageId}`;
        } else {
            // Normal hizmet seçildi
            url += `&service_id=${selectedService.id}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(staff => {
                displayAvailableStaff(staff);
            })
            .catch(error => {
                console.error('Personel yükleme hatası:', error);
                staffSelection.innerHTML = `
                    <div class="text-center py-4 text-red-600">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Personeller yüklenirken hata oluştu</p>
                    </div>
                `;
            });
    }
    
    function displayAvailableStaff(staff) {
        if (staff.length === 0) {
            staffSelection.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-user-times text-3xl mb-2"></i>
                    <p>Bu hizmet için uygun personel bulunamadı</p>
                </div>
            `;
        } else {
            staffSelection.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    ${staff.map(person => `
                        <div class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors staff-option ${person.available === false ? 'opacity-50' : ''}" 
                             data-staff-id="${person.id}" ${person.available === false ? 'data-unavailable="true"' : ''}>
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">${person.first_name} ${person.last_name}</h4>
                                    <p class="text-sm text-gray-600">${person.role_name}</p>
                                </div>
                            </div>
                            ${person.available === false ? 
                                '<div class="text-xs text-red-600"><i class="fas fa-clock mr-1"></i>Seçilen saatte müsait değil</div>' : 
                                '<div class="text-xs text-green-600"><i class="fas fa-check mr-1"></i>Müsait</div>'
                            }
                        </div>
                    `).join('')}
                </div>
            `;
            
            // Personel seçimi event listener'ları
            document.querySelectorAll('.staff-option').forEach(option => {
                option.addEventListener('click', function() {
                    if (!this.dataset.unavailable) {
                        selectStaff(this.dataset.staffId);
                    }
                });
            });
        }
    }
    
    function selectStaff(staffId) {
        // Diğer seçimleri temizle
        document.querySelectorAll('.staff-option').forEach(option => {
            option.classList.remove('border-blue-500', 'bg-blue-50');
        });
        
        // Seçilen personeli vurgula
        const selectedOption = document.querySelector(`[data-staff-id="${staffId}"]`);
        selectedOption.classList.add('border-blue-500', 'bg-blue-50');
        
        document.getElementById('staff_id').value = staffId;
        selectedStaff = { id: staffId };
    }
    
    // Uygun saatleri yükle
    function loadAvailableTimeSlots() {
        if (!selectedStaff || !appointmentDate.value) return;
        
        const duration = document.getElementById('duration').value;
        if (!duration) return;
        
        availableTimeSlots.innerHTML = `
            <div class="col-span-3 text-center py-4">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
                <p class="text-gray-600">Uygun saatler yükleniyor...</p>
            </div>
        `;
        
        fetch(`/calendar/available-time-slots?staff_id=${selectedStaff.id}&date=${appointmentDate.value}&duration=${duration}`)
            .then(response => response.json())
            .then(timeSlots => {
                displayAvailableTimeSlots(timeSlots);
            })
            .catch(error => {
                console.error('Saat yükleme hatası:', error);
                availableTimeSlots.innerHTML = `
                    <div class="col-span-3 text-center py-4 text-red-600">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Uygun saatler yüklenirken hata oluştu</p>
                    </div>
                `;
            });
    }
    
    function displayAvailableTimeSlots(timeSlots) {
        if (timeSlots.length === 0) {
            availableTimeSlots.innerHTML = `
                <div class="col-span-3 text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-times text-3xl mb-2"></i>
                    <p>Bu tarihte uygun saat bulunamadı</p>
                </div>
            `;
        } else {
            availableTimeSlots.innerHTML = timeSlots.map(slot => `
                <button type="button" class="time-slot-btn p-2 text-sm border border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors" 
                        data-time="${slot.time}">
                    ${slot.label}
                </button>
            `).join('');
            
            // Saat seçimi event listener'ları
            document.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectTimeSlot(this.dataset.time);
                });
            });
        }
    }
    
    function selectTimeSlot(time) {
        // Diğer seçimleri temizle
        document.querySelectorAll('.time-slot-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700');
        });
        
        // Seçilen saati vurgula
        const selectedBtn = document.querySelector(`[data-time="${time}"]`);
        selectedBtn.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700');
        
        startTime.value = time;
    }
    
    // Yardımcı fonksiyonlar
    function showAlert(message, type = 'info') {
        const alertColors = {
            success: 'bg-green-50 border-green-200 text-green-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };
        
        const alertIcons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 z-50 ${alertColors[type]} border rounded-lg p-4 shadow-lg max-w-sm`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <i class="${alertIcons[type]} mr-3"></i>
                <span>${message}</span>
                <button type="button" class="ml-auto text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        // 5 saniye sonra otomatik kaldır
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // İlk adımı göster
    showStep(1);
});