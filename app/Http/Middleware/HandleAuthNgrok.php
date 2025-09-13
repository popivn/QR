<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleAuthNgrok
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
            // Cấu hình session cho ngrok - ưu tiên bảo mật cho auth
            config([
                'session.secure' => true,
                'session.same_site' => 'none', // Cần none cho ngrok
                'session.http_only' => true, // Bảo mật cho authentication
                'session.domain' => null, // Không set domain để tránh conflict
            ]);
            
            $response = $next($request);
            
            // Lấy origin từ request header
            $origin = $request->header('Origin');
            $allowedOrigins = [
                'http://f48b02984710.ngrok-free.app',
                'https://f48b02984710.ngrok-free.app',
                'http://localhost:8000',
                'https://localhost:8000'
            ];
            
            // Nếu origin được phép, sử dụng nó, nếu không thì sử dụng wildcard
            $allowedOrigin = in_array($origin, $allowedOrigins) ? $origin : '*';
            
            // Thêm CORS headers cho ngrok - minimal cho auth
            $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN, X-Requested-With, Accept');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');
            $response->headers->set('Vary', 'Origin');
            
            // Content Security Policy cho auth - relaxed for development
            $csp = "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob: https:; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http: data: blob:; " .
                   "style-src 'self' 'unsafe-inline' https: http: data:; " .
                   "img-src 'self' data: blob: https: http:; " .
                   "connect-src 'self' " . $request->getSchemeAndHttpHost() . " " . $origin . " https: http:; " .
                   "font-src 'self' https: http: data:; " .
                   "media-src 'self' blob: https: http:; " .
                   "object-src 'none'; " .
                   "base-uri 'self'; " .
                   "form-action 'self' " . $request->getSchemeAndHttpHost() . " " . $origin . "; " .
                   "frame-ancestors 'self';";
            
            $response->headers->set('Content-Security-Policy', $csp);
            
            return $response;
        }
        
        return $next($request);
    }
}
