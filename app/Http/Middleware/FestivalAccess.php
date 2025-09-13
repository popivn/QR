<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Festival;

class FestivalAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy festival_id từ route parameter hoặc session
        $festivalId = $request->route('festival_id') ?? session('current_festival_id');
        
        if ($festivalId) {
            $festival = Festival::find($festivalId);
            
            if (!$festival) {
                return redirect()->route('festival.index')
                    ->with('error', 'Lễ hội không tồn tại');
            }
            
            // Kiểm tra quyền truy cập
            if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
                return redirect()->route('festival.index')
                    ->with('error', 'Bạn không có quyền truy cập lễ hội này');
            }
            
            // Lưu festival vào request để sử dụng trong controller
            $request->merge(['current_festival' => $festival]);
        }

        return $next($request);
    }
}
