

<?php $__env->startSection('title', 'Group Management - VTTU'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mobile-space-y-4">
        <div>
            <h1 class="mobile-text-2xl md:text-3xl font-bold text-gray-800">
                <i class="fas fa-users text-blue-600 mr-3"></i>
                Group Management
            </h1>
            <p class="text-gray-600 mt-2 mobile-text-sm">Quản lý nhóm và thành viên</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 mobile-space-y-2">
            <?php if(auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('group.create')); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                    <i class="fas fa-plus mr-2"></i>Tạo nhóm
                </a>
                <a href="<?php echo e(route('group.list')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                    <i class="fas fa-list mr-2"></i>Danh sách nhóm
                </a>
                <a href="<?php echo e(route('group.users')); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                    <i class="fas fa-user mr-2"></i>Quản lý user
                </a>
            <?php endif; ?>
            <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                <i class="fas fa-home mr-2"></i>Trang chủ
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <!-- Current Group Info -->
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6 mobile-card">
        <?php if($group): ?>
            <h2 class="mobile-text-xl md:text-2xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Thông tin nhóm hiện tại
            </h2>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 md:p-6">
                <h1 class="mobile-text-2xl md:text-3xl font-bold text-blue-800 mb-2">Bạn Đang Ở <?php echo e($group->name); ?></h1>
                <?php if($group->description): ?>
                    <p class="text-blue-700 mb-4 mobile-text-sm"><?php echo e($group->description); ?></p>
                <?php endif; ?>
                <div class="grid mobile-grid md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-users text-xl md:text-2xl text-blue-600 mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Thành viên</p>
                                <p class="mobile-text-lg md:text-xl font-semibold"><?php echo e($group->users->count()); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-xl md:text-2xl text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Ngày tạo</p>
                                <p class="mobile-text-lg md:text-xl font-semibold"><?php echo e($group->created_at->format('d/m/Y')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-user-tag text-xl md:text-2xl text-purple-600 mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Vai trò</p>
                                <p class="mobile-text-lg md:text-xl font-semibold"><?php echo e(auth()->user()->isAdmin() ? 'Admin' : 'User'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-8 md:py-12">
                <i class="fas fa-users text-4xl md:text-6xl text-gray-300 mb-4"></i>
                <h3 class="mobile-text-base md:text-lg font-medium text-gray-900 mb-2">Bạn chưa thuộc nhóm nào</h3>
                <p class="text-gray-500 mb-6 mobile-text-sm">Liên hệ admin để được thêm vào nhóm</p>
                <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('group.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg smooth-transition touch-button">
                        <i class="fas fa-plus mr-2"></i>Tạo nhóm
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Group Members (if user is in a group) -->
    <?php if($group && $group->users->count() > 0): ?>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6 mobile-card">
            <h3 class="mobile-text-lg md:text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-users text-green-600 mr-2"></i>
                Thành viên trong nhóm
            </h3>
            <div class="grid mobile-grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $group->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-gray-50 rounded-lg p-4 border">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold mobile-text-sm">
                                <?php echo e(substr($member->name, 0, 1)); ?>

                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="font-medium text-gray-900 mobile-text-sm truncate"><?php echo e($member->name); ?></p>
                                <p class="text-sm text-gray-600 truncate"><?php echo e($member->email); ?></p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($member->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'); ?>">
                                    <?php echo e($member->isAdmin() ? 'Admin' : 'User'); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="grid mobile-grid md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mobile-card">
            <div class="text-center">
                <i class="fas fa-users text-3xl md:text-4xl text-blue-600 mb-4"></i>
                <h3 class="mobile-text-base md:text-lg font-semibold text-gray-800 mb-2">Quản lý nhóm</h3>
                <p class="text-gray-600 mobile-text-sm mb-4">Tạo và quản lý các nhóm trong hệ thống</p>
                <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('group.list')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button">
                        Xem danh sách
                    </a>
                <?php else: ?>
                    <p class="mobile-text-sm text-gray-500">Chỉ admin mới có quyền</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mobile-card">
            <div class="text-center">
                <i class="fas fa-user-plus text-3xl md:text-4xl text-green-600 mb-4"></i>
                <h3 class="mobile-text-base md:text-lg font-semibold text-gray-800 mb-2">Quản lý user</h3>
                <p class="text-gray-600 mobile-text-sm mb-4">Thêm user vào nhóm và phân quyền</p>
                <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('group.users')); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button">
                        Quản lý user
                    </a>
                <?php else: ?>
                    <p class="mobile-text-sm text-gray-500">Chỉ admin mới có quyền</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mobile-card">
            <div class="text-center">
                <i class="fas fa-qrcode text-3xl md:text-4xl text-purple-600 mb-4"></i>
                <h3 class="mobile-text-base md:text-lg font-semibold text-gray-800 mb-2">QR Code Generator</h3>
                <p class="text-gray-600 mobile-text-sm mb-4">Tạo mã QR cho sinh viên</p>
                <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('qr.index')); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button">
                        Tạo QR Code
                    </a>
                <?php else: ?>
                    <p class="mobile-text-sm text-gray-500">Chỉ admin mới có quyền</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- QR Scanner Card -->
        <?php if($group): ?>
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mobile-card">
                <div class="text-center">
                    <i class="fas fa-camera text-3xl md:text-4xl text-blue-600 mb-4"></i>
                    <h3 class="mobile-text-base md:text-lg font-semibold text-gray-800 mb-2">QR Code Scanner</h3>
                    <p class="text-gray-600 mobile-text-sm mb-4">Quét mã QR của sinh viên</p>
                    <div class="space-y-2">
                        <a href="<?php echo e(route('qr.scanner')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button block">
                            <i class="fas fa-qrcode mr-2"></i>
                            Quét QR Code
                        </a>
                        <a href="<?php echo e(route('qr.statistics', $group->id)); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button block">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Xem thống kê
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/group/index.blade.php ENDPATH**/ ?>