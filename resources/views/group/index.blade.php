<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Management - VTTU</title>
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
                        <i class="fas fa-users text-blue-600 mr-3"></i>
                        Group Management
                    </h1>
                    <p class="text-gray-600 mt-2">Quản lý nhóm và thành viên</p>
                </div>
                <div class="flex space-x-4">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('group.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Tạo nhóm
                        </a>
                        <a href="{{ route('group.list') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-list mr-2"></i>Danh sách nhóm
                        </a>
                        <a href="{{ route('group.users') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-user mr-2"></i>Quản lý user
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                        </button>
                    </form>
                    <a href="/" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-home mr-2"></i>Trang chủ
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Current Group Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            @if($group)
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Thông tin nhóm hiện tại
                </h2>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h1 class="text-3xl font-bold text-blue-800 mb-2">Bạn Đang Ở {{ $group->name }}</h1>
                    @if($group->description)
                        <p class="text-blue-700 mb-4">{{ $group->description }}</p>
                    @endif
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-users text-2xl text-blue-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-600">Thành viên</p>
                                    <p class="text-xl font-semibold">{{ $group->users->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-2xl text-green-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-600">Ngày tạo</p>
                                    <p class="text-xl font-semibold">{{ $group->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-user-tag text-2xl text-purple-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-600">Vai trò</p>
                                    <p class="text-xl font-semibold">{{ auth()->user()->isAdmin() ? 'Admin' : 'User' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Bạn chưa thuộc nhóm nào</h3>
                    <p class="text-gray-500 mb-6">Liên hệ admin để được thêm vào nhóm</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('group.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Tạo nhóm
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Group Members (if user is in a group) -->
        @if($group && $group->users->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-users text-green-600 mr-2"></i>
                    Thành viên trong nhóm
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($group->users as $member)
                        <div class="bg-gray-50 rounded-lg p-4 border">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">{{ $member->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $member->email }}</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $member->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $member->isAdmin() ? 'Admin' : 'User' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-3 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <i class="fas fa-users text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Quản lý nhóm</h3>
                    <p class="text-gray-600 text-sm mb-4">Tạo và quản lý các nhóm trong hệ thống</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('group.list') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Xem danh sách
                        </a>
                    @else
                        <p class="text-sm text-gray-500">Chỉ admin mới có quyền</p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <i class="fas fa-user-plus text-4xl text-green-600 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Quản lý user</h3>
                    <p class="text-gray-600 text-sm mb-4">Thêm user vào nhóm và phân quyền</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('group.users') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Quản lý user
                        </a>
                    @else
                        <p class="text-sm text-gray-500">Chỉ admin mới có quyền</p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <i class="fas fa-qrcode text-4xl text-purple-600 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">QR Code Generator</h3>
                    <p class="text-gray-600 text-sm mb-4">Tạo mã QR cho sinh viên</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('qr.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Tạo QR Code
                        </a>
                    @else
                        <p class="text-sm text-gray-500">Chỉ admin mới có quyền</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
