<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Thêm CORS headers cho response
     */
    private function addCorsHeaders($response, $request)
    {
        $origin = $request->header('Origin');
        if ($origin && (str_contains($origin, 'ngrok') || str_contains($origin, 'localhost'))) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
        return $response;
    }
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect based on role
            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('group.index'));
            } else {
                return redirect()->intended(route('group.index'));
            }
        }

        return back()->withErrors([
            'username' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('username');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2, // Default role is User
        ]);

        Auth::login($user);

        return redirect()->route('group.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function verifyLogin(Request $request)
    {
        $token = $request->get('token');
        $username = $request->get('username');

        // Kiểm tra token và username
        if (!$token || !$username) {
            return redirect()->route('login')->with('error', 'Thiếu thông tin xác thực.');
        }

        // Tìm user theo username
        $user = User::where('username', $username)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Người dùng không tồn tại.');
        }

        // Gửi request xác thực token tới API ngoài
        $apiUrl = 'https://info.vttu.edu.vn/api/verify_token.php';
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get($apiUrl, [
                'username' => $username,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Không thể kết nối tới máy chủ xác thực.');
        }

        if (!$response || $response->failed()) {
            return redirect()->route('login')->with('error', 'Lỗi xác thực từ máy chủ.');
        }

        $result = $response->json();

        // Giả sử API trả về ['status' => 'ok'] khi thành công
        if (!isset($result['status']) || $result['status'] !== 'ok') {
            return redirect()->route('login')->with('error', 'Token không hợp lệ hoặc đã hết hạn.');
        }

        // Đăng nhập user
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->intended(route('group.index'))->with('success', 'Đăng nhập thành công!');
        } else {
            return redirect()->intended(route('group.index'))->with('success', 'Đăng nhập thành công!');
        }
    }
}