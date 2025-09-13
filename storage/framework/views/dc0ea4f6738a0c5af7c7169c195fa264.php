<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sinh viên - QR Code Generator</title>
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
                        <i class="fas fa-list text-green-600 mr-3"></i>
                        Danh sách sinh viên
                    </h1>
                    <p class="text-gray-600 mt-2">Quản lý và tải xuống QR code của sinh viên</p>
                </div>
                <div class="flex space-x-4">
                    <a href="<?php echo e(route('qr.download-all')); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-download mr-2"></i>Tải tất cả QR
                    </a>
                    <a href="<?php echo e(route('qr.index')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Thêm sinh viên
                    </a>
                    <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-home mr-2"></i>Trang chủ
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng sinh viên</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($students->total()); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-qrcode text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Có QR Code</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($students->where('qr_code_path', '!=', null)->count()); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Hôm nay</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($students->where('created_at', '>=', today())->count()); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-calendar text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tuần này</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($students->where('created_at', '>=', now()->startOfWeek())->count()); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Danh sách sinh viên</h2>
            </div>
            
            <?php if($students->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-hashtag mr-1"></i>STT
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-id-card mr-1"></i>MSSV
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i>Tên sinh viên
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-graduation-cap mr-1"></i>Lớp
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-qrcode mr-1"></i>QR Code
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-calendar mr-1"></i>Ngày tạo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-cogs mr-1"></i>Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e(($students->currentPage() - 1) * $students->perPage() + $index + 1); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($student->mssv); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo e($student->name ?? 'Chưa cập nhật'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo e($student->class ?? 'Chưa cập nhật'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($student->qr_code_path): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Có QR
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>Chưa có
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($student->created_at->format('d/m/Y H:i')); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if($student->qr_code_path): ?>
                                            <a href="<?php echo e(route('qr.download', $student->id)); ?>" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-download mr-1"></i>Tải QR
                                            </a>
                                        <?php endif; ?>
                                        <button onclick="viewQR('<?php echo e($student->mssv); ?>')" 
                                                class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-eye mr-1"></i>Xem QR
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <?php echo e($students->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có sinh viên nào</h3>
                    <p class="text-gray-500 mb-6">Hãy upload file Excel để tạo QR code cho sinh viên</p>
                    <a href="<?php echo e(route('qr.index')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Thêm sinh viên
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div id="qrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-qrcode text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">QR Code</h3>
                <div id="qrCodeContainer" class="mb-4">
                    <!-- QR Code will be displayed here -->
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewQR(mssv) {
            const modal = document.getElementById('qrModal');
            const container = document.getElementById('qrCodeContainer');
            
            // Create QR code using a simple API or library
            // For demo purposes, we'll show the MSSV
            container.innerHTML = `
                <div class="bg-gray-100 p-4 rounded-lg">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-800 mb-2">${mssv}</div>
                        <div class="text-sm text-gray-600">Mã số sinh viên</div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('qrModal').classList.add('hidden');
        });

        // Close modal when clicking outside
        document.getElementById('qrModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/qr/list.blade.php ENDPATH**/ ?>