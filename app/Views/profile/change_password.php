<?= $this->extend('layouts/app') ?>

<?= $this->section('head') ?>
<style>
    .password-strength {
        height: 4px;
        border-radius: 2px;
        transition: all 0.3s ease;
    }
    .strength-weak { background-color: #ef4444; }
    .strength-medium { background-color: #f59e0b; }
    .strength-strong { background-color: #10b981; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto">
    <!-- Başlık -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Şifre Değiştir</h1>
        <p class="mt-2 text-gray-600">Hesap güvenliğiniz için güçlü bir şifre seçin</p>
    </div>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?= session()->getFlashdata('success') ?></span>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
    </div>
    <?php endif; ?>

    <!-- Şifre Değiştirme Formu -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Şifre Güncelleme</h3>
            <p class="mt-1 text-sm text-gray-500">Mevcut şifrenizi girin ve yeni şifrenizi belirleyin</p>
        </div>
        
        <form action="<?= base_url('profile/change-password') ?>" method="POST" class="p-6" id="passwordForm">
            <?= csrf_field() ?>
            
            <div class="space-y-6">
                <!-- Mevcut Şifre -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">
                        Mevcut Şifre
                    </label>
                    <div class="mt-1 relative">
                        <input type="password" name="current_password" id="current_password" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm pr-10"
                               required>
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('current_password')">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="current_password_icon"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['current_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['current_password'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Yeni Şifre -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">
                        Yeni Şifre
                    </label>
                    <div class="mt-1 relative">
                        <input type="password" name="new_password" id="new_password" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm pr-10"
                               required minlength="6">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="new_password_icon"></i>
                        </button>
                    </div>
                    <!-- Şifre Gücü Göstergesi -->
                    <div class="mt-2">
                        <div class="password-strength w-full bg-gray-200" id="passwordStrength"></div>
                        <p class="mt-1 text-xs text-gray-500" id="passwordStrengthText">En az 6 karakter olmalıdır</p>
                    </div>
                    <?php if (isset($errors['new_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['new_password'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Şifre Tekrarı -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                        Yeni Şifre (Tekrar)
                    </label>
                    <div class="mt-1 relative">
                        <input type="password" name="confirm_password" id="confirm_password" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm pr-10"
                               required>
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="confirm_password_icon"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500" id="passwordMatchText"></p>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['confirm_password'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Şifre Güvenlik İpuçları -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Güçlü Şifre İpuçları</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>En az 8 karakter kullanın</li>
                                <li>Büyük ve küçük harfleri karıştırın</li>
                                <li>Sayılar ve özel karakterler ekleyin</li>
                                <li>Kişisel bilgilerinizi kullanmayın</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="mt-6 flex items-center justify-between">
                <a href="<?= base_url('profile') ?>" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Geri Dön
                </a>
                <button type="submit" 
                        class="bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-md transition-colors"
                        id="submitBtn" disabled>
                    <i class="fas fa-key mr-2"></i>
                    Şifreyi Değiştir
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Şifre görünürlüğü toggle
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Şifre gücü kontrolü
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];

        if (password.length >= 8) strength += 1;
        else feedback.push('En az 8 karakter');

        if (/[a-z]/.test(password)) strength += 1;
        else feedback.push('Küçük harf');

        if (/[A-Z]/.test(password)) strength += 1;
        else feedback.push('Büyük harf');

        if (/[0-9]/.test(password)) strength += 1;
        else feedback.push('Sayı');

        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        else feedback.push('Özel karakter');

        return { strength, feedback };
    }

    // Şifre gücü göstergesi güncelleme
    function updatePasswordStrength() {
        const password = document.getElementById('new_password').value;
        const strengthBar = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('passwordStrengthText');
        
        if (password.length === 0) {
            strengthBar.className = 'password-strength w-full bg-gray-200';
            strengthText.textContent = 'En az 6 karakter olmalıdır';
            return;
        }

        const { strength, feedback } = checkPasswordStrength(password);
        
        if (strength <= 2) {
            strengthBar.className = 'password-strength w-full strength-weak';
            strengthText.textContent = 'Zayıf şifre. Eksik: ' + feedback.join(', ');
        } else if (strength <= 3) {
            strengthBar.className = 'password-strength w-full strength-medium';
            strengthText.textContent = 'Orta güçte şifre. Eksik: ' + feedback.join(', ');
        } else {
            strengthBar.className = 'password-strength w-full strength-strong';
            strengthText.textContent = 'Güçlü şifre!';
        }
    }

    // Şifre eşleşme kontrolü
    function checkPasswordMatch() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const matchText = document.getElementById('passwordMatchText');
        const submitBtn = document.getElementById('submitBtn');
        
        if (confirmPassword.length === 0) {
            matchText.textContent = '';
            submitBtn.disabled = true;
            return;
        }

        if (newPassword === confirmPassword) {
            matchText.textContent = '✓ Şifreler eşleşiyor';
            matchText.className = 'mt-1 text-xs text-green-600';
            submitBtn.disabled = false;
        } else {
            matchText.textContent = '✗ Şifreler eşleşmiyor';
            matchText.className = 'mt-1 text-xs text-red-600';
            submitBtn.disabled = true;
        }
    }

    // Event listener'lar
    document.getElementById('new_password').addEventListener('input', function() {
        updatePasswordStrength();
        checkPasswordMatch();
    });

    document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

    // Form validasyonu
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (!currentPassword || !newPassword || !confirmPassword) {
            e.preventDefault();
            alert('Lütfen tüm alanları doldurun.');
            return false;
        }

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Yeni şifreler eşleşmiyor.');
            return false;
        }

        if (newPassword.length < 6) {
            e.preventDefault();
            alert('Yeni şifre en az 6 karakter olmalıdır.');
            return false;
        }
    });
</script>
<?= $this->endSection() ?>