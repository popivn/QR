@extends('layouts.app')

@section('title', isset($group) ? 'Thống kê QR Code - ' . $group->name : 'Bảng xếp hạng QR Code')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('breadcrumb')
    <li><a href="{{ route('group.index') }}" class="text-blue-600 hover:text-blue-800">Quản lý nhóm</a></li>
    <li><i class="fas fa-chevron-right text-gray-400"></i></li>
    <li class="text-gray-500">Thống kê QR Code</li>
@endsection

@section('page-header')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Thống kê QR Code
            </h1>
            <p class="text-gray-600 mt-1 text-sm lg:text-base">
                Group: <span class="font-semibold text-blue-600">{{ $group->name }}</span>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 lg:gap-3">
            <a href="{{ route('qr.scanner') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                <i class="fas fa-qrcode mr-2"></i>
                Quét QR
            </a>
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
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-2 lg:p-3 rounded-full bg-blue-100 text-blue-600 flex-shrink-0">
                    <i class="fas fa-qrcode text-lg lg:text-xl"></i>
                </div>
                <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                    <p class="text-xs lg:text-sm font-medium text-gray-600">Tổng số lần quét</p>
                    <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $totalScans }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-2 lg:p-3 rounded-full bg-green-100 text-green-600 flex-shrink-0">
                    <i class="fas fa-users text-lg lg:text-xl"></i>
                </div>
                <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                    <p class="text-xs lg:text-sm font-medium text-gray-600">Sinh viên đã quét</p>
                    <p class="text-xl lg:text-2xl font-semibold text-gray-900">{{ $uniqueStudents }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center">
                <div class="p-2 lg:p-3 rounded-full bg-purple-100 text-purple-600 flex-shrink-0">
                    <i class="fas fa-chart-line text-lg lg:text-xl"></i>
                </div>
                <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                    <p class="text-xs lg:text-sm font-medium text-gray-600">Trung bình/người</p>
                    <p class="text-xl lg:text-2xl font-semibold text-gray-900">
                        {{ $uniqueStudents > 0 ? round($totalScans / $uniqueStudents, 1) : 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Scan Count Chart -->
        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
            <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-3 lg:mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                <span class="hidden sm:inline">Top 10 sinh viên quét nhiều nhất</span>
                <span class="sm:hidden">Top sinh viên</span>
            </h3>
            <div class="h-48 lg:h-64">
                <canvas id="scanCountChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
            <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-3 lg:mb-4 flex items-center">
                <i class="fas fa-clock mr-2 text-green-600"></i>
                <span class="hidden sm:inline">Hoạt động gần đây</span>
                <span class="sm:hidden">Hoạt động</span>
            </h3>
            <div class="space-y-2 lg:space-y-3 max-h-48 lg:max-h-64 overflow-y-auto">
                @forelse($statistics->take(10) as $stat)
                    <div class="flex items-center justify-between p-2 lg:p-3 bg-gray-50 rounded-lg">
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-gray-800 text-sm lg:text-base truncate">{{ $stat->student->name ?? 'N/A' }}</div>
                            <div class="text-xs lg:text-sm text-gray-600">MSSV: {{ $stat->student->mssv }}</div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <div class="text-xs lg:text-sm font-semibold text-blue-600">{{ $stat->scan_count }} lần</div>
                            <div class="text-xs text-gray-500">{{ $stat->last_scanned_at->format('d/m H:i') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-6 lg:py-8">
                        <i class="fas fa-inbox text-2xl lg:text-3xl mb-2"></i>
                        <p class="text-sm lg:text-base">Chưa có dữ liệu quét</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Detailed Statistics Table -->
    <div class="bg-white rounded-xl shadow-sm p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 lg:gap-4 mb-4 lg:mb-6">
            <h3 class="text-base lg:text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-table mr-2 text-purple-600"></i>
                <span class="hidden sm:inline">Chi tiết thống kê</span>
                <span class="sm:hidden">Chi tiết</span>
            </h3>
            <div class="flex flex-col sm:flex-row gap-2">
                <button onclick="refreshData()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs lg:text-sm flex items-center justify-center">
                    <i class="fas fa-sync-alt mr-1 lg:mr-2"></i>
                    Làm mới
                </button>
                <button onclick="exportData()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-xs lg:text-sm flex items-center justify-center">
                    <i class="fas fa-download mr-1 lg:mr-2"></i>
                    Xuất Excel
                </button>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="block lg:hidden space-y-3">
            @forelse($statistics as $stat)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="flex-shrink-0 h-8 w-8">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 truncate">
                                    {{ $stat->student->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-600">MSSV: {{ $stat->student->mssv }}</div>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $stat->scan_count >= 5 ? 'bg-green-100 text-green-800' : 
                               ($stat->scan_count >= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $stat->scan_count }} lần
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-gray-600">
                        <span>Lớp: {{ $stat->student->class ?? 'N/A' }}</span>
                        <span>{{ $stat->last_scanned_at->format('d/m H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">Chưa có dữ liệu quét QR</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sinh viên
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            MSSV
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lớp
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số lần quét
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lần quét cuối
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($statistics as $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $stat->student->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat->student->mssv }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat->student->class ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $stat->scan_count >= 5 ? 'bg-green-100 text-green-800' : 
                                       ($stat->scan_count >= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $stat->scan_count }} lần
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat->last_scanned_at->format('d/m/Y H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p>Chưa có dữ liệu quét QR</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($statistics->count() > 0)
            <div class="mt-4 lg:mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs lg:text-sm text-gray-700">
                <div>
                    Hiển thị {{ $statistics->count() }} sinh viên
                </div>
                <div>
                    Tổng: {{ $totalScans }} lần quét
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Prepare data in PHP first to avoid JavaScript parsing issues
        const chartData = {!! json_encode($statistics->take(10)->map(function($stat) {
            return [
                'name' => $stat->student->name ?? 'N/A',
                'mssv' => $stat->student->mssv,
                'scan_count' => $stat->scan_count
            ];
        })) !!};

        const statisticsData = {!! json_encode($statistics->map(function($stat) {
            return [
                'name' => $stat->student->name ?? 'N/A',
                'mssv' => $stat->student->mssv,
                'class' => $stat->student->class ?? 'N/A',
                'scan_count' => $stat->scan_count,
                'last_scanned' => $stat->last_scanned_at->format('d/m/Y H:i:s')
            ];
        })) !!};

        // Initialize scan count chart
        const ctx = document.getElementById('scanCountChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(item => item.mssv),
                datasets: [{
                    label: 'Số lần quét',
                    data: chartData.map(item => item.scan_count),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: window.innerWidth < 640 ? 45 : 0,
                            font: {
                                size: window.innerWidth < 640 ? 10 : 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: window.innerWidth < 640 ? 10 : 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: window.innerWidth < 640 ? 10 : 12
                            }
                        }
                    },
                    tooltip: {
                        titleFont: {
                            size: window.innerWidth < 640 ? 11 : 13
                        },
                        bodyFont: {
                            size: window.innerWidth < 640 ? 10 : 12
                        },
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return chartData[index].name;
                            },
                            label: function(context) {
                                return 'MSSV: ' + context.label + ' - ' + context.parsed.y + ' lần quét';
                            }
                        }
                    }
                }
            }
        });

        // Refresh data function
        function refreshData() {
            location.reload();
        }

        // Export data function
        function exportData() {
            // Check if data exists
            if (!statisticsData || !Array.isArray(statisticsData) || statisticsData.length === 0) {
                alert('Không có dữ liệu để xuất!');
                return;
            }

            // Convert to CSV
            const headers = ['Tên', 'MSSV', 'Lớp', 'Số lần quét', 'Lần quét cuối'];
            const csvContent = [
                headers.join(','),
                ...statisticsData.map(row => [
                    `"${row.name || ''}"`,
                    `"${row.mssv || ''}"`,
                    `"${row.class || ''}"`,
                    `"${row.scan_count || 0}"`,
                    `"${row.last_scanned || ''}"`
                ].join(','))
            ].join('\n');

            // Download CSV with proper encoding
            const BOM = '\uFEFF'; // UTF-8 BOM for proper Vietnamese character support
            const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'qr_statistics_{{ $group->name }}_' + new Date().toISOString().split('T')[0] + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Auto refresh every 30 seconds
        setInterval(function() {
            // Only refresh if user is on this page and not interacting
            if (document.visibilityState === 'visible' && !document.querySelector(':hover')) {
                refreshData();
            }
        }, 30000);
    </script>
@endpush        