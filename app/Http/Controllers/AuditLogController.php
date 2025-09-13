<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Hiển thị danh sách audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Lọc theo user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Lọc theo resource type
        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        // Lọc theo IP address
        if ($request->filled('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        // Lọc theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // Lấy danh sách users để filter
        $users = User::orderBy('name')->get();

        // Lấy danh sách actions để filter
        $actions = AuditLog::distinct()->pluck('action')->sort();

        // Lấy danh sách resource types để filter
        $resourceTypes = AuditLog::distinct()->pluck('resource_type')->filter()->sort();

        return view('audit.index', compact('logs', 'users', 'actions', 'resourceTypes'));
    }

    /**
     * Hiển thị chi tiết audit log
     */
    public function show(AuditLog $auditLog)
    {
        return view('audit.show', compact('auditLog'));
    }

    /**
     * Thống kê audit logs
     */
    public function statistics()
    {
        // Thống kê theo action
        $actionStats = AuditLog::select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        // Thống kê theo user
        $userStats = AuditLog::select('user_id', DB::raw('count(*) as count'))
            ->with('user')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Thống kê theo IP (chỉ cho user chưa đăng nhập)
        $ipStats = AuditLog::select('ip_address', DB::raw('count(*) as count'))
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Thống kê theo ngày
        $dailyStats = AuditLog::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('audit.statistics', compact('actionStats', 'userStats', 'ipStats', 'dailyStats'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user');

        // Áp dụng các filter giống như index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID',
                'Thời gian',
                'User ID',
                'Tên User',
                'IP Address',
                'Action',
                'Resource Type',
                'Resource ID',
                'Mô tả',
                'User Agent',
                'URL',
                'Method'
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_id,
                    $log->user ? $log->user->name : 'N/A',
                    $log->ip_address,
                    $log->action,
                    $log->resource_type,
                    $log->resource_id,
                    $log->description,
                    $log->metadata['user_agent'] ?? '',
                    $log->metadata['url'] ?? '',
                    $log->metadata['method'] ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
