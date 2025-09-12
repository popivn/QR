<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleNgrok
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detect ngrok hoặc HTTPS tunnel
        $isNgrok = $request->header('x-forwarded-proto') === 'https' || 
                   str_contains($request->getHost(), 'ngrok.io') ||
                   str_contains($request->getHost(), 'ngrok-free.app') ||
                   $request->isSecure();
        
        if ($isNgrok) {
            // Cấu hình session cho ngrok
            config([
                'session.secure' => true,
                'session.same_site' => 'none', // Cần none cho ngrok
                'session.http_only' => false, // Cho phép JavaScript access
            ]);
            
            // Thêm CORS headers cho ngrok
            $response = $next($request);
            
            $response->headers->set('Access-Control-Allow-Origin', $request->header('Origin', '*'));
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN, X-Requested-With, Accept, Cache-Control');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            
            return $response;
        }
        
        return $next($request);
    }
}
