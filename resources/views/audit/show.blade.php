<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chi tiết Audit Log')</title>
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
                        <i class="fas fa-eye"></i> Chi tiết Audit Log #{{ $auditLog->id }}
                    </h3>
                    <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Thông tin cơ bản</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $auditLog->id }}</td>
                                </tr>
                                <tr>
                                    <th>Thời gian</th>
                                    <td>{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>
                                        @if($auditLog->user)
                                            <span class="badge bg-primary">{{ $auditLog->user->name }}</span>
                                            <small class="text-muted">(ID: {{ $auditLog->user_id }})</small>
                                        @else
                                            <span class="badge bg-secondary">Chưa đăng nhập</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>IP Address</th>
                                    <td>
                                        @if($auditLog->ip_address)
                                            <code>{{ $auditLog->ip_address }}</code>
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Action</th>
                                    <td><span class="badge bg-info">{{ $auditLog->action }}</span></td>
                                </tr>
                                <tr>
                                    <th>Resource Type</th>
                                    <td>
                                        @if($auditLog->resource_type)
                                            {{ $auditLog->resource_type }}
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Resource ID</th>
                                    <td>
                                        @if($auditLog->resource_id)
                                            {{ $auditLog->resource_id }}
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Mô tả</h5>
                            <div class="alert alert-info">
                                {{ $auditLog->description ?? 'Không có mô tả' }}
                            </div>

                            <h5>Metadata</h5>
                            @if($auditLog->metadata)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        @foreach($auditLog->metadata as $key => $value)
                                            <tr>
                                                <th width="30%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                                <td>
                                                    @if(is_array($value))
                                                        <pre class="mb-0"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    Không có metadata
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Thông tin bổ sung cho QR scan -->
                    @if(in_array($auditLog->action, ['qr_scan_manual', 'qr_scan_image']) && $auditLog->metadata)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Thông tin QR Scan</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            @if(isset($auditLog->metadata['student_mssv']))
                                                <div class="col-md-3">
                                                    <strong>MSSV:</strong><br>
                                                    <span class="badge bg-success">{{ $auditLog->metadata['student_mssv'] }}</span>
                                                </div>
                                            @endif
                                            @if(isset($auditLog->metadata['student_name']))
                                                <div class="col-md-3">
                                                    <strong>Tên sinh viên:</strong><br>
                                                    {{ $auditLog->metadata['student_name'] }}
                                                </div>
                                            @endif
                                            @if(isset($auditLog->metadata['student_class']))
                                                <div class="col-md-3">
                                                    <strong>Lớp:</strong><br>
                                                    {{ $auditLog->metadata['student_class'] }}
                                                </div>
                                            @endif
                                            @if(isset($auditLog->metadata['group_name']))
                                                <div class="col-md-3">
                                                    <strong>Nhóm:</strong><br>
                                                    <span class="badge bg-primary">{{ $auditLog->metadata['group_name'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if(isset($auditLog->metadata['scan_count']))
                                            <div class="row mt-2">
                                                <div class="col-md-3">
                                                    <strong>Số lần quét:</strong><br>
                                                    <span class="badge bg-warning">{{ $auditLog->metadata['scan_count'] }}</span>
                                                </div>
                                                @if(isset($auditLog->metadata['scan_method']))
                                                    <div class="col-md-3">
                                                        <strong>Phương thức quét:</strong><br>
                                                        <span class="badge bg-info">{{ $auditLog->metadata['scan_method'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
