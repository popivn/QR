

<?php $__env->startSection('title', 'Đăng ký - VTTU'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-lg shadow-md p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-user-plus text-green-600 mr-2"></i>
                    Đăng ký
                </h2>
                <p class="text-gray-600">Tạo tài khoản mới</p>
            </div>

            <!-- Register Form -->
            <form class="mt-8 space-y-6" action="<?php echo e(route('register')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <?php if($errors->any()): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <ul class="list-disc list-inside">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Họ và tên
                        </label>
                        <input id="name" 
                               name="name" 
                               type="text" 
                               value="<?php echo e(old('name')); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nhập họ và tên"
                               required>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Tên đăng nhập
                        </label>
                        <input id="username" 
                               name="username" 
                               type="text" 
                               value="<?php echo e(old('username')); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nhập tên đăng nhập"
                               required>
                    </div>

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
                               placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)"
                               required>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Xác nhận mật khẩu
                        </label>
                        <input id="password_confirmation" 
                               name="password_confirmation" 
                               type="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nhập lại mật khẩu"
                               required>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>
                        Đăng ký
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Đã có tài khoản? 
                        <a href="<?php echo e(route('login')); ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                            Đăng nhập ngay
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/auth/register.blade.php ENDPATH**/ ?>