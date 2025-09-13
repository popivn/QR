<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Chi tiết Audit Log'); ?></title>
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
                        <a class="nav-link" href="<?php echo e(route('audit.index')); ?>">Audit Logs</a>
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
                        <i class="fas fa-eye"></i> Chi tiết Audit Log #<?php echo e($auditLog->id); ?>

                    </h3>
                    <a href="<?php echo e(route('audit.index')); ?>" class="btn btn-secondary">
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
                                    <td><?php echo e($auditLog->id); ?></td>
                                </tr>
                                <tr>
                                    <th>Thời gian</th>
                                    <td><?php echo e($auditLog->created_at->format('d/m/Y H:i:s')); ?></td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>
                                        <?php if($auditLog->user): ?>
                                            <span class="badge bg-primary"><?php echo e($auditLog->user->name); ?></span>
                                            <small class="text-muted">(ID: <?php echo e($auditLog->user_id); ?>)</small>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Chưa đăng nhập</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>IP Address</th>
                                    <td>
                                        <?php if($auditLog->ip_address): ?>
                                            <code><?php echo e($auditLog->ip_address); ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">Không có</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Action</th>
                                    <td><span class="badge bg-info"><?php echo e($auditLog->action); ?></span></td>
                                </tr>
                                <tr>
                                    <th>Resource Type</th>
                                    <td>
                                        <?php if($auditLog->resource_type): ?>
                                            <?php echo e($auditLog->resource_type); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Không có</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Resource ID</th>
                                    <td>
                                        <?php if($auditLog->resource_id): ?>
                                            <?php echo e($auditLog->resource_id); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Không có</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Mô tả</h5>
                            <div class="alert alert-info">
                                <?php echo e($auditLog->description ?? 'Không có mô tả'); ?>

                            </div>

                            <h5>Metadata</h5>
                            <?php if($auditLog->metadata): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <?php $__currentLoopData = $auditLog->metadata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <th width="30%"><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?></th>
                                                <td>
                                                    <?php if(is_array($value)): ?>
                                                        <pre class="mb-0"><code><?php echo e(json_encode($value, JSON_PRETTY_PRINT)); ?></code></pre>
                                                    <?php else: ?>
                                                        <?php echo e($value); ?>

                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-secondary">
                                    Không có metadata
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Thông tin bổ sung cho QR scan -->
                    <?php if(in_array($auditLog->action, ['qr_scan_manual', 'qr_scan_image']) && $auditLog->metadata): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Thông tin QR Scan</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <?php if(isset($auditLog->metadata['student_mssv'])): ?>
                                                <div class="col-md-3">
                                                    <strong>MSSV:</strong><br>
                                                    <span class="badge bg-success"><?php echo e($auditLog->metadata['student_mssv']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($auditLog->metadata['student_name'])): ?>
                                                <div class="col-md-3">
                                                    <strong>Tên sinh viên:</strong><br>
                                                    <?php echo e($auditLog->metadata['student_name']); ?>

                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($auditLog->metadata['student_class'])): ?>
                                                <div class="col-md-3">
                                                    <strong>Lớp:</strong><br>
                                                    <?php echo e($auditLog->metadata['student_class']); ?>

                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($auditLog->metadata['group_name'])): ?>
                                                <div class="col-md-3">
                                                    <strong>Nhóm:</strong><br>
                                                    <span class="badge bg-primary"><?php echo e($auditLog->metadata['group_name']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if(isset($auditLog->metadata['scan_count'])): ?>
                                            <div class="row mt-2">
                                                <div class="col-md-3">
                                                    <strong>Số lần quét:</strong><br>
                                                    <span class="badge bg-warning"><?php echo e($auditLog->metadata['scan_count']); ?></span>
                                                </div>
                                                <?php if(isset($auditLog->metadata['scan_method'])): ?>
                                                    <div class="col-md-3">
                                                        <strong>Phương thức quét:</strong><br>
                                                        <span class="badge bg-info"><?php echo e($auditLog->metadata['scan_method']); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
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
<?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/audit/show.blade.php ENDPATH**/ ?>