<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Giriş Yap' ?> - BeautyPro</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-600 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo ve Başlık -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-spa text-pink-500 text-2xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-white">
                    BeautyPro Yönetim
                </h2>
                <p class="mt-2 text-sm text-pink-100">
                    Hesabınıza giriş yapın
                </p>
            </div>

            <!-- Giriş Formu -->
            <div class="bg-white rounded-xl shadow-2xl p-8">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($validation)): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <p class="flex items-center mb-1 last:mb-0">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <?= $error ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" action="<?= site_url('/login') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1"></i>
                            E-posta Adresi
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 focus:z-10 sm:text-sm transition-colors"
                            placeholder="ornek@email.com"
                            value="<?= old('email') ?>"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>
                            Şifre
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="appearance-none rounded-lg relative block w-full px-3 py-3 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 focus:z-10 sm:text-sm transition-colors"
                                placeholder="••••••••"
                            >
                            <button 
                                type="button" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                onclick="togglePassword()"
                            >
                                <i id="password-icon" class="fas fa-eye text-gray-400 hover:text-gray-600 transition-colors"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember-me" 
                                name="remember-me" 
                                type="checkbox" 
                                class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded"
                            >
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                Beni hatırla
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-pink-600 hover:text-pink-500 transition-colors">
                                Şifremi unuttum
                            </a>
                        </div>
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all duration-200 transform hover:scale-105"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-pink-300 group-hover:text-pink-200"></i>
                            </span>
                            Giriş Yap
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-pink-100 text-sm">
                    © <?= date('Y') ?> BeautyPro. Tüm hakları saklıdır.
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Lütfen tüm alanları doldurunuz.');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Şifre en az 6 karakter olmalıdır.');
                return false;
            }
        });

        // Auto focus on email field
        document.getElementById('email').focus();
    </script>
</body>
</html>