@extends('layouts.app')

@section('title', 'Danh sách nhóm - VTTU')

@section('breadcrumb')
    <li><a href="{{ route('group.index') }}" class="hover:text-blue-600">Quản lý nhóm</a></li>
    <li><i class="fas fa-chevron-right text-gray-400"></i></li>
    <li class="text-gray-800">Danh sách nhóm</li>
@endsection

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mobile-space-y-4">
        <div>
            <h1 class="mobile-text-2xl md:text-3xl font-bold text-gray-800">
                <i class="fas fa-list text-blue-600 mr-3"></i>
                Danh sách nhóm
            </h1>
            <p class="text-gray-600 mt-2 mobile-text-sm">Quản lý tất cả các nhóm trong hệ thống</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 mobile-space-y-2">
            <a href="{{ route('group.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                <i class="fas fa-plus mr-2"></i>Tạo nhóm mới
            </a>
            <a href="{{ route('group.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg smooth-transition touch-button text-center">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div>
@endsection

@section('content')

    <!-- Groups List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mobile-card">
        @if($groups->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-hashtag mr-1"></i>STT
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-tag mr-1"></i>Tên nhóm
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-align-left mr-1"></i>Mô tả
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-users mr-1"></i>Thành viên
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
                        @foreach($groups as $index => $group)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($groups->currentPage() - 1) * $groups->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $group->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        {{ $group->description ?? 'Không có mô tả' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-users mr-1"></i>{{ $group->users->count() }} thành viên
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $group->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('group.show', $group->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye mr-1"></i>Xem
                                    </a>
                                    <a href="{{ route('group.edit', $group->id) }}" 
                                       class="text-green-600 hover:text-green-900 mr-3">
                                        <i class="fas fa-edit mr-1"></i>Sửa
                                    </a>
                                    <form action="{{ route('group.destroy', $group->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash mr-1"></i>Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden">
                @foreach($groups as $index => $group)
                    <div class="border-b border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900 mobile-text-base">{{ $group->name }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-users mr-1"></i>{{ $group->users->count() }}
                            </span>
                        </div>
                        @if($group->description)
                            <p class="text-gray-600 mobile-text-sm mb-3">{{ $group->description }}</p>
                        @endif
                        <p class="text-gray-500 mobile-text-sm mb-3">
                            <i class="fas fa-calendar mr-1"></i>{{ $group->created_at->format('d/m/Y H:i') }}
                        </p>
                        <div class="flex space-x-2">
                            <a href="{{ route('group.show', $group->id) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg smooth-transition touch-button text-center mobile-text-sm">
                                <i class="fas fa-eye mr-1"></i>Xem
                            </a>
                            <a href="{{ route('group.edit', $group->id) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg smooth-transition touch-button text-center mobile-text-sm">
                                <i class="fas fa-edit mr-1"></i>Sửa
                            </a>
                            <form action="{{ route('group.destroy', $group->id) }}" 
                                  method="POST" 
                                  class="flex-1"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg smooth-transition touch-button mobile-text-sm">
                                    <i class="fas fa-trash mr-1"></i>Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-4 md:px-6 py-4 border-t border-gray-200">
                {{ $groups->links() }}
            </div>
        @else
            <div class="text-center py-8 md:py-12">
                <i class="fas fa-users text-4xl md:text-6xl text-gray-300 mb-4"></i>
                <h3 class="mobile-text-base md:text-lg font-medium text-gray-900 mb-2">Chưa có nhóm nào</h3>
                <p class="text-gray-500 mb-6 mobile-text-sm">Hãy tạo nhóm đầu tiên để bắt đầu</p>
                <a href="{{ route('group.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg smooth-transition touch-button">
                    <i class="fas fa-plus mr-2"></i>Tạo nhóm đầu tiên
                </a>
            </div>
        @endif
    </div>
@endsection
