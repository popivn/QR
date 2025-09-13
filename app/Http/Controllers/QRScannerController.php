<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Group;
use App\Models\GroupStudent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Zxing\QrReader;

class QRScannerController extends Controller
{
    /**
     * Hiển thị giao diện quét QR code
     */
    public function index()
    {
        $user = auth()->user();
        $group = $user->group;
        
        // Nếu user chưa có group, redirect về trang chờ phân công
        if (!$group) {
            return redirect()->route('group.index')->with('error', 'Bạn chưa được phân vào nhóm nào. Vui lòng chờ Admin phân công.');
        }
        
        return view('qr.scanner', compact('group'));
    }

    /**
     * Xử lý upload ảnh và quét QR code
     */
    public function scanImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'group_id' => 'required|exists:groups,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $groupId = $request->group_id;
            $image = $request->file('qr_image');
            
            // Lưu ảnh tạm thời
            $tempPath = $image->store('temp', 'public');
            $fullPath = storage_path('app/public/' . $tempPath);
            
            // Quét QR code từ ảnh
            $qrReader = new QrReader($fullPath);
            $qrData = $qrReader->text();
            
            // Xóa ảnh tạm thời
            unlink($fullPath);
            
            if (empty($qrData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy QR code trong ảnh. Vui lòng thử ảnh khác.'
                ], 404);
            }
            
            // Tìm student dựa trên QR data
            $student = $this->findStudentByQRData($qrData);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sinh viên với mã QR này'
                ], 404);
            }

            // Lưu thông tin quét vào database
            $groupStudent = GroupStudent::recordScan($groupId, $student->id);
            
            // Lấy thông tin group
            $group = Group::find($groupId);
            
            Log::info('QR Code scanned from image', [
                'student_id' => $student->id,
                'student_mssv' => $student->mssv,
                'group_id' => $groupId,
                'group_name' => $group->name,
                'scan_count' => $groupStudent->scan_count,
                'user_id' => auth()->id(),
                'qr_data' => $qrData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quét QR thành công!',
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'mssv' => $student->mssv,
                        'name' => $student->name,
                        'class' => $student->class
                    ],
                    'group' => [
                        'id' => $group->id,
                        'name' => $group->name
                    ],
                    'scan_count' => $groupStudent->scan_count,
                    'last_scanned_at' => $groupStudent->last_scanned_at->format('d/m/Y H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('QR Image Scan error', [
                'error' => $e->getMessage(),
                'group_id' => $request->group_id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý ảnh QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xử lý dữ liệu QR code được quét (manual input)
     */
    public function scan(Request $request)
    {
        Log::info('QR Scan request received', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'headers' => $request->headers->all(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method(),
            'origin' => $request->header('Origin'),
            'user_agent' => $request->header('User-Agent')
        ]);

        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string'
        ]);

        if ($validator->fails()) {
            Log::error('QR Scan validation failed', [
                'errors' => $validator->errors(),
                'request_data' => $request->all()
            ]);
            
            $response = response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 400);
            
            return $this->addCorsHeaders($response, $request);
        }

        try {
            // Parse QR data (có thể là MSSV hoặc JSON)
            $qrData = $request->qr_data;
            
            // Lấy group_id từ user hiện tại
            $user = auth()->user();
            $group = $user->group;
            
            if (!$group) {
                $response = response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa được phân vào nhóm nào'
                ], 400);
                
                return $this->addCorsHeaders($response, $request);
            }
            
            $groupId = $group->id;
            
            // Tìm student dựa trên QR data
            $student = $this->findStudentByQRData($qrData);
            
            if (!$student) {
                $response = response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sinh viên với mã QR này'
                ], 404);
                
                // Thêm CORS headers nếu cần
                $origin = $request->header('Origin');
                if ($origin && (str_contains($origin, 'ngrok') || str_contains($origin, 'localhost'))) {
                    $response->headers->set('Access-Control-Allow-Origin', $origin);
                    $response->headers->set('Access-Control-Allow-Credentials', 'true');
                }
                
                return $response;
            }

            // Lưu thông tin quét vào database
            $groupStudent = GroupStudent::recordScan($groupId, $student->id);
            
            // Lấy thông tin group
            $group = Group::find($groupId);
            
            Log::info('QR Code scanned', [
                'student_id' => $student->id,
                'student_mssv' => $student->mssv,
                'group_id' => $groupId,
                'group_name' => $group->name,
                'scan_count' => $groupStudent->scan_count,
                'user_id' => auth()->id()
            ]);

            $responseData = [
                'success' => true,
                'message' => 'Quét QR thành công!',
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'mssv' => $student->mssv,
                        'name' => $student->name,
                        'class' => $student->class
                    ],
                    'group' => [
                        'id' => $group->id,
                        'name' => $group->name
                    ],
                    'scan_count' => $groupStudent->scan_count,
                    'last_scanned_at' => $groupStudent->last_scanned_at->format('d/m/Y H:i:s')
                ]
            ];

            // Nếu là AJAX request, trả về JSON
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                $response = response()->json($responseData);
                
                // Thêm CORS headers nếu cần
                $origin = $request->header('Origin');
                if ($origin && (str_contains($origin, 'ngrok') || str_contains($origin, 'localhost'))) {
                    $response->headers->set('Access-Control-Allow-Origin', $origin);
                    $response->headers->set('Access-Control-Allow-Credentials', 'true');
                }
                
                return $response;
            }
            
            // Nếu là form submission, redirect với message
            return redirect()->back()->with('success', $responseData['message']);

        } catch (\Exception $e) {
            Log::error('QR Scan error', [
                'error' => $e->getMessage(),
                'qr_data' => $request->qr_data,
                'group_id' => $request->group_id,
                'user_id' => auth()->id()
            ]);

            $response = response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý QR code'
            ], 500);
            
            // Thêm CORS headers nếu cần
            $origin = $request->header('Origin');
            if ($origin && (str_contains($origin, 'ngrok') || str_contains($origin, 'localhost'))) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
            }
            
            return $response;
        }
    }

    /**
     * Hiển thị thống kê quét QR theo group
     */
    public function statistics($groupId = null)
    {
        $user = auth()->user();
        
        // Nếu không có group_id, hiển thị bảng xếp hạng tổng thể
        if (!$groupId) {
            return $this->showLeaderboard();
        }
        
        // Nếu user chưa có group và không phải admin, redirect về trang chờ phân công
        if ($user && !$user->isAdmin() && !$groupId) {
            return redirect()->route('group.index')->with('error', 'Bạn chưa được phân vào nhóm nào. Vui lòng chờ Admin phân công.');
        }
        
        // Kiểm tra quyền truy cập (chỉ áp dụng cho user đã đăng nhập)
        if ($user && !$user->isAdmin() && $user->group_id != $groupId) {
            abort(403, 'Bạn không có quyền xem thống kê của group này');
        }

        $group = Group::findOrFail($groupId);
        
        // Lấy thống kê quét QR
        $statistics = GroupStudent::where('group_id', $groupId)
            ->with(['student'])
            ->orderBy('scan_count', 'desc')
            ->orderBy('last_scanned_at', 'desc')
            ->get();

        // Tính tổng số lần quét
        $totalScans = $statistics->sum('scan_count');
        
        // Tính số sinh viên đã được quét (distinct)
        $uniqueStudents = $statistics->count();

        return view('qr.statistics', compact('group', 'statistics', 'totalScans', 'uniqueStudents'));
    }

    /**
     * Hiển thị bảng xếp hạng tổng thể
     */
    public function leaderboard()
    {
        // Lấy thống kê tổng thể của tất cả groups
        $leaderboard = Group::withCount(['groupStudents as total_scans' => function($query) {
                $query->selectRaw('sum(scan_count)');
            }])
            ->withCount(['groupStudents as unique_students' => function($query) {
                $query->selectRaw('count(distinct student_id)');
            }])
            ->orderBy('total_scans', 'desc')
            ->orderBy('unique_students', 'desc')
            ->get();

        // Tính tổng số lần quét của toàn hệ thống
        $totalSystemScans = $leaderboard->sum('total_scans');
        $totalSystemStudents = $leaderboard->sum('unique_students');

        return view('qr.leaderboard', compact('leaderboard', 'totalSystemScans', 'totalSystemStudents'));
    }

    /**
     * Hiển thị bảng xếp hạng tổng thể (private method cho statistics)
     */
    private function showLeaderboard()
    {
        // Lấy thống kê tổng thể của tất cả groups
        $leaderboard = Group::withCount(['groupStudents as total_scans' => function($query) {
                $query->selectRaw('sum(scan_count)');
            }])
            ->withCount(['groupStudents as unique_students' => function($query) {
                $query->selectRaw('count(distinct student_id)');
            }])
            ->orderBy('total_scans', 'desc')
            ->orderBy('unique_students', 'desc')
            ->get();

        // Tính tổng số lần quét của toàn hệ thống
        $totalSystemScans = $leaderboard->sum('total_scans');
        $totalSystemStudents = $leaderboard->sum('unique_students');

        return view('qr.statistics', compact('leaderboard', 'totalSystemScans', 'totalSystemStudents'));
    }

    /**
     * API endpoint để lấy thống kê real-time
     */
    public function getStatistics($groupId)
    {
        $user = auth()->user();
        
        // Kiểm tra quyền truy cập (chỉ áp dụng cho user đã đăng nhập)
        if ($user && !$user->isAdmin() && $user->group_id != $groupId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $statistics = GroupStudent::where('group_id', $groupId)
            ->with(['student'])
            ->orderBy('scan_count', 'desc')
            ->orderBy('last_scanned_at', 'desc')
            ->get();

        $totalScans = $statistics->sum('scan_count');
        $uniqueStudents = $statistics->count();

        return response()->json([
            'total_scans' => $totalScans,
            'unique_students' => $uniqueStudents,
            'statistics' => $statistics->map(function ($item) {
                return [
                    'student_id' => $item->student_id,
                    'student_mssv' => $item->student->mssv,
                    'student_name' => $item->student->name,
                    'student_class' => $item->student->class,
                    'scan_count' => $item->scan_count,
                    'last_scanned_at' => $item->last_scanned_at->format('d/m/Y H:i:s')
                ];
            })
        ]);
    }

    /**
     * Tìm student dựa trên QR data
     */
    private function findStudentByQRData($qrData)
    {
        // Thử parse JSON trước
        $decoded = json_decode($qrData, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['mssv'])) {
            return Student::where('mssv', $decoded['mssv'])->first();
        }

        // Nếu không phải JSON, thử tìm trực tiếp bằng MSSV
        return Student::where('mssv', $qrData)->first();
    }

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
}