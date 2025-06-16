<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Yetkisiz Erişim' ?> - BeautyPro</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-500 via-pink-500 to-purple-600 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo ve Başlık -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-white">
                    Yetkisiz Erişim
                </h2>
                <p class="mt-2 text-sm text-pink-100">
                    Bu sayfaya erişim yetkiniz bulunmamaktadır
                </p>
            </div>

            <!-- Mesaj Kutusu -->
            <div class="bg-white rounded-xl shadow-2xl p-8">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="text-center">
                    <div class="mx-auto h-20 w-20 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-ban text-red-500 text-3xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Erişim Reddedildi
                    </h3>
                    
                    <p class="text-gray-600 mb-6">
                        Bu sayfayı görüntülemek için gerekli yetkilere sahip değilsiniz. 
                        Lütfen sistem yöneticinizle iletişime geçiniz.
                    </p>

                    <div class="space-y-3">
                        <?php if (session()->get('is_logged_in')): ?>
                            <a href="/dashboard" 
                               class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                <i class="fas fa-home mr-2"></i>
                                Ana Sayfaya Dön
                            </a>
                        <?php else: ?>
                            <a href="/login" 
                               class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Giriş Yap
                            </a>
                        <?php endif; ?>
                        
                        <button onclick="history.back()" 
                                class="w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Geri Dön
                        </button>
                    </div>
                </div>
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
        // Auto redirect after 10 seconds if user is logged in
        <?php if (session()->get('is_logged_in')): ?>
        setTimeout(function() {
            window.location.href = '/dashboard';
        }, 10000);
        <?php endif; ?>
    </script>
</body>
</html>