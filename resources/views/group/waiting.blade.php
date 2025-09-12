<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chờ Phân Công - QR Scan System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-clock mr-2 text-yellow-600"></i>
                        Chờ Phân Công
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Xin chào, <span class="font-semibold text-blue-600">{{ $user->name }}</span>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-2xl mx-auto">
            <!-- Waiting Card -->
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="mb-6">
                    <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hourglass-half text-4xl text-yellow-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        Vui lòng chờ Admin phân công
                    </h2>
                    <p class="text-gray-600 text-lg">
                        Tài khoản của bạn chưa được phân vào nhóm nào. 
                        Vui lòng chờ Admin phân công để có thể sử dụng hệ thống.
                    </p>
                </div>

                <!-- User Info -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Thông tin tài khoản
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Họ tên:</label>
                            <p class="text-gray-900 font-semibold">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email:</label>
                            <p class="text-gray-900 font-semibold">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Vai trò:</label>
                            <p class="text-gray-900 font-semibold">
                                @if($user->isAdmin())
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm">Admin</span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">User</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Trạng thái:</label>
                            <p class="text-gray-900 font-semibold">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm">
                                    <i class="fas fa-clock mr-1"></i>
                                    Chờ phân công
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        Hướng dẫn
                    </h3>
                    <div class="text-left space-y-2 text-blue-700">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                            <span>Admin sẽ phân bạn vào một nhóm phù hợp</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                            <span>Sau khi được phân nhóm, bạn có thể quét QR code</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                            <span>Bạn sẽ nhận được thông báo khi được phân công</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-3">
                        <i class="fas fa-phone mr-2"></i>
                        Liên hệ hỗ trợ
                    </h3>
                    <p class="text-green-700 mb-2">
                        Nếu bạn cần hỗ trợ hoặc có thắc mắc, vui lòng liên hệ:
                    </p>
                    <div class="text-green-800">
                        <p><i class="fas fa-envelope mr-2"></i> Email: admin@vttu.edu.vn</p>
                        <p><i class="fas fa-phone mr-2"></i> Hotline: 0123-456-789</p>
                    </div>
                </div>
            </div>

            <!-- Auto Refresh Notice -->
            <div class="bg-white rounded-lg shadow-md p-4 mt-6">
                <div class="flex items-center justify-center text-gray-600">
                    <i class="fas fa-sync-alt mr-2 text-blue-600"></i>
                    <span>Trang sẽ tự động làm mới mỗi 30 giây để kiểm tra trạng thái phân công</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);

        // Show refresh countdown
        let countdown = 30;
        const countdownElement = document.createElement('div');
        countdownElement.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-3 py-2 rounded-lg shadow-lg';
        countdownElement.innerHTML = `<i class="fas fa-sync-alt mr-1"></i>Làm mới sau: <span id="countdown">${countdown}</span>s`;
        document.body.appendChild(countdownElement);

        const countdownInterval = setInterval(function() {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                countdownElement.innerHTML = '<i class="fas fa-sync-alt mr-1"></i>Đang làm mới...';
            }
        }, 1000);
    </script>
</body>
</html>
