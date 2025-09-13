

<?php $__env->startSection('title', 'Bảng xếp hạng các nhóm'); ?>

<?php $__env->startPush('styles'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Ensure Arial font for Vietnamese text */
        body, table, th, td, .font-medium, .text-sm, .text-xs {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
        }
        
        .leaderboard-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .leaderboard-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 24px;
        }
        
        .rank-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 18px;
            color: white;
            margin-right: 16px;
        }
        
        .rank-1 { background: #fbbf24; }
        .rank-2 { background: #9ca3af; }
        .rank-3 { background: #d97706; }
        .rank-other { background: #6b7280; }
        
        .group-item {
            display: flex;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s ease;
        }
        
        .group-item:hover {
            background-color: #f8fafc;
        }
        
        .group-item:last-child {
            border-bottom: none;
        }
        
        .group-info {
            flex: 1;
            display: flex;
            align-items: center;
        }
        
        .group-details h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 4px 0;
        }
        
        .group-details p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }
        
        .group-stats {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        
        .stat-item {
            text-align: center;
            min-width: 80px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .stat-label {
            font-size: 12px;
            color: #64748b;
            margin: 4px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .action-btn {
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        
        .action-btn:hover {
            background: #2563eb;
            color: white;
        }
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .overview-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .overview-card h3 {
            font-size: 14px;
            color: #64748b;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .overview-card .number {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .chart-container {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 32px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }
        
        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 8px 0;
        }
        
        .empty-state p {
            font-size: 16px;
            margin: 0;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li><a href="<?php echo e(route('group.index')); ?>" class="text-blue-600 hover:text-blue-800">Quản lý nhóm</a></li>
    <li><i class="fas fa-chevron-right text-gray-400"></i></li>
    <li class="text-gray-500">Bảng xếp hạng</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-header'); ?>
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
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('qr.scanner')); ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-qrcode mr-2"></i>
                    Quét QR
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('group.index')); ?>" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Tổng quan thống kê -->
    <div class="stats-overview">
        <div class="overview-card">
            <h3>Tổng số nhóm</h3>
            <p class="number"><?php echo e($leaderboard->count()); ?></p>
        </div>
        
        <div class="overview-card">
            <h3>Sinh viên tham gia</h3>
            <p class="number"><?php echo e(number_format($totalSystemStudents)); ?></p>
        </div>
    </div>

    <!-- Bảng xếp hạng -->
    <div class="leaderboard-container">
        <div class="leaderboard-header">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-trophy mr-3 text-yellow-500"></i>
                Bảng xếp hạng các nhóm
            </h2>
            <p class="text-gray-600 mt-2">
                Cập nhật lần cuối: <?php echo e(now()->format('d/m/Y H:i:s')); ?>

            </p>
        </div>
        
        <?php if($leaderboard->count() > 0): ?>
            <?php $__currentLoopData = $leaderboard; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $rank = $index + 1;
                ?>
                
                <div class="group-item">
                    <div class="group-info">
                        <!-- Rank badge -->
                        <div class="rank-badge rank-<?php echo e($rank <= 3 ? $rank : 'other'); ?>">
                            <?php if($rank <= 3): ?>
                                <i class="fas fa-medal"></i>
                            <?php else: ?>
                                <?php echo e($rank); ?>

                            <?php endif; ?>
                        </div>
                        
                        <!-- Group details -->
                        <div class="group-details">
                            <h3><?php echo e($group->name); ?></h3>
                            <?php if($group->description): ?>
                                <p><?php echo e($group->description); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="group-stats">
                        <div class="stat-item">
                            <p class="stat-number"><?php echo e(number_format($group->unique_students)); ?></p>
                            <p class="stat-label">Sinh viên</p>
                        </div>
                        
                        <a href="<?php echo e(route('qr.statistics', $group->id)); ?>" class="action-btn">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Chi tiết
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-trophy"></i>
                <h3>Chưa có dữ liệu xếp hạng</h3>
                <p>Chưa có nhóm nào tham gia quét QR code</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Biểu đồ so sánh nhỏ -->
    <?php if($leaderboard->count() > 0): ?>
        <div class="chart-container">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                Biểu đồ so sánh
            </h3>
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-3">Sinh viên tham gia</h4>
                <div style="height: 200px; width: 100%;">
                    <canvas id="studentChart" style="width: 100%;"></canvas>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if($leaderboard->count() > 0): ?>
        // Chart data
        const groups = <?php echo json_encode($leaderboard->pluck('name'), 15, 512) ?>;
        const studentData = <?php echo json_encode($leaderboard->pluck('unique_students'), 15, 512) ?>;
        
        // Colors for charts - clean and modern
        const colors = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
            '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1'
        ];
        
        // Student count chart only - compact version
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
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            usePointStyle: true,
                            color: '#64748b',
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    <?php endif; ?>
    
    // Auto refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/qr/leaderboard.blade.php ENDPATH**/ ?>