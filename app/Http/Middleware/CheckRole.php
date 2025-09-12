<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Kiểm tra role admin (role_id = 1)
        if ($role === 'admin' && !$user->isAdmin()) {
            return redirect()->route('group.index')->with('error', 'Bạn không có quyền truy cập trang này!');
        }

        return $next($request);
    }
}
