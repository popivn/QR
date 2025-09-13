@extends('layouts.app')

@section('title', 'Chờ Phân Công - FesSuport - VTTU')

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mobile-space-y-4">
        <div>
            <h1 class="mobile-text-xl md:text-2xl font-bold text-gray-800">
                <i class="fas fa-clock mr-2 text-yellow-600"></i>
                Chờ Phân Công
            </h1>
            <p class="text-gray-600 mt-1 mobile-text-sm">
                Xin chào, <span class="font-semibold text-blue-600">{{ $user->name }}</span>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 mobile-space-y-2">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')

    <!-- Main Content -->
    <div class="max-w-2xl mx-auto">
        <!-- Waiting Card -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-8 text-center mobile-card">
            <div class="mb-6">
                <div class="w-16 h-16 md:w-24 md:h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hourglass-half text-2xl md:text-4xl text-yellow-600"></i>
                </div>
                <h2 class="mobile-text-xl md:text-2xl font-bold text-gray-800 mb-2">
                    Vui lòng chờ Admin phân công
                </h2>
                <p class="text-gray-600 mobile-text-sm md:text-lg">
                    Tài khoản của bạn chưa được phân vào nhóm nào. 
                    Vui lòng chờ Admin phân công để có thể sử dụng hệ thống.
                </p>
            </div>

            <!-- User Info -->
            <div class="bg-gray-50 rounded-lg p-4 md:p-6 mb-6">
                <h3 class="mobile-text-base md:text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Thông tin tài khoản
                </h3>
                <div class="grid mobile-grid md:grid-cols-2 gap-4 text-left">
                    <div class="bg-white rounded-lg p-3">
                        <label class="block text-sm font-medium text-gray-600">Họ tên:</label>
                        <p class="text-gray-900 font-semibold mobile-text-sm">{{ $user->name }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <label class="block text-sm font-medium text-gray-600">Email:</label>
                        <p class="text-gray-900 font-semibold mobile-text-sm break-all">{{ $user->email }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <label class="block text-sm font-medium text-gray-600">Vai trò:</label>
                        <p class="text-gray-900 font-semibold">
                            @if($user->isAdmin())
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full mobile-text-sm">Admin</span>
                            @else
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full mobile-text-sm">User</span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <label class="block text-sm font-medium text-gray-600">Trạng thái:</label>
                        <p class="text-gray-900 font-semibold">
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full mobile-text-sm">
                                <i class="fas fa-clock mr-1"></i>
                                Chờ phân công
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 md:p-6 mb-6">
                <h3 class="mobile-text-base md:text-lg font-semibold text-blue-800 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    Hướng dẫn
                </h3>
                <div class="text-left space-y-2 text-blue-700 mobile-text-sm">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mt-1 mr-2 flex-shrink-0"></i>
                        <span>Admin sẽ phân bạn vào một nhóm phù hợp</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mt-1 mr-2 flex-shrink-0"></i>
                        <span>Sau khi được phân nhóm, bạn có thể quét QR code</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mt-1 mr-2 flex-shrink-0"></i>
                        <span>Bạn sẽ nhận được thông báo khi được phân công</span>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 md:p-6">
                <h3 class="mobile-text-base md:text-lg font-semibold text-green-800 mb-3">
                    <i class="fas fa-phone mr-2"></i>
                    Liên hệ hỗ trợ
                </h3>
                <p class="text-green-700 mb-2 mobile-text-sm">
                    Nếu bạn cần hỗ trợ hoặc có thắc mắc, vui lòng liên hệ:
                </p>
                <div class="text-green-800 mobile-text-sm">
                    <p class="break-all"><i class="fas fa-envelope mr-2"></i> Email: admin@vttu.edu.vn</p>
                    <p><i class="fas fa-phone mr-2"></i> Hotline: 0123-456-789</p>
                </div>
            </div>
        </div>

        <!-- Auto Refresh Notice -->
        <div class="bg-white rounded-lg shadow-md p-4 mt-6 mobile-card">
            <div class="flex items-center justify-center text-gray-600 mobile-text-sm">
                <i class="fas fa-sync-alt mr-2 text-blue-600"></i>
                <span>Trang sẽ tự động làm mới mỗi 30 giây để kiểm tra trạng thái phân công</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto refresh every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);

    // Show refresh countdown
    let countdown = 30;
    const countdownElement = document.createElement('div');
    countdownElement.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-3 py-2 rounded-lg shadow-lg mobile-text-sm';
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
@endpush
