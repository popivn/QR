

<?php $__env->startSection('title', 'Admin - Nhóm - VTTU'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-3xl font-bold text-gray-800">
                <i class="fas fa-users-cog text-blue-600 mr-3"></i>
                Nhóm - Admin
            </h1>
            <p class="text-gray-600 mt-1 text-sm lg:text-base">Quản lý tất cả các nhóm trong hệ thống</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
            <a href="<?php echo e(route('group.create')); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>
                Tạo nhóm mới
            </a>
            <a href="<?php echo e(route('group.users')); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                <i class="fas fa-user mr-2"></i>
                Quản lý user
            </a>
            <a href="<?php echo e(route('qr.index')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                <i class="fas fa-qrcode mr-2"></i>
                QR Code
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 lg:space-y-8">
    <!-- Tổng quan thống kê -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-5">
        <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-xs lg:text-sm text-gray-500 uppercase tracking-wide mb-2 lg:mb-3">Tổng nhóm</h3>
            <p class="text-2xl lg:text-3xl font-bold text-blue-600"><?php echo e($groups->count()); ?></p>
        </div>
        
        <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-xs lg:text-sm text-gray-500 uppercase tracking-wide mb-2 lg:mb-3">Tổng user</h3>
            <p class="text-2xl lg:text-3xl font-bold text-green-600"><?php echo e($groups->sum(function($group) { return $group->users->count(); })); ?></p>
        </div>
        
        <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-xs lg:text-sm text-gray-500 uppercase tracking-wide mb-2 lg:mb-3">Admin</h3>
            <p class="text-2xl lg:text-3xl font-bold text-red-600"><?php echo e(\App\Models\User::where('role_id', 1)->count()); ?></p>
        </div>
        
        <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-xs lg:text-sm text-gray-500 uppercase tracking-wide mb-2 lg:mb-3">User thường</h3>
            <p class="text-2xl lg:text-3xl font-bold text-purple-600"><?php echo e(\App\Models\User::where('role_id', 2)->count()); ?></p>
        </div>
    </div>

    <!-- Danh sách nhóm -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 p-4 lg:p-6">
            <h2 class="text-lg lg:text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 lg:mr-3 text-blue-600"></i>
                Danh sách tất cả nhóm
            </h2>
            <p class="text-gray-600 mt-2 text-xs lg:text-sm">
                Quản lý và theo dõi tất cả các nhóm trong hệ thống
            </p>
        </div>
        
        <?php if($groups->count() > 0): ?>
            <div class="divide-y divide-gray-100">
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 lg:p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <!-- Thông tin nhóm -->
                            <div class="flex-1">
                                <div class="flex items-start gap-3 lg:gap-4">
                                    <div class="w-12 h-12 lg:w-14 lg:h-14 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-users text-blue-600 text-lg lg:text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-1"><?php echo e($group->name); ?></h3>
                                        <?php if($group->description): ?>
                                            <p class="text-xs lg:text-sm text-gray-600 mb-2"><?php echo e($group->description); ?></p>
                                        <?php endif; ?>
                                        <div class="flex flex-wrap gap-2 lg:gap-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-users mr-1"></i>
                                                <?php echo e($group->users->count()); ?> thành viên
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-calendar mr-1"></i>
                                                <?php echo e($group->created_at->format('d/m/Y')); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
                                <a href="<?php echo e(route('group.show', $group->id)); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded-lg text-xs lg:text-sm font-medium transition-colors whitespace-nowrap text-center">
                                    <i class="fas fa-eye mr-1 lg:mr-2"></i>
                                    Xem chi tiết
                                </a>
                                <a href="<?php echo e(route('group.edit', $group->id)); ?>" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 lg:px-4 py-2 rounded-lg text-xs lg:text-sm font-medium transition-colors whitespace-nowrap text-center">
                                    <i class="fas fa-edit mr-1 lg:mr-2"></i>
                                    Chỉnh sửa
                                </a>
                                <a href="<?php echo e(route('qr.statistics', $group->id)); ?>" class="bg-green-600 hover:bg-green-700 text-white px-3 lg:px-4 py-2 rounded-lg text-xs lg:text-sm font-medium transition-colors whitespace-nowrap text-center">
                                    <i class="fas fa-chart-bar mr-1 lg:mr-2"></i>
                                    Thống kê
                                </a>
                                <form action="<?php echo e(route('group.destroy', $group->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 lg:px-4 py-2 rounded-lg text-xs lg:text-sm font-medium transition-colors whitespace-nowrap w-full sm:w-auto">
                                        <i class="fas fa-trash mr-1 lg:mr-2"></i>
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Thành viên trong nhóm (preview) -->
                        <?php if($group->users->count() > 0): ?>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h4 class="text-xs lg:text-sm font-medium text-gray-700 mb-2">Thành viên:</h4>
                                <div class="flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $group->users->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                            <i class="fas fa-user mr-1"></i>
                                            <?php echo e($user->name); ?>

                                            <?php if($user->isAdmin()): ?>
                                                <span class="ml-1 text-red-600">(Admin)</span>
                                            <?php endif; ?>
                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($group->users->count() > 5): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-200 text-gray-600">
                                            +<?php echo e($group->users->count() - 5); ?> khác
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 lg:py-16 px-4 text-gray-500">
                <i class="fas fa-users text-4xl lg:text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg lg:text-xl font-semibold mb-2">Chưa có nhóm nào</h3>
                <p class="text-sm lg:text-base mb-6">Hãy tạo nhóm đầu tiên để bắt đầu quản lý</p>
                <a href="<?php echo e(route('group.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tạo nhóm đầu tiên
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions - Collapsible -->
    <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
        <button onclick="toggleAdminQuickActions()" class="w-full flex items-center justify-between text-left">
            <h2 class="text-lg lg:text-2xl font-bold text-gray-800">
                <i class="fas fa-bolt text-yellow-600 mr-2 lg:mr-3"></i>
                Chức năng nhanh
            </h2>
            <i id="adminQuickActionsIcon" class="fas fa-chevron-down text-gray-500 transition-transform duration-200"></i>
        </button>
        <div id="adminQuickActionsContent" class="hidden mt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
                    <i class="fas fa-qrcode text-3xl lg:text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-2">QR Code Generator</h3>
                    <p class="text-gray-600 text-sm lg:text-base mb-4">Tạo mã QR cho sinh viên</p>
                    <a href="<?php echo e(route('qr.index')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-qrcode mr-2"></i>
                        Tạo QR Code
                    </a>
                </div>
                
                <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
                    <i class="fas fa-chart-line text-3xl lg:text-4xl text-green-600 mb-4"></i>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-2">Bảng xếp hạng</h3>
                    <p class="text-gray-600 text-sm lg:text-base mb-4">Xem xếp hạng các nhóm</p>
                    <a href="<?php echo e(route('qr.leaderboard')); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-trophy mr-2"></i>
                        Xem xếp hạng
                    </a>
                </div>
                
                <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
                    <i class="fas fa-history text-3xl lg:text-4xl text-purple-600 mb-4"></i>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-2">Audit Log</h3>
                    <p class="text-gray-600 text-sm lg:text-base mb-4">Xem lịch sử hoạt động</p>
                    <a href="<?php echo e(route('audit.index')); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-history mr-2"></i>
                        Xem log
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.toggleAdminQuickActions = function() {
        const content = document.getElementById('adminQuickActionsContent');
        const icon = document.getElementById('adminQuickActionsIcon');
        
        if (content && icon) {
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    };
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/group/admin-index.blade.php ENDPATH**/ ?>