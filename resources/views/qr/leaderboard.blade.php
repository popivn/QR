@extends('layouts.app')

@section('title', 'Bảng xếp hạng các nhóm')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Ensure Arial font for Vietnamese text */
        body, table, th, td, .font-medium, .text-sm, .text-xs {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
        }
        
        /* Leaderboard specific styling */
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: bold;
            font-size: 16px;
            color: white;
        }
        
        .rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); }
        .rank-2 { background: linear-gradient(135deg, #C0C0C0, #A0A0A0); }
        .rank-3 { background: linear-gradient(135deg, #CD7F32, #B8860B); }
        .rank-other { background: linear-gradient(135deg, #6B7280, #4B5563); }
        
        .group-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .group-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .group-card.rank-1 { border-left-color: #FFD700; }
        .group-card.rank-2 { border-left-color: #C0C0C0; }
        .group-card.rank-3 { border-left-color: #CD7F32; }
        
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(90deg, #3B82F6, #1D4ED8);
            transition: width 0.5s ease;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stats-card.success {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        }
        
        .stats-card.warning {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
    </style>
@endpush

@section('breadcrumb')
    <li><a href="{{ route('group.index') }}" class="text-blue-600 hover:text-blue-800">Quản lý nhóm</a></li>
    <li><i class="fas fa-chevron-right text-gray-400"></i></li>
    <li class="text-gray-500">Bảng xếp hạng</li>
@endsection

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                Bảng xếp hạng các nhóm
            </h1>
            <p class="text-gray-600 mt-1">
                Xếp hạng dựa trên số lượng sinh viên tham gia thống nhất
            </p>
        </div>
        <div class="flex space-x-3">
            @auth
                <a href="{{ route('qr.scanner') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-qrcode mr-2"></i>
                    Quét QR
                </a>
            @endauth
            <a href="{{ route('group.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Tổng quan thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stats-card rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Tổng số nhóm</p>
                    <p class="text-3xl font-bold">{{ $leaderboard->count() }}</p>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card success rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Tổng số lần quét</p>
                    <p class="text-3xl font-bold">{{ number_format($totalSystemScans) }}</p>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-qrcode"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card warning rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Sinh viên tham gia</p>
                    <p class="text-3xl font-bold">{{ number_format($totalSystemStudents) }}</p>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng xếp hạng -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-medal mr-2"></i>
                Bảng xếp hạng các nhóm
            </h2>
            <p class="text-blue-100 text-sm mt-1">
                Cập nhật lần cuối: {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
        
        <div class="p-6">
            @if($leaderboard->count() > 0)
                <div class="space-y-4">
                    @foreach($leaderboard as $index => $group)
                        @php
                            $rank = $index + 1;
                            $maxScans = $leaderboard->max('total_scans');
                            $maxStudents = $leaderboard->max('unique_students');
                            $scanPercentage = $maxScans > 0 ? ($group->total_scans / $maxScans) * 100 : 0;
                            $studentPercentage = $maxStudents > 0 ? ($group->unique_students / $maxStudents) * 100 : 0;
                        @endphp
                        
                        <div class="group-card rank-{{ $rank <= 3 ? $rank : 'other' }} bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Rank badge -->
                                    <div class="rank-badge rank-{{ $rank <= 3 ? $rank : 'other' }}">
                                        @if($rank <= 3)
                                            <i class="fas fa-medal"></i>
                                        @else
                                            {{ $rank }}
                                        @endif
                                    </div>
                                    
                                    <!-- Group info -->
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">
                                            {{ $group->name }}
                                        </h3>
                                        @if($group->description)
                                            <p class="text-sm text-gray-600 mb-2">{{ $group->description }}</p>
                                        @endif
                                        
                                        <!-- Progress bars -->
                                        <div class="space-y-2">
                                            <div>
                                                <div class="flex justify-between text-sm mb-1">
                                                    <span class="text-gray-600">Số lần quét</span>
                                                    <span class="font-semibold text-blue-600">{{ number_format($group->total_scans) }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="progress-bar h-2 rounded-full" style="width: {{ $scanPercentage }}%"></div>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <div class="flex justify-between text-sm mb-1">
                                                    <span class="text-gray-600">Sinh viên tham gia</span>
                                                    <span class="font-semibold text-green-600">{{ number_format($group->unique_students) }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: {{ $studentPercentage }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Stats -->
                                <div class="text-right">
                                    <div class="grid grid-cols-2 gap-4 text-center">
                                        <div class="bg-blue-50 rounded-lg p-3">
                                            <div class="text-2xl font-bold text-blue-600">{{ number_format($group->total_scans) }}</div>
                                            <div class="text-xs text-gray-600">Lần quét</div>
                                        </div>
                                        <div class="bg-green-50 rounded-lg p-3">
                                            <div class="text-2xl font-bold text-green-600">{{ number_format($group->unique_students) }}</div>
                                            <div class="text-xs text-gray-600">Sinh viên</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action buttons -->
                                    <div class="mt-3 flex space-x-2">
                                        <a href="{{ route('qr.statistics', $group->id) }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                            <i class="fas fa-chart-bar mr-1"></i>
                                            Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Chưa có dữ liệu xếp hạng</h3>
                    <p class="text-gray-500">Chưa có nhóm nào tham gia quét QR code</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    @if($leaderboard->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                Biểu đồ so sánh
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart for scan counts -->
                <div>
                    <h4 class="text-md font-semibold text-gray-700 mb-3">Số lần quét theo nhóm</h4>
                    <canvas id="scanChart" width="400" height="200"></canvas>
                </div>
                
                <!-- Chart for student counts -->
                <div>
                    <h4 class="text-md font-semibold text-gray-700 mb-3">Số sinh viên tham gia theo nhóm</h4>
                    <canvas id="studentChart" width="400" height="200"></canvas>
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
        const scanData = @json($leaderboard->pluck('total_scans'));
        const studentData = @json($leaderboard->pluck('unique_students'));
        
        // Colors for charts
        const colors = [
            '#FFD700', '#C0C0C0', '#CD7F32', '#3B82F6', '#10B981', 
            '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16'
        ];
        
        // Scan count chart
        const scanCtx = document.getElementById('scanChart').getContext('2d');
        new Chart(scanCtx, {
            type: 'bar',
            data: {
                labels: groups,
                datasets: [{
                    label: 'Số lần quét',
                    data: scanData,
                    backgroundColor: colors.slice(0, groups.length),
                    borderColor: colors.slice(0, groups.length).map(color => color + '80'),
                    borderWidth: 2,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Student count chart
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
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
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
