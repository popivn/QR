@extends('layouts.app')

@section('title', 'Chi tiết Lễ hội')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ $festival->name }}
                        @if($festival->isOngoing())
                            <span class="badge badge-success ml-2">Đang diễn ra</span>
                        @endif
                    </h3>
                    <div>
                        @if(auth()->user()->isAdmin() || $festival->isAdmin(auth()->id()))
                            <a href="{{ route('festival.edit', $festival->id) }}" class="btn btn-warning mr-2">
                                <i class="fas fa-edit mr-1"></i>
                                Chỉnh sửa
                            </a>
                        @endif
                        <a href="{{ route('festival.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Quay lại
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Thông tin cơ bản</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td>{{ $festival->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tên lễ hội:</strong></td>
                                            <td>{{ $festival->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mô tả:</strong></td>
                                            <td>{{ $festival->description ?: 'Không có mô tả' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày bắt đầu:</strong></td>
                                            <td>{{ $festival->start_date ? $festival->start_date->format('d/m/Y') : 'Không xác định' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày kết thúc:</strong></td>
                                            <td>{{ $festival->end_date ? $festival->end_date->format('d/m/Y') : 'Không xác định' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td>
                                                @if($festival->is_active)
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @else
                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Người tạo:</strong></td>
                                            <td>{{ $festival->creator->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày tạo:</strong></td>
                                            <td>{{ $festival->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5>Thống kê</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info">
                                                    <i class="fas fa-users"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Sinh viên</span>
                                                    <span class="info-box-number">{{ $stats['total_students'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success">
                                                    <i class="fas fa-layer-group"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Nhóm</span>
                                                    <span class="info-box-number">{{ $stats['total_groups'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning">
                                                    <i class="fas fa-qrcode"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Lần quét</span>
                                                    <span class="info-box-number">{{ $stats['total_scans'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Thao tác nhanh</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <form action="{{ route('festival.select') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="festival_id" value="{{ $festival->id }}">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-check mr-1"></i>
                                                Chọn lễ hội này
                                            </button>
                                        </form>
                                        
                                        @if(auth()->user()->isAdmin() || $festival->isAdmin(auth()->id()))
                                            <a href="{{ route('qr.index') }}?festival_id={{ $festival->id }}" class="btn btn-info btn-block">
                                                <i class="fas fa-qrcode mr-1"></i>
                                                Quản lý QR Code
                                            </a>
                                            
                                            <a href="{{ route('group.index') }}?festival_id={{ $festival->id }}" class="btn btn-success btn-block">
                                                <i class="fas fa-layer-group mr-1"></i>
                                                Quản lý Nhóm
                                            </a>
                                            
                                            <a href="{{ route('audit.index') }}?festival_id={{ $festival->id }}" class="btn btn-warning btn-block">
                                                <i class="fas fa-history mr-1"></i>
                                                Xem Audit Log
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('qr.scanner') }}?festival_id={{ $festival->id }}" class="btn btn-secondary btn-block">
                                            <i class="fas fa-camera mr-1"></i>
                                            Quét QR Code
                                        </a>
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
@endsection
