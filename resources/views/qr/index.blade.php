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
    <div class="container mx-auto px-4 py-4 lg:py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6 mb-6 lg:mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl lg:text-3xl font-bold text-gray-800">
                        <i class="fas fa-qrcode text-blue-600 mr-2 lg:mr-3"></i>
                        QR Code Generator
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm lg:text-base">Tạo mã QR cho sinh viên thủ công hoặc từ file Excel</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-4">
                    <a href="{{ route('qr.list') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-list mr-2"></i>
                        <span class="hidden sm:inline">Danh sách sinh viên</span>
                        <span class="sm:hidden">Danh sách</span>
                    </a>
                    <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-home mr-2"></i>
                        <span class="hidden sm:inline">Trang chủ</span>
                        <span class="sm:hidden">Trang chủ</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Festival Selection -->
        @if($festivals->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6 mb-6 lg:mb-8">
            <h2 class="text-lg lg:text-2xl font-semibold text-gray-800 mb-4 lg:mb-6">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                Chọn Lễ hội
            </h2>
            
            <form action="{{ route('festival.select') }}" method="POST" class="mb-4">
                @csrf
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <label for="festival_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Lễ hội <span class="text-red-500">*</span>
                        </label>
                        <select name="festival_id" id="festival_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">-- Chọn lễ hội --</option>
                            @foreach($festivals as $festival)
                                <option value="{{ $festival->id }}" {{ session('current_festival_id') == $festival->id ? 'selected' : '' }}>
                                    {{ $festival->name }}
                                    @if($festival->isOngoing())
                                        (Đang diễn ra)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-check mr-2"></i>
                            Chọn
                        </button>
                    </div>
                </div>
            </form>
            
            @if(session('current_festival_id'))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <span class="text-blue-800">
                            Đang làm việc với lễ hội: <strong>{{ session('current_festival_name') }}</strong>
                        </span>
                    </div>
                </div>
            @endif
        </div>
        @endif

        <!-- Manual Input Form -->
        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6 mb-6 lg:mb-8">
            <h2 class="text-lg lg:text-2xl font-semibold text-gray-800 mb-4 lg:mb-6">
                <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                Tạo QR Code Thủ Công
            </h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-3 lg:px-4 py-2 lg:py-3 rounded-lg mb-4 lg:mb-6 text-sm lg:text-base">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 lg:px-4 py-2 lg:py-3 rounded-lg mb-4 lg:mb-6 text-sm lg:text-base">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 lg:px-4 py-2 lg:py-3 rounded-lg mb-4 lg:mb-6 text-sm lg:text-base">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('qr.create-manual') }}" method="POST" class="space-y-4 lg:space-y-6">
                @csrf
                
                @if(session('current_festival_id'))
                    <input type="hidden" name="festival_id" value="{{ session('current_festival_id') }}">
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            <span class="text-yellow-800">
                                Vui lòng chọn lễ hội trước khi tạo QR code
                            </span>
                        </div>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <div>
                        <label for="mssv" class="block text-sm lg:text-base font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card text-blue-600 mr-1"></i>
                            MSSV <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="mssv" 
                               name="mssv" 
                               value="{{ old('mssv') }}"
                               class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm lg:text-base"
                               placeholder="Nhập mã số sinh viên"
                               required>
                    </div>
                    
                    <div>
                        <label for="class" class="block text-sm lg:text-base font-medium text-gray-700 mb-2">
                            <i class="fas fa-graduation-cap text-purple-600 mr-1"></i>
                            Lớp
                        </label>
                        <input type="text" 
                               id="class" 
                               name="class" 
                               value="{{ old('class') }}"
                               class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm lg:text-base"
                               placeholder="Nhập lớp">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <div>
                        <label for="holot" class="block text-sm lg:text-base font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-green-600 mr-1"></i>
                            Họ lót
                        </label>
                        <input type="text" 
                               id="holot" 
                               name="holot" 
                               value="{{ old('holot') }}"
                               class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm lg:text-base"
                               placeholder="Nhập họ lót">
                    </div>
                    
                    <div>
                        <label for="ten" class="block text-sm lg:text-base font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-green-600 mr-1"></i>
                            Tên
                        </label>
                        <input type="text" 
                               id="ten" 
                               name="ten" 
                               value="{{ old('ten') }}"
                               class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm lg:text-base"
                               placeholder="Nhập tên">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <div>
                        <label for="gioi" class="block text-sm lg:text-base font-medium text-gray-700 mb-2">
                            <i class="fas fa-venus-mars text-pink-600 mr-1"></i>
                            Giới tính
                        </label>
                        <select id="gioi" 
                                name="gioi" 
                                class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm lg:text-base">
                            <option value="">Chọn giới tính</option>
                            <option value="Nam" {{ old('gioi') == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ old('gioi') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="ngay_sinh" class="block text-sm lg:text-base font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar text-orange-600 mr-1"></i>
                            Ngày sinh
                        </label>
                        <input type="date" 
                               id="ngay_sinh" 
                               name="ngay_sinh" 
                               value="{{ old('ngay_sinh') }}"
                               class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm lg:text-base">
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm lg:text-base font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-green-600 mr-1"></i>
                        Tên đầy đủ (tùy chọn)
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm lg:text-base"
                           placeholder="Nhập tên đầy đủ (nếu khác với họ lót + tên)">
                </div>

                <div class="flex justify-center lg:justify-end">
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 lg:px-8 py-2 lg:py-3 rounded-lg font-medium transition duration-200 text-sm lg:text-base w-full sm:w-auto">
                        <i class="fas fa-qrcode mr-2"></i>
                        Tạo QR Code
                    </button>
                </div>
            </form>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6 mb-6 lg:mb-8">
            <h2 class="text-lg lg:text-2xl font-semibold text-gray-800 mb-4 lg:mb-6">
                <i class="fas fa-upload text-blue-600 mr-2"></i>
                Upload File Excel
            </h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-3 lg:px-4 py-2 lg:py-3 rounded-lg mb-4 lg:mb-6 text-sm lg:text-base">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 lg:px-4 py-2 lg:py-3 rounded-lg mb-4 lg:mb-6 text-sm lg:text-base">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 lg:px-4 py-2 lg:py-3 rounded-lg mb-4 lg:mb-6 text-sm lg:text-base">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('qr.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4 lg:space-y-6">
                @csrf
                
                @if(session('current_festival_id'))
                    <input type="hidden" name="festival_id" value="{{ session('current_festival_id') }}">
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            <span class="text-yellow-800">
                                Vui lòng chọn lễ hội trước khi upload file Excel
                            </span>
                        </div>
                    </div>
                @endif
                
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 lg:p-8 text-center hover:border-blue-400 transition duration-200">
                    <div class="space-y-3 lg:space-y-4">
                        <i class="fas fa-file-excel text-4xl lg:text-6xl text-green-600"></i>
                        <div>
                            <h3 class="text-base lg:text-lg font-medium text-gray-700">Chọn file Excel</h3>
                            <p class="text-gray-500 text-sm lg:text-base">Hỗ trợ định dạng .xlsx, .xls (tối đa 10MB)</p>
                        </div>
                        <input type="file" 
                               name="excel_file" 
                               id="excel_file" 
                               accept=".xlsx,.xls"
                               class="hidden"
                               required>
                        <label for="excel_file" 
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 lg:px-6 py-2 lg:py-3 rounded-lg cursor-pointer transition duration-200 text-sm lg:text-base">
                            <i class="fas fa-folder-open mr-2"></i>
                            Chọn File
                        </label>
                        <p id="file-name" class="text-xs lg:text-sm text-gray-600 hidden"></p>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 lg:p-4">
                    <h4 class="font-medium text-blue-800 mb-2 text-sm lg:text-base">
                        <i class="fas fa-info-circle mr-2"></i>
                        Hướng dẫn sử dụng:
                    </h4>
                    <ul class="text-blue-700 text-xs lg:text-sm space-y-1">
                        <li>• Cột A: Mã số sinh viên (MSSV) - Bắt buộc</li>
                        <li>• Cột B: Họ lót - Tùy chọn</li>
                        <li>• Cột C: Tên - Tùy chọn</li>
                        <li>• Cột D: Giới tính (Nam/Nữ) - Tùy chọn</li>
                        <li>• Cột E: Ngày sinh (dd/mm/yyyy) - Tùy chọn</li>
                        <li>• Hàng đầu tiên sẽ được bỏ qua (header)</li>
                    </ul>
                </div>

                <div class="flex justify-center lg:justify-end">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 lg:px-8 py-2 lg:py-3 rounded-lg font-medium transition duration-200 text-sm lg:text-base w-full sm:w-auto">
                        <i class="fas fa-magic mr-2"></i>
                        Tạo QR Code
                    </button>
                </div>
            </form>
        </div>

        <!-- Features -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
                <div class="text-center">
                    <i class="fas fa-plus-circle text-3xl lg:text-4xl text-green-600 mb-3 lg:mb-4"></i>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-2">Tạo Thủ Công</h3>
                    <p class="text-gray-600 text-xs lg:text-sm">Nhập thông tin sinh viên trực tiếp để tạo QR code</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
                <div class="text-center">
                    <i class="fas fa-file-upload text-3xl lg:text-4xl text-blue-600 mb-3 lg:mb-4"></i>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-2">Upload Excel</h3>
                    <p class="text-gray-600 text-xs lg:text-sm">Upload file Excel chứa danh sách MSSV để tạo QR code hàng loạt</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
                <div class="text-center">
                    <i class="fas fa-qrcode text-3xl lg:text-4xl text-purple-600 mb-3 lg:mb-4"></i>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-2">Tạo QR Code</h3>
                    <p class="text-gray-600 text-xs lg:text-sm">Hệ thống tự động tạo mã QR cho từng MSSV</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
                <div class="text-center">
                    <i class="fas fa-download text-3xl lg:text-4xl text-orange-600 mb-3 lg:mb-4"></i>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-2">Tải xuống</h3>
                    <p class="text-gray-600 text-xs lg:text-sm">Tải xuống từng QR code hoặc tất cả dưới dạng file ZIP</p>
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
