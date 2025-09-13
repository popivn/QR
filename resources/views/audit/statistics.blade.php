<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Thống kê Audit Logs')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <i class="fas fa-qrcode"></i> FesSuport - VTTU
            </a>
            <div class="navbar-nav ms-auto">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a class="nav-link" href="{{ route('qr.index') }}">QR Generator</a>
                        <a class="nav-link" href="{{ route('audit.index') }}">Audit Logs</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Đăng xuất</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Thống kê Audit Logs
                    </h3>
                    <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <div class="card-body">
                    <!-- Thống kê theo Action -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5><i class="fas fa-tasks"></i> Thống kê theo Action</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Action</th>
                                            <th>Số lần</th>
                                            <th>Tỷ lệ</th>
                                            <th>Thanh tiến trình</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalActions = $actionStats->sum('count');
                                        @endphp
                                        @foreach($actionStats as $stat)
                                            @php
                                                $percentage = $totalActions > 0 ? ($stat->count / $totalActions) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <span class="badge bg-info">{{ $stat->action }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ number_format($stat->count) }}</strong>
                                                </td>
                                                <td>{{ number_format($percentage, 1) }}%</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-info" role="progressbar" 
                                                             style="width: {{ $percentage }}%">
                                                            {{ number_format($percentage, 1) }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê theo User -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-users"></i> Top Users (Đã đăng nhập)</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>User</th>
                                            <th>Số lần</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($userStats as $stat)
                                            <tr>
                                                <td>
                                                    @if($stat->user)
                                                        <span class="badge bg-primary">{{ $stat->user->name }}</span>
                                                    @else
                                                        <span class="text-muted">User không tồn tại</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ number_format($stat->count) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5><i class="fas fa-globe"></i> Top IP Addresses (Chưa đăng nhập)</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>IP Address</th>
                                            <th>Số lần</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ipStats as $stat)
                                            <tr>
                                                <td>
                                                    <code>{{ $stat->ip_address }}</code>
                                                </td>
                                                <td>
                                                    <strong>{{ number_format($stat->count) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê theo ngày -->
                    <div class="row">
                        <div class="col-12">
                            <h5><i class="fas fa-calendar"></i> Thống kê theo ngày (30 ngày gần nhất)</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Số lần</th>
                                            <th>Thanh tiến trình</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $maxDailyCount = $dailyStats->max('count');
                                        @endphp
                                        @foreach($dailyStats as $stat)
                                            @php
                                                $percentage = $maxDailyCount > 0 ? ($stat->count / $maxDailyCount) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ \Carbon\Carbon::parse($stat->date)->format('d/m/Y') }}</strong>
                                                </td>
                                                <td>
                                                    <strong>{{ number_format($stat->count) }}</strong>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                             style="width: {{ $percentage }}%">
                                                            {{ number_format($stat->count) }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tổng kết -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5><i class="fas fa-info-circle"></i> Tổng kết</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-primary">{{ number_format($actionStats->sum('count')) }}</h3>
                                                <p class="mb-0">Tổng số actions</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-success">{{ $actionStats->count() }}</h3>
                                                <p class="mb-0">Loại actions</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-info">{{ $userStats->count() }}</h3>
                                                <p class="mb-0">Users đã đăng nhập</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-warning">{{ $ipStats->count() }}</h3>
                                                <p class="mb-0">IP addresses</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
