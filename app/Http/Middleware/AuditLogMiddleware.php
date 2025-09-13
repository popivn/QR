<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AuditLog;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chỉ ghi log cho các route quan trọng
        if ($this->shouldLog($request)) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    /**
     * Kiểm tra xem có nên ghi log cho request này không
     */
    private function shouldLog(Request $request): bool
    {
        $path = $request->path();
        $method = $request->method();

        // Danh sách các route cần ghi log
        $loggableRoutes = [
            'qr/statistics',
            'qr/leaderboard',
            'qr/scanner',
            'qr/scan',
            'qr/scan-image',
            'group',
            'login',
            'register',
        ];

        // Ghi log cho các route trong danh sách
        foreach ($loggableRoutes as $route) {
            if (str_starts_with($path, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ghi log request
     */
    private function logRequest(Request $request, Response $response): void
    {
        try {
            $action = $this->determineAction($request);
            $resourceType = $this->determineResourceType($request);
            $resourceId = $this->determineResourceId($request);
            $description = $this->generateDescription($request, $response);

            AuditLog::log(
                action: $action,
                resourceType: $resourceType,
                resourceId: $resourceId,
                description: $description,
                metadata: [
                    'status_code' => $response->getStatusCode(),
                    'response_time' => microtime(true) - LARAVEL_START,
                ]
            );
        } catch (\Exception $e) {
            // Không để lỗi audit log làm crash ứng dụng
            \Log::error('Audit log error: ' . $e->getMessage());
        }
    }

    /**
     * Xác định action từ request
     */
    private function determineAction(Request $request): string
    {
        $path = $request->path();
        $method = $request->method();

        if (str_contains($path, 'qr/scan')) {
            return 'qr_scan';
        }

        if (str_contains($path, 'qr/statistics')) {
            return 'view_statistics';
        }

        if (str_contains($path, 'qr/leaderboard')) {
            return 'view_leaderboard';
        }

        if (str_contains($path, 'qr/scanner')) {
            return 'view_scanner';
        }

        if (str_contains($path, 'group')) {
            return $method === 'GET' ? 'view_group' : 'manage_group';
        }

        if (str_contains($path, 'login')) {
            return 'login_attempt';
        }

        if (str_contains($path, 'register')) {
            return 'register_attempt';
        }

        return 'view_page';
    }

    /**
     * Xác định resource type từ request
     */
    private function determineResourceType(Request $request): ?string
    {
        $path = $request->path();

        if (str_contains($path, 'qr/statistics')) {
            return 'group_statistics';
        }

        if (str_contains($path, 'qr/leaderboard')) {
            return 'leaderboard';
        }

        if (str_contains($path, 'qr/scanner')) {
            return 'qr_scanner';
        }

        if (str_contains($path, 'group')) {
            return 'group';
        }

        return null;
    }

    /**
     * Xác định resource ID từ request
     */
    private function determineResourceId(Request $request): ?int
    {
        $path = $request->path();

        // Lấy group_id từ URL nếu có
        if (preg_match('/\/group\/(\d+)/', $path, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/\/qr\/statistics\/(\d+)/', $path, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Tạo mô tả cho log entry
     */
    private function generateDescription(Request $request, Response $response): string
    {
        $path = $request->path();
        $method = $request->method();
        $statusCode = $response->getStatusCode();

        $descriptions = [
            'qr/statistics' => "Xem thống kê QR scan",
            'qr/leaderboard' => "Xem bảng xếp hạng",
            'qr/scanner' => "Truy cập trang quét QR",
            'qr/scan' => "Quét QR code",
            'qr/scan-image' => "Quét QR code từ ảnh",
            'group' => "Quản lý nhóm",
            'login' => "Đăng nhập",
            'register' => "Đăng ký tài khoản",
        ];

        foreach ($descriptions as $route => $description) {
            if (str_starts_with($path, $route)) {
                return $description . " (HTTP {$method}, Status: {$statusCode})";
            }
        }

        return "Truy cập trang {$path} (HTTP {$method}, Status: {$statusCode})";
    }
}
