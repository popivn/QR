@extends('layouts.app')

@section('title', 'Quản lý Lễ hội')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Quản lý Lễ hội
                    </h3>
                    <a href="{{ route('festival.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Tạo Lễ hội Mới
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($festivals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Lễ hội</th>
                                        <th>Mô tả</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Trạng thái</th>
                                        <th>Người tạo</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($festivals as $festival)
                                        <tr>
                                            <td>{{ $festival->id }}</td>
                                            <td>
                                                <strong>{{ $festival->name }}</strong>
                                                @if($festival->isOngoing())
                                                    <span class="badge badge-success ml-2">Đang diễn ra</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($festival->description, 50) }}</td>
                                            <td>{{ $festival->start_date ? $festival->start_date->format('d/m/Y') : 'Không xác định' }}</td>
                                            <td>{{ $festival->end_date ? $festival->end_date->format('d/m/Y') : 'Không xác định' }}</td>
                                            <td>
                                                @if($festival->is_active)
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @else
                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                @endif
                                            </td>
                                            <td>{{ $festival->creator->name ?? 'N/A' }}</td>
                                            <td>{{ $festival->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('festival.show', $festival->id) }}" 
                                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if(auth()->user()->isAdmin() || $festival->isAdmin(auth()->id()))
                                                        <a href="{{ route('festival.edit', $festival->id) }}" 
                                                           class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <form action="{{ route('festival.destroy', $festival->id) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa lễ hội này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <form action="{{ route('festival.select') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="festival_id" value="{{ $festival->id }}">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Chọn lễ hội">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $festivals->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có lễ hội nào</h5>
                            <p class="text-muted">Hãy tạo lễ hội đầu tiên của bạn!</p>
                            <a href="{{ route('festival.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i>
                                Tạo Lễ hội Mới
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
