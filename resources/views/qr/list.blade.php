@extends('layouts.app')

@section('title', 'Danh sách sinh viên - QR Code Generator')

@section('content')
<div class="container mx-auto px-0 py-0 lg:py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6 mb-6 lg:mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl lg:text-3xl font-bold text-gray-800">
                        <i class="fas fa-list text-green-600 mr-2 lg:mr-3"></i>
                        Danh sách sinh viên
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm lg:text-base">Quản lý và tải xuống QR code của sinh viên</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-4">
                    <a href="{{ route('qr.download-all') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        <span class="hidden sm:inline">Tải tất cả QR</span>
                        <span class="sm:hidden">Tải tất cả</span>
                    </a>
                    <a href="{{ route('qr.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="hidden sm:inline">Thêm sinh viên</span>
                        <span class="sm:hidden">Thêm</span>
                    </a>
                    <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-home mr-2"></i>
                        <span class="hidden sm:inline">Trang chủ</span>
                        <span class="sm:hidden">Trang chủ</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
            <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="p-2 lg:p-3 rounded-full bg-blue-100 text-blue-600 flex-shrink-0">
                        <i class="fas fa-users text-lg lg:text-xl"></i>
                    </div>
                    <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Tổng sinh viên</p>
                        <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $totalStudents }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="p-2 lg:p-3 rounded-full bg-green-100 text-green-600 flex-shrink-0">
                        <i class="fas fa-qrcode text-lg lg:text-xl"></i>
                    </div>
                    <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Có QR Code</p>
                        <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $studentsWithQR }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center">
                    <div class="p-2 lg:p-3 rounded-full bg-red-100 text-red-600 flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-lg lg:text-xl"></i>
                    </div>
                    <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Chưa có QR</p>
                        <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $studentsWithoutQR }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-gray-200">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-800">Danh sách sinh viên</h2>
            </div>
            
            @if($students->count() > 0)
                <!-- Mobile Card View -->
                <div class="block lg:hidden p-0 space-y-3">
                    @foreach($students as $index => $student)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center min-w-0 flex-1">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">
                                                {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3 min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $student->mssv }}</div>
                                        <div class="text-xs text-gray-600 truncate">{{ $student->name ?? 'Chưa cập nhật' }}</div>
                                    </div>
                                </div>
                                @if($student->qr_code_path)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>Có QR
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i>Chưa có
                                    </span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center text-xs text-gray-600 mb-3">
                                <span>Lớp: {{ $student->class ?? 'Chưa cập nhật' }}</span>
                                <span>{{ $student->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex gap-2">
                                @if($student->qr_code_path)
                                    <a href="{{ route('qr.download', $student->id) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-medium text-center transition duration-200">
                                        <i class="fas fa-download mr-1"></i>Tải QR
                                    </a>
                                @endif
                                <button onclick="viewQR('{{ $student->mssv }}', '{{ $student->qr_code_path }}')" 
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-xs font-medium transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>Xem QR
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
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
                            @foreach($students as $index => $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->mssv }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $student->name ?? 'Chưa cập nhật' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $student->class ?? 'Chưa cập nhật' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($student->qr_code_path)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Có QR
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>Chưa có
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $student->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($student->qr_code_path)
                                            <a href="{{ route('qr.download', $student->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-download mr-1"></i>Tải QR
                                            </a>
                                        @endif
                                        <button onclick="viewQR('{{ $student->mssv }}', '{{ $student->qr_code_path }}')" 
                                                class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-eye mr-1"></i>Xem QR
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-4 lg:px-6 py-3 lg:py-4 border-t border-gray-200">
                    {{ $students->links() }}
                </div>
            @else
                <div class="text-center py-8 lg:py-12 px-4">
                    <i class="fas fa-inbox text-4xl lg:text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-2">Chưa có sinh viên nào</h3>
                    <p class="text-gray-500 mb-6 text-sm lg:text-base">Hãy upload file Excel để tạo QR code cho sinh viên</p>
                    <a href="{{ route('qr.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 text-sm lg:text-base">
                        <i class="fas fa-plus mr-2"></i>Thêm sinh viên
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- QR Code Modal -->
    <div id="qrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 lg:top-20 mx-auto p-4 lg:p-5 border w-11/12 sm:w-96 shadow-lg rounded-xl bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-10 w-10 lg:h-12 lg:w-12 rounded-full bg-green-100 mb-3 lg:mb-4">
                    <i class="fas fa-qrcode text-green-600 text-lg lg:text-xl"></i>
                </div>
                <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-3 lg:mb-4">QR Code</h3>
                <div id="qrCodeContainer" class="mb-3 lg:mb-4">
                    <!-- QR Code will be displayed here -->
                </div>
                <div class="items-center px-2 lg:px-4 py-2 lg:py-3">
                    <button id="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm lg:text-base w-full sm:w-auto">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewQR(mssv, qrPath) {
            const modal = document.getElementById('qrModal');
            const container = document.getElementById('qrCodeContainer');
            
            if (qrPath) {
                // Hiển thị hình ảnh QR code thực tế
                const fileName = qrPath.split('/').pop(); // Lấy tên file từ path
                container.innerHTML = `
                    <div class="text-center">
                        <div class="text-base lg:text-lg font-semibold text-gray-800 mb-2 lg:mb-3">QR Code - ${mssv}</div>
                        <img src="/qr/display/${fileName}" alt="QR Code ${mssv}" class="mx-auto border border-gray-300 rounded-lg shadow-sm w-48 h-48 lg:w-64 lg:h-64 object-contain">
                        <div class="text-xs lg:text-sm text-gray-600 mt-2 lg:mt-3">Mã số sinh viên: ${mssv}</div>
                    </div>
                `;
            } else {
                // Fallback nếu không có QR code
                container.innerHTML = `
                    <div class="bg-gray-100 p-3 lg:p-4 rounded-lg">
                        <div class="text-center">
                            <div class="text-lg lg:text-2xl font-bold text-gray-800 mb-2">${mssv}</div>
                            <div class="text-xs lg:text-sm text-gray-600">Chưa có QR code</div>
                        </div>
                    </div>
                `;
            }
            
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
@endsection
