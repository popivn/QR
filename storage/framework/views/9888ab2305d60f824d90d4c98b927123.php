<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết nhóm - <?php echo e($group->name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-eye text-blue-600 mr-3"></i>
                        Chi tiết nhóm: <?php echo e($group->name); ?>

                    </h1>
                    <p class="text-gray-600 mt-2">Thông tin chi tiết và thành viên của nhóm</p>
                </div>
                <div class="flex space-x-4">
                    <a href="<?php echo e(route('group.edit', $group->id)); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                    </a>
                    <a href="<?php echo e(route('group.list')); ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>

        <!-- Group Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Thông tin nhóm
            </h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên nhóm:</label>
                    <p class="text-lg font-semibold text-gray-900"><?php echo e($group->name); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số thành viên:</label>
                    <p class="text-lg font-semibold text-gray-900"><?php echo e($group->users->count()); ?> thành viên</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả:</label>
                    <p class="text-gray-900"><?php echo e($group->description ?? 'Không có mô tả'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày tạo:</label>
                    <p class="text-gray-900"><?php echo e($group->created_at->format('d/m/Y H:i:s')); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cập nhật lần cuối:</label>
                    <p class="text-gray-900"><?php echo e($group->updated_at->format('d/m/Y H:i:s')); ?></p>
                </div>
            </div>
        </div>

        <!-- Members -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-users text-green-600 mr-2"></i>
                Thành viên trong nhóm
            </h3>
            
            <?php if($group->users->count() > 0): ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $group->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gray-50 rounded-lg p-4 border">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                    <?php echo e(substr($member->name, 0, 1)); ?>

                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="font-medium text-gray-900"><?php echo e($member->name); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo e($member->email); ?></p>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($member->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'); ?>">
                                            <i class="fas fa-<?php echo e($member->isAdmin() ? 'crown' : 'user'); ?> mr-1"></i>
                                            <?php echo e($member->isAdmin() ? 'Admin' : 'User'); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Nhóm này chưa có thành viên nào</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/group/show.blade.php ENDPATH**/ ?>