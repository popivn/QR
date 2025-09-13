<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator - VTTU</title>
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
                        <i class="fas fa-qrcode text-blue-600 mr-3"></i>
                        QR Code Generator
                    </h1>
                    <p class="text-gray-600 mt-2">Tạo mã QR cho sinh viên từ file Excel</p>
                </div>
                <div class="flex space-x-4">
                    <a href="<?php echo e(route('qr.list')); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-list mr-2"></i>Danh sách sinh viên
                    </a>
                    <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-home mr-2"></i>Trang chủ
                    </a>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                <i class="fas fa-upload text-blue-600 mr-2"></i>
                Upload File Excel
            </h2>
            
            <?php if(session('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <ul class="list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('qr.upload')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition duration-200">
                    <div class="space-y-4">
                        <i class="fas fa-file-excel text-6xl text-green-600"></i>
                        <div>
                            <h3 class="text-lg font-medium text-gray-700">Chọn file Excel</h3>
                            <p class="text-gray-500">Hỗ trợ định dạng .xlsx, .xls (tối đa 10MB)</p>
                        </div>
                        <input type="file" 
                               name="excel_file" 
                               id="excel_file" 
                               accept=".xlsx,.xls"
                               class="hidden"
                               required>
                        <label for="excel_file" 
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg cursor-pointer transition duration-200">
                            <i class="fas fa-folder-open mr-2"></i>
                            Chọn File
                        </label>
                        <p id="file-name" class="text-sm text-gray-600 hidden"></p>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Hướng dẫn sử dụng:
                    </h4>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Cột đầu tiên (A): Mã số sinh viên (MSSV)</li>
                        <li>• Cột thứ hai (B): Tên sinh viên (tùy chọn)</li>
                        <li>• Cột thứ ba (C): Lớp (tùy chọn)</li>
                        <li>• Hàng đầu tiên sẽ được bỏ qua (header)</li>
                    </ul>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-magic mr-2"></i>
                        Tạo QR Code
                    </button>
                </div>
            </form>
        </div>

        <!-- Features -->
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <i class="fas fa-file-upload text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Upload Excel</h3>
                    <p class="text-gray-600 text-sm">Upload file Excel chứa danh sách MSSV để tạo QR code hàng loạt</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <i class="fas fa-qrcode text-4xl text-green-600 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Tạo QR Code</h3>
                    <p class="text-gray-600 text-sm">Hệ thống tự động tạo mã QR cho từng MSSV trong file Excel</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <i class="fas fa-download text-4xl text-purple-600 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Tải xuống</h3>
                    <p class="text-gray-600 text-sm">Tải xuống từng QR code hoặc tất cả dưới dạng file ZIP</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File input handling
        document.getElementById('excel_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const fileNameElement = document.getElementById('file-name');
            
            if (fileName) {
                fileNameElement.textContent = 'File đã chọn: ' + fileName;
                fileNameElement.classList.remove('hidden');
            } else {
                fileNameElement.classList.add('hidden');
            }
        });

        // Drag and drop functionality
        const dropZone = document.querySelector('.border-dashed');
        
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('excel_file').files = files;
                document.getElementById('file-name').textContent = 'File đã chọn: ' + files[0].name;
                document.getElementById('file-name').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/qr/index.blade.php ENDPATH**/ ?>