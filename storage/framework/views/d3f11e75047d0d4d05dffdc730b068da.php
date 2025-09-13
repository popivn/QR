<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Đăng nhập - VTTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-lg shadow-md p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-sign-in-alt text-blue-600 mr-2"></i>
                    Đăng nhập
                </h2>
                <p class="text-gray-600">Đăng nhập vào hệ thống VTTU</p>
            </div>

            <!-- Login Form -->
            <form class="mt-8 space-y-6" action="<?php echo e(route('login')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <?php if($errors->any()): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               value="<?php echo e(old('email')); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nhập email của bạn"
                               required>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Mật khẩu
                        </label>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nhập mật khẩu"
                               required>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Đăng nhập
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Chưa có tài khoản? 
                        <a href="<?php echo e(route('register')); ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                            Đăng ký ngay
                        </a>
                    </p>
                </div>
            </form>

            <!-- Demo Accounts -->
            
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/auth/login.blade.php ENDPATH**/ ?>