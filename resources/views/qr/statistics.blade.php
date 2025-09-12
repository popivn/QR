<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê QR Code - {{ $group->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                        Thống kê QR Code
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Group: <span class="font-semibold text-blue-600">{{ $group->name }}</span>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('qr.scanner') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-qrcode mr-2"></i>
                        Quét QR
                    </a>
                    <a href="{{ route('group.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-qrcode text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng số lần quét</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalScans }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sinh viên đã quét</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $uniqueStudents }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Trung bình/người</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $uniqueStudents > 0 ? round($totalScans / $uniqueStudents, 1) : 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Scan Count Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                    Top 10 sinh viên quét nhiều nhất
                </h3>
                <div class="h-64">
                    <canvas id="scanCountChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-clock mr-2 text-green-600"></i>
                    Hoạt động gần đây
                </h3>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($statistics->take(10) as $stat)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-800">{{ $stat->student->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600">MSSV: {{ $stat->student->mssv }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-blue-600">{{ $stat->scan_count }} lần</div>
                                <div class="text-xs text-gray-500">{{ $stat->last_scanned_at->format('d/m H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>Chưa có dữ liệu quét</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Detailed Statistics Table -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-table mr-2 text-purple-600"></i>
                    Chi tiết thống kê
                </h3>
                <div class="flex space-x-2">
                    <button onclick="refreshData()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                        <i class="fas fa-sync-alt mr-1"></i>
                        Làm mới
                    </button>
                    <button onclick="exportData()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                        <i class="fas fa-download mr-1"></i>
                        Xuất Excel
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
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
                <div class="mt-4 flex items-center justify-between text-sm text-gray-700">
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

    <script>
        // Chart data
        const chartData = @json($statistics->take(10)->map(function($stat) {
            return [
                'name' => $stat->student->name ?? 'N/A',
                'mssv' => $stat->student->mssv,
                'scan_count' => $stat->scan_count
            ];
        }));

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
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    tooltip: {
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
            const data = @json($statistics->map(function($stat) {
                return [
                    'Tên' => $stat->student->name ?? 'N/A',
                    'MSSV' => $stat->student->mssv,
                    'Lớp' => $stat->student->class ?? 'N/A',
                    'Số lần quét' => $stat->scan_count,
                    'Lần quét cuối' => $stat->last_scanned_at->format('d/m/Y H:i:s')
                ];
            }));

            // Convert to CSV
            const headers = Object.keys(data[0] || {});
            const csvContent = [
                headers.join(','),
                ...data.map(row => headers.map(header => `"${row[header]}"`).join(','))
            ].join('\n');

            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
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
</body>
</html>
