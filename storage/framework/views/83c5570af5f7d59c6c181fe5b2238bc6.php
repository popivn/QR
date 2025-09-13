<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'FesSuport - VTTU - VTTU'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap CSS for audit views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo e(asset('Logo-DH-Vo-Truong-Toan-VTTU-288x300.png')); ?>">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS for mobile optimization -->
    <style>
        /* Mobile-first responsive design */
        @media (max-width: 640px) {
            .mobile-padding {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .mobile-text-sm {
                font-size: 0.875rem;
            }
            
            .mobile-text-lg {
                font-size: 1.125rem;
            }
            
            .mobile-text-xl {
                font-size: 1.25rem;
            }
            
            .mobile-text-2xl {
                font-size: 1.5rem;
            }
            
            .mobile-text-3xl {
                font-size: 1.875rem;
            }
            
            .mobile-button {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }
            
            .mobile-card {
                margin-bottom: 1rem;
                padding: 1rem;
            }
            
            .mobile-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .mobile-flex-col {
                flex-direction: column;
            }
            
            .mobile-space-y-2 > * + * {
                margin-top: 0.5rem;
            }
            
            .mobile-space-y-4 > * + * {
                margin-top: 1rem;
            }
        }
        
        /* Touch-friendly buttons */
        .touch-button {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Smooth transitions */
        .smooth-transition {
            transition: all 0.2s ease-in-out;
        }
        
        /* Better focus states for accessibility */
        .focus-visible:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Bootstrap compatibility for audit views */
        .audit-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        
        /* Ensure Bootstrap cards work properly */
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        
        /* Override Tailwind for audit pages */
        .audit-page .container-fluid {
            padding: 0 !important;
        }
        
        /* Ensure audit pages use Bootstrap styling */
        .audit-page * {
            box-sizing: border-box;
        }
        
        .audit-page .card {
            background-color: #fff;
            border-radius: 0.375rem;
        }
        
        .audit-page .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <a href="<?php echo e(route('welcome')); ?>" class="flex items-center space-x-2">
                        <i class="fas fa-qrcode text-blue-600 text-xl"></i>
                        <span class="font-bold text-gray-800 mobile-text-sm md:text-base">FesSuport - VTTU</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Bảng xếp hạng - accessible to everyone -->
                    <a href="<?php echo e(route('qr.leaderboard')); ?>" class="text-gray-700 hover:text-blue-600 smooth-transition">
                        <i class="fas fa-trophy mr-1"></i>Bảng xếp hạng
                    </a>
                    
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('group.index')); ?>" class="text-gray-700 hover:text-blue-600 smooth-transition">
                            <i class="fas fa-users mr-1"></i>Quản lý nhóm
                        </a>
                        <?php if(auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('qr.index')); ?>" class="text-gray-700 hover:text-blue-600 smooth-transition">
                                <i class="fas fa-qrcode mr-1"></i>QR Generator
                            </a>
                            <a href="<?php echo e(route('audit.index')); ?>" class="text-gray-700 hover:text-blue-600 smooth-transition">
                                <i class="fas fa-clipboard-list mr-1"></i>Audit Logs
                            </a>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline" id="logout-form">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-gray-700 hover:text-red-600 smooth-transition">
                                <i class="fas fa-sign-out-alt mr-1"></i>Đăng xuất
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-gray-700 hover:text-blue-600 smooth-transition">
                            <i class="fas fa-sign-in-alt mr-1"></i>Đăng nhập
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg smooth-transition">
                            <i class="fas fa-user-plus mr-1"></i>Đăng ký
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600 touch-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 py-4">
                <div class="flex flex-col space-y-2">
                    <!-- Bảng xếp hạng - accessible to everyone -->
                    <a href="<?php echo e(route('qr.leaderboard')); ?>" class="text-gray-700 hover:text-blue-600 py-2 smooth-transition">
                        <i class="fas fa-trophy mr-2"></i>Bảng xếp hạng
                    </a>
                    
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('group.index')); ?>" class="text-gray-700 hover:text-blue-600 py-2 smooth-transition">
                            <i class="fas fa-users mr-2"></i>Quản lý nhóm
                        </a>
                        <?php if(auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('qr.index')); ?>" class="text-gray-700 hover:text-blue-600 py-2 smooth-transition">
                                <i class="fas fa-qrcode mr-2"></i>QR Generator
                            </a>
                            <a href="<?php echo e(route('audit.index')); ?>" class="text-gray-700 hover:text-blue-600 py-2 smooth-transition">
                                <i class="fas fa-clipboard-list mr-2"></i>Audit Logs
                            </a>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline" id="logout-form-mobile">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-gray-700 hover:text-red-600 py-2 smooth-transition w-full text-left">
                                <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-gray-700 hover:text-blue-600 py-2 smooth-transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg smooth-transition text-center">
                            <i class="fas fa-user-plus mr-2"></i>Đăng ký
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6 mobile-padding">
        <!-- Breadcrumb -->
        <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-600">
                    <?php echo $__env->yieldContent('breadcrumb'); ?>
                </ol>
            </nav>
        <?php endif; ?>

        <!-- Page Header -->
        <?php if (! empty(trim($__env->yieldContent('page-header')))): ?>
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6 mobile-card">
                <?php echo $__env->yieldContent('page-header'); ?>
            </div>
        <?php endif; ?>

        <!-- Flash Messages -->
        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 smooth-transition">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo e(session('success')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 smooth-transition">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo e(session('error')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6 smooth-transition">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo e(session('warning')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if(session('info')): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6 smooth-transition">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <?php echo e(session('info')); ?>

                </div>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center">
                <div class="flex items-center justify-center mb-2">
                    <i class="fas fa-university mr-2 text-blue-400"></i>
                    <span class="font-semibold text-sm sm:text-base">Trung Tâm Công Nghệ Phần Mềm</span>
                </div>
                <p class="text-xs sm:text-sm text-gray-300">
                    Trường Đại Học Võ Trường Toản
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    © <?php echo e(date('Y')); ?> All rights reserved
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            
            if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-yellow-100, .bg-blue-100');
            flashMessages.forEach(function(message) {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 300);
            });
        }, 5000);

        // Touch-friendly interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add touch class to body for touch devices
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
            }

            // Handle logout forms to ensure HTTPS
            const logoutForms = document.querySelectorAll('#logout-form, #logout-form-mobile');
            
            logoutForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // Ensure form action uses HTTPS if current page is HTTPS
                    if (window.location.protocol === 'https:' && form.action.startsWith('http:')) {
                        form.action = form.action.replace('http:', 'https:');
                    }
                });
            });
        });
    </script>

    <!-- Bootstrap JS for audit views -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Workspace\Laravel\VTTU\QRScan\resources\views/layouts/app.blade.php ENDPATH**/ ?>