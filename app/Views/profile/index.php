<?= $this->extend('layouts/app') ?>

<?= $this->section('head') ?>
<style>
    .profile-card {
        transition: all 0.3s ease;
    }
    .profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <!-- Başlık -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profil Ayarları</h1>
        <p class="mt-2 text-gray-600">Kişisel bilgilerinizi ve hesap ayarlarınızı yönetin</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sol Taraf - Profil Bilgileri -->
        <div class="lg:col-span-2">
            <div class="profile-card bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Kişisel Bilgiler</h3>
                    <p class="mt-1 text-sm text-gray-500">Profil bilgilerinizi güncelleyin</p>
                </div>
                
                <form action="<?= base_url('profile/update') ?>" method="POST" class="p-6">
                    <?= csrf_field() ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ad -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">Ad</label>
                            <input type="text" name="first_name" id="first_name" 
                                   value="<?= old('first_name', $user['first_name']) ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                                   required>
                            <?php if (isset($errors['first_name'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= $errors['first_name'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Soyad -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Soyad</label>
                            <input type="text" name="last_name" id="last_name" 
                                   value="<?= old('last_name', $user['last_name']) ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                                   required>
                            <?php if (isset($errors['last_name'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= $errors['last_name'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- E-posta -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">E-posta</label>
                            <input type="email" name="email" id="email" 
                                   value="<?= old('email', $user['email']) ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                                   required>
                            <?php if (isset($errors['email'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= $errors['email'] ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Telefon -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                            <input type="tel" name="phone" id="phone" 
                                   value="<?= old('phone', $user['phone']) ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                                   required>
                            <?php if (isset($errors['phone'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= $errors['phone'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                class="bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Bilgileri Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sağ Taraf - Hızlı İşlemler -->
        <div class="space-y-6">
            <!-- Şifre Değiştir -->
            <div class="profile-card bg-white shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-key text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">Şifre Değiştir</h3>
                            <p class="text-sm text-gray-500">Hesap güvenliğinizi artırın</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= base_url('profile/change-password') ?>" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors inline-block text-center">
                            Şifre Değiştir
                        </a>
                    </div>
                </div>
            </div>


            <!-- Bildirimler -->
            <div class="profile-card bg-white shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bell text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">Bildirimler</h3>
                            <p class="text-sm text-gray-500">Bildirim geçmişinizi görün</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= base_url('profile/notifications') ?>" 
                           class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition-colors inline-block text-center">
                            Bildirimleri Görüntüle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hesap Bilgileri -->
            <div class="profile-card bg-white shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Hesap Bilgileri</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Rol:</span>
                            <span class="font-medium text-gray-900"><?= ucfirst($user['role_name']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Şube:</span>
                            <span class="font-medium text-gray-900"><?= $user['branch_name'] ?? 'Tüm Şubeler' ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Üyelik Tarihi:</span>
                            <span class="font-medium text-gray-900"><?= date('d.m.Y', strtotime($user['created_at'])) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Son Güncelleme:</span>
                            <span class="font-medium text-gray-900"><?= date('d.m.Y H:i', strtotime($user['updated_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Form validasyonu
    document.querySelector('form').addEventListener('submit', function(e) {
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();

        if (!firstName || !lastName || !email || !phone) {
            e.preventDefault();
            alert('Lütfen tüm alanları doldurun.');
            return false;
        }

        // E-posta formatı kontrolü
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Lütfen geçerli bir e-posta adresi girin.');
            return false;
        }
    });
</script>
<?= $this->endSection() ?>