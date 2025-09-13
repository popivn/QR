

<?php $__env->startSection('title', 'Chỉnh sửa nhóm - ' . $group->name); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li><a href="<?php echo e(route('group.index')); ?>" class="hover:text-blue-600">Quản lý nhóm</a></li>
    <li><i class="fas fa-chevron-right text-gray-400"></i></li>
    <li><a href="<?php echo e(route('group.show', $group->id)); ?>" class="hover:text-blue-600"><?php echo e($group->name); ?></a></li>
    <li><i class="fas fa-chevron-right text-gray-400"></i></li>
    <li class="text-gray-800">Chỉnh sửa</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mobile-space-y-4">
        <div>
            <h1 class="mobile-text-2xl md:text-3xl font-bold text-gray-800">
                <i class="fas fa-edit text-green-600 mr-3"></i>
                Chỉnh sửa nhóm: <?php echo e($group->name); ?>

            </h1>
            <p class="text-gray-600 mt-2 mobile-text-sm">Cập nhật thông tin nhóm</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 mobile-space-y-2">
            <a href="<?php echo e(route('group.show', $group->id)); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                <i class="fas fa-eye mr-2"></i>Xem chi tiết
            </a>
            <a href="<?php echo e(route('group.list')); ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mobile-card">
        <?php if($errors->any()): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <ul class="list-disc list-inside mobile-text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('group.update', $group->id)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag mr-2"></i>Tên nhóm *
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="<?php echo e(old('name', $group->name)); ?>"
                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent touch-button mobile-text-sm"
                       placeholder="Nhập tên nhóm..."
                       required>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2"></i>Mô tả
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mobile-text-sm"
                          placeholder="Nhập mô tả cho nhóm..."><?php echo e(old('description', $group->description)); ?></textarea>
            </div>

            <!-- Group Stats -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-800 mb-3 mobile-text-sm">
                    <i class="fas fa-chart-bar mr-2"></i>Thống kê nhóm
                </h4>
                <div class="grid mobile-grid md:grid-cols-3 gap-4">
                    <div class="text-center bg-white rounded-lg p-3">
                        <p class="mobile-text-xl md:text-2xl font-bold text-blue-600"><?php echo e($group->users->count()); ?></p>
                        <p class="mobile-text-sm text-gray-600">Thành viên</p>
                    </div>
                    <div class="text-center bg-white rounded-lg p-3">
                        <p class="mobile-text-xl md:text-2xl font-bold text-green-600"><?php echo e($group->users->where('role_id', 1)->count()); ?></p>
                        <p class="mobile-text-sm text-gray-600">Admin</p>
                    </div>
                    <div class="text-center bg-white rounded-lg p-3">
                        <p class="mobile-text-xl md:text-2xl font-bold text-purple-600"><?php echo e($group->users->where('role_id', 2)->count()); ?></p>
                        <p class="mobile-text-sm text-gray-600">User</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-end mobile-space-y-2">
                <a href="<?php echo e(route('group.show', $group->id)); ?>" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg smooth-transition touch-button text-center">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg smooth-transition touch-button">
                    <i class="fas fa-save mr-2"></i>Cập nhật
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/group/edit.blade.php ENDPATH**/ ?>