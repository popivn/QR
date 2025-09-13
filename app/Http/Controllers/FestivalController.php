<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Festival;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FestivalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Nếu là admin toàn hệ thống, xem tất cả festival
        if ($user->isAdmin()) {
            $festivals = Festival::with('creator')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            // Nếu không phải admin, chỉ xem festival mà user đã tạo
            $festivals = $user->createdFestivals()->orderBy('created_at', 'desc')->paginate(10);
        }
        
        return view('festival.index', compact('festivals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('festival.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'name.required' => 'Tên lễ hội là bắt buộc',
            'name.max' => 'Tên lễ hội không được quá 255 ký tự',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $festival = Festival::create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'created_by' => Auth::id(),
                'is_active' => true,
            ]);

            // Log audit
            AuditLog::log(
                'CREATE',
                'Festival',
                $festival->id,
                "Tạo lễ hội: {$festival->name}",
                ['festival_name' => $festival->name],
                $festival->id
            );

            return redirect()->route('festival.show', $festival->id)
                ->with('success', 'Tạo lễ hội thành công! Bạn đã trở thành admin của lễ hội này.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Lỗi khi tạo lễ hội: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $festival = Festival::with('creator')->findOrFail($id);
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền truy cập lễ hội này');
        }

        // Thống kê
        $stats = [
            'total_students' => $festival->students()->count(),
            'total_groups' => $festival->groups()->count(),
            'total_scans' => $festival->auditLogs()->where('action', 'SCAN')->count(),
        ];

        return view('festival.show', compact('festival', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $festival = Festival::findOrFail($id);
        $user = Auth::user();
        
        // Kiểm tra quyền chỉnh sửa
        if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền chỉnh sửa lễ hội này');
        }

        return view('festival.edit', compact('festival'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $festival = Festival::findOrFail($id);
        $user = Auth::user();
        
        // Kiểm tra quyền chỉnh sửa
        if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền chỉnh sửa lễ hội này');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Tên lễ hội là bắt buộc',
            'name.max' => 'Tên lễ hội không được quá 255 ký tự',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $oldData = $festival->toArray();
            
            $festival->update([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->has('is_active'),
            ]);

            // Log audit
            AuditLog::log(
                'UPDATE',
                'Festival',
                $festival->id,
                "Cập nhật lễ hội: {$festival->name}",
                [
                    'old_data' => $oldData,
                    'new_data' => $festival->toArray()
                ],
                $festival->id
            );

            return redirect()->route('festival.show', $festival->id)
                ->with('success', 'Cập nhật lễ hội thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Lỗi khi cập nhật lễ hội: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $festival = Festival::findOrFail($id);
        $user = Auth::user();
        
        // Kiểm tra quyền xóa
        if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền xóa lễ hội này');
        }

        try {
            $festivalName = $festival->name;
            
            // Log audit trước khi xóa
            AuditLog::log(
                'DELETE',
                'Festival',
                $festival->id,
                "Xóa lễ hội: {$festivalName}",
                ['festival_data' => $festival->toArray()],
                $festival->id
            );

            $festival->delete();

            return redirect()->route('festival.index')
                ->with('success', "Đã xóa lễ hội '{$festivalName}' thành công!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Lỗi khi xóa lễ hội: ' . $e->getMessage());
        }
    }

    /**
     * Chọn lễ hội để làm việc
     */
    public function select(Request $request)
    {
        $festivalId = $request->festival_id;
        $festival = Festival::findOrFail($festivalId);
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền truy cập lễ hội này');
        }

        // Lưu festival_id vào session
        session(['current_festival_id' => $festivalId]);
        session(['current_festival_name' => $festival->name]);

        return redirect()->back()
            ->with('success', "Đã chọn lễ hội: {$festival->name}");
    }

    /**
     * Lấy danh sách lễ hội cho dropdown
     */
    public function getFestivals()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $festivals = Festival::active()->orderBy('name')->get();
        } else {
            $festivals = $user->createdFestivals()->active()->orderBy('name')->get();
        }
        
        return response()->json($festivals);
    }
}
