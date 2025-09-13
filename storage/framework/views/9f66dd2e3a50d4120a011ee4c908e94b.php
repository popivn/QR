

<?php $__env->startSection('title', 'Quản lý Lễ hội'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Quản lý Lễ hội
                    </h3>
                    <a href="<?php echo e(route('festival.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Tạo Lễ hội Mới
                    </a>
                </div>
                
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if($festivals->count() > 0): ?>
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
                                    <?php $__currentLoopData = $festivals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $festival): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($festival->id); ?></td>
                                            <td>
                                                <strong><?php echo e($festival->name); ?></strong>
                                                <?php if($festival->isOngoing()): ?>
                                                    <span class="badge badge-success ml-2">Đang diễn ra</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e(Str::limit($festival->description, 50)); ?></td>
                                            <td><?php echo e($festival->start_date ? $festival->start_date->format('d/m/Y') : 'Không xác định'); ?></td>
                                            <td><?php echo e($festival->end_date ? $festival->end_date->format('d/m/Y') : 'Không xác định'); ?></td>
                                            <td>
                                                <?php if($festival->is_active): ?>
                                                    <span class="badge badge-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($festival->creator->name ?? 'N/A'); ?></td>
                                            <td><?php echo e($festival->created_at->format('d/m/Y H:i')); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('festival.show', $festival->id)); ?>" 
                                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if(auth()->user()->isAdmin() || $festival->isAdmin(auth()->id())): ?>
                                                        <a href="<?php echo e(route('festival.edit', $festival->id)); ?>" 
                                                           class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <form action="<?php echo e(route('festival.destroy', $festival->id)); ?>" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa lễ hội này?')">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <form action="<?php echo e(route('festival.select')); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="festival_id" value="<?php echo e($festival->id); ?>">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Chọn lễ hội">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            <?php echo e($festivals->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có lễ hội nào</h5>
                            <p class="text-muted">Hãy tạo lễ hội đầu tiên của bạn!</p>
                            <a href="<?php echo e(route('festival.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i>
                                Tạo Lễ hội Mới
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/festival/index.blade.php ENDPATH**/ ?>