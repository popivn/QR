@extends('layouts.app')

@section('title', 'Bảng xếp hạng các nhóm')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('breadcrumb')
    <li><a href="{{ route('group.index') }}" class="text-blue-600 hover:text-blue-800">Quản lý nhóm</a></li>
    <li><i class="fas fa-chevron-right text-gray-400"></i></li>
    <li class="text-gray-500">Bảng xếp hạng</li>
@endsection

@section('page-header')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-800">
                <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                Bảng xếp hạng các nhóm
            </h1>
            <p class="text-gray-600 mt-1 text-sm lg:text-base">
                Xếp hạng dựa trên số lượng sinh viên tham gia thống nhất
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
            @auth
                <a href="{{ route('qr.scanner') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <i class="fas fa-qrcode mr-2"></i>
                    Quét QR
                </a>
            @endauth
            <a href="{{ route('group.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Tổng quan thống kê -->
    <div class="grid grid-cols-2 lg:grid-cols-2 gap-3 lg:gap-5 mb-6 lg:mb-8">
        <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-xs lg:text-sm text-gray-500 uppercase tracking-wide mb-2 lg:mb-3">Tổng số nhóm</h3>
            <p class="text-2xl lg:text-3xl font-bold text-gray-800">{{ $leaderboard->count() }}</p>
        </div>
        
        <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-xs lg:text-sm text-gray-500 uppercase tracking-wide mb-2 lg:mb-3">Sinh viên tham gia</h3>
            <p class="text-2xl lg:text-3xl font-bold text-gray-800">{{ number_format($totalSystemStudents) }}</p>
        </div>
    </div>

    <!-- Bảng xếp hạng -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 p-4 lg:p-6">
            <h2 class="text-lg lg:text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-trophy mr-2 lg:mr-3 text-yellow-500"></i>
                <span class="hidden sm:inline">Bảng xếp hạng các nhóm</span>
                <span class="sm:hidden">Xếp hạng</span>
            </h2>
            <p class="text-gray-600 mt-2 text-xs lg:text-sm">
                Cập nhật: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
        
        @if($leaderboard->count() > 0)
            @foreach($leaderboard as $index => $group)
                @php
                    $rank = $index + 1;
                @endphp
                
                <div class="flex flex-col lg:flex-row lg:items-center p-4 lg:p-5 border-b border-gray-100 hover:bg-gray-50 transition-colors last:border-b-0">
                    <div class="flex items-center mb-3 lg:mb-0 flex-1">
                        <!-- Rank badge -->
                        <div class="flex items-center justify-center w-10 h-10 lg:w-12 lg:h-12 rounded-lg lg:rounded-xl font-bold text-white text-base lg:text-lg mr-3 lg:mr-4 flex-shrink-0
                            @if($rank == 1) bg-yellow-400
                            @elseif($rank == 2) bg-gray-400
                            @elseif($rank == 3) bg-orange-600
                            @else bg-gray-500
                            @endif">
                            @if($rank <= 3)
                                <i class="fas fa-medal"></i>
                            @else
                                {{ $rank }}
                            @endif
                        </div>
                        
                        <!-- Group details -->
                        <div class="flex-1">
                            <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-1 leading-tight">{{ $group->name }}</h3>
                            @if($group->description)
                                <p class="text-xs lg:text-sm text-gray-600 leading-relaxed">{{ $group->description }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex items-center justify-between lg:justify-end gap-4">
                        <div class="text-center min-w-0 lg:min-w-20">
                            <p class="text-lg lg:text-2xl font-bold text-gray-800">{{ number_format($group->unique_students) }}</p>
                            <p class="text-xs lg:text-xs text-gray-500 uppercase tracking-wide">Sinh viên</p>
                        </div>
                        
                        <a href="{{ route('qr.statistics', $group->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded-lg text-xs lg:text-sm font-medium transition-colors whitespace-nowrap">
                            <i class="fas fa-chart-bar mr-1 lg:mr-2"></i>
                            Chi tiết
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-12 lg:py-16 px-4 text-gray-500">
                <i class="fas fa-trophy text-4xl lg:text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg lg:text-xl font-semibold mb-2">Chưa có dữ liệu xếp hạng</h3>
                <p class="text-sm lg:text-base">Chưa có nhóm nào tham gia quét QR code</p>
            </div>
        @endif
    </div>

    <!-- Biểu đồ so sánh nhỏ -->
    @if($leaderboard->count() > 0)
        <div class="bg-white p-4 lg:p-6 rounded-xl shadow-sm">
            <h3 class="text-base lg:text-lg font-semibold text-gray-700 mb-3 lg:mb-4 flex items-center">
                <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                <span class="hidden sm:inline">Biểu đồ so sánh</span>
                <span class="sm:hidden">Biểu đồ</span>
            </h3>
            <div>
                <h4 class="text-xs lg:text-sm font-medium text-gray-600 mb-2 lg:mb-3">Sinh viên tham gia</h4>
                <div class="h-44 lg:h-48 w-full">
                    <canvas id="studentChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($leaderboard->count() > 0)
        // Chart data
        const groups = @json($leaderboard->pluck('name'));
        const studentData = @json($leaderboard->pluck('unique_students'));
        
        // Colors for charts - clean and modern
        const colors = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
            '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1'
        ];
        
        // Student count chart only - compact version
        const studentCtx = document.getElementById('studentChart').getContext('2d');
        new Chart(studentCtx, {
            type: 'doughnut',
            data: {
                labels: groups,
                datasets: [{
                    data: studentData,
                    backgroundColor: colors.slice(0, groups.length),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            usePointStyle: true,
                            color: '#64748b',
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    @endif
    
    // Auto refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
@endpush
