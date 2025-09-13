<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Audit Logs'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('welcome')); ?>">
                <i class="fas fa-qrcode"></i> FesSuport - VTTU
            </a>
            <div class="navbar-nav ms-auto">
                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->isAdmin()): ?>
                        <a class="nav-link" href="<?php echo e(route('qr.index')); ?>">QR Generator</a>
                        <a class="nav-link active" href="<?php echo e(route('audit.index')); ?>">Audit Logs</a>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-light btn-sm">Đăng xuất</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> Audit Logs
                    </h3>
                    <div>
                        <a href="<?php echo e(route('audit.statistics')); ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Thống kê
                        </a>
                        <a href="<?php echo e(route('audit.export', request()->query())); ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('audit.index')); ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="user_id" class="form-label">User</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">Tất cả users</option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                            <?php echo e($user->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="action" class="form-label">Action</label>
                                <select name="action" id="action" class="form-select">
                                    <option value="">Tất cả actions</option>
                                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($action); ?>" <?php echo e(request('action') == $action ? 'selected' : ''); ?>>
                                            <?php echo e($action); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="resource_type" class="form-label">Resource Type</label>
                                <select name="resource_type" id="resource_type" class="form-select">
                                    <option value="">Tất cả types</option>
                                    <?php $__currentLoopData = $resourceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type); ?>" <?php echo e(request('resource_type') == $type ? 'selected' : ''); ?>>
                                            <?php echo e($type); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="ip_address" class="form-label">IP Address</label>
                                <input type="text" name="ip_address" id="ip_address" class="form-control" 
                                       value="<?php echo e(request('ip_address')); ?>" placeholder="192.168.1.1">
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">Từ ngày</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" 
                                       value="<?php echo e(request('date_from')); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Đến ngày</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" 
                                       value="<?php echo e(request('date_to')); ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="<?php echo e(route('audit.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Audit Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Thời gian</th>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Action</th>
                                    <th>Resource</th>
                                    <th>Mô tả</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($log->id); ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo e($log->created_at->format('d/m/Y H:i:s')); ?>

                                            </small>
                                        </td>
                                        <td>
                                            <?php if($log->user): ?>
                                                <span class="badge bg-primary"><?php echo e($log->user->name); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Chưa đăng nhập</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($log->ip_address): ?>
                                                <code><?php echo e($log->ip_address); ?></code>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($log->action); ?></span>
                                        </td>
                                        <td>
                                            <?php if($log->resource_type): ?>
                                                <small>
                                                    <?php echo e($log->resource_type); ?>

                                                    <?php if($log->resource_id): ?>
                                                        #<?php echo e($log->resource_id); ?>

                                                    <?php endif; ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?php echo e(Str::limit($log->description, 50)); ?></small>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('audit.show', $log)); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            Không có audit log nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($logs->hasPages()): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?php echo e($logs->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/audit/index.blade.php ENDPATH**/ ?>