

<?php $__env->startSection('title', 'Trang chủ - FesSuport - VTTU VTTU'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="text-center">
        <!-- Logo VTTU -->
        <div class="mb-8">
            <img src="<?php echo e(asset('Logo-DH-Vo-Truong-Toan-VTTU-288x300.png')); ?>" 
                 alt="Logo Đại học Võ Trường Toản" 
                 class="mx-auto h-64 w-auto md:h-80 lg:h-96 drop-shadow-lg hover:scale-105 transition-transform duration-300">
        </div>
        
        <!-- Tiêu đề chính -->
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 mb-4">
            FesSuport - VTTU
        </h1>
        
        <!-- Phụ đề -->
        <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Hệ thống quét mã QR chuyên nghiệp cho Đại học Võ Trường Toản
        </p>
        
        <!-- Các nút hành động -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <!-- Bảng xếp hạng - accessible to everyone -->
            <a href="<?php echo e(route('qr.leaderboard')); ?>" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl">
                <i class="fas fa-trophy"></i>
                <span>Bảng xếp hạng</span>
            </a>
            
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('group.index')); ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl">
                    <i class="fas fa-users"></i>
                    <span>Quản lý nhóm</span>
                </a>
                
                <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('qr.index')); ?>" 
                       class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl">
                        <i class="fas fa-qrcode"></i>
                        <span>QR Generator</span>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                
                <a href="<?php echo e(route('register')); ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl">
                    <i class="fas fa-user-plus"></i>
                    <span>Đăng ký</span>
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Thông tin bổ sung -->
        <div class="mt-12 text-sm text-gray-500">
            <p>© <?php echo e(date('Y')); ?> Đại học Võ Trường Toản - Tất cả quyền được bảo lưu</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/welcome.blade.php ENDPATH**/ ?>