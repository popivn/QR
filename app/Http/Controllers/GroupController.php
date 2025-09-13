<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\Festival;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $festivalId = $request->festival_id ?? session('current_festival_id');
        
        if (!$festivalId) {
            return redirect()->route('festival.index')->with('error', 'Vui lòng chọn lễ hội trước');
        }
        
        $festival = Festival::findOrFail($festivalId);
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền truy cập lễ hội này');
        }
        
        // Admin có thể truy cập tất cả nhóm trong festival
        if ($user->isAdmin()) {
            $groups = Group::where('festival_id', $festivalId)->with('users')->get();
            return view('group.admin-index', compact('groups', 'festival'));
        }
        
        $group = $user->group;
        
        // Nếu user thường chưa có group, hiển thị thông báo chờ admin phân công
        if (!$group || $group->festival_id != $festivalId) {
            return view('group.waiting', compact('user', 'festival'));
        }
        
        return view('group.index', compact('group', 'festival'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $festivalId = $request->festival_id ?? session('current_festival_id');
        
        if (!$festivalId) {
            return redirect()->route('festival.index')->with('error', 'Vui lòng chọn lễ hội trước');
        }
        
        $festival = Festival::findOrFail($festivalId);
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền truy cập lễ hội này');
        }
        
        return view('group.create', compact('festival'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $festivalId = $request->festival_id ?? session('current_festival_id');
        
        if (!$festivalId) {
            return redirect()->route('festival.index')->with('error', 'Vui lòng chọn lễ hội trước');
        }
        
        $festival = Festival::findOrFail($festivalId);
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền truy cập lễ hội này');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'festival_id' => 'required|exists:festivals,id'
        ], [
            'name.required' => 'Tên nhóm là bắt buộc',
            'name.max' => 'Tên nhóm không được quá 255 ký tự',
            'festival_id.required' => 'Vui lòng chọn lễ hội',
            'festival_id.exists' => 'Lễ hội không tồn tại'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Kiểm tra tên nhóm trùng trong cùng festival
        $existingGroup = Group::where('name', $request->name)
                              ->where('festival_id', $festivalId)
                              ->first();
        
        if ($existingGroup) {
            return redirect()->back()
                ->withErrors(['name' => 'Tên nhóm đã tồn tại trong lễ hội này'])
                ->withInput();
        }

        $group = Group::create([
            'festival_id' => $festivalId,
            'name' => $request->name,
            'description' => $request->description
        ]);

        // Log audit
        AuditLog::log(
            'CREATE',
            'Group',
            $group->id,
            "Tạo nhóm: {$group->name}",
            ['group_name' => $group->name, 'festival_id' => $festivalId],
            $festivalId
        );

        return redirect()->route('group.index', ['festival_id' => $festivalId])->with('success', 'Nhóm đã được tạo thành công!');
    }

    public function show($id)
    {
        $group = Group::with(['users', 'festival'])->findOrFail($id);
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$group->festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền truy cập nhóm này');
        }
        
        return view('group.show', compact('group'));
    }

    public function edit($id)
    {
        $group = Group::with('festival')->findOrFail($id);
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$group->festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền chỉnh sửa nhóm này');
        }
        
        return view('group.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        $group = Group::with('festival')->findOrFail($id);
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$group->festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền chỉnh sửa nhóm này');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ], [
            'name.required' => 'Tên nhóm là bắt buộc',
            'name.max' => 'Tên nhóm không được quá 255 ký tự'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Kiểm tra tên nhóm trùng trong cùng festival
        $existingGroup = Group::where('name', $request->name)
                              ->where('festival_id', $group->festival_id)
                              ->where('id', '!=', $id)
                              ->first();
        
        if ($existingGroup) {
            return redirect()->back()
                ->withErrors(['name' => 'Tên nhóm đã tồn tại trong lễ hội này'])
                ->withInput();
        }

        $oldData = $group->toArray();
        
        $group->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        // Log audit
        AuditLog::log(
            'UPDATE',
            'Group',
            $group->id,
            "Cập nhật nhóm: {$group->name}",
            [
                'old_data' => $oldData,
                'new_data' => $group->toArray()
            ],
            $group->festival_id
        );

        return redirect()->route('group.index', ['festival_id' => $group->festival_id])->with('success', 'Nhóm đã được cập nhật thành công!');
    }

    public function destroy($id)
    {
        $group = Group::with('festival')->findOrFail($id);
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if (!$user->isAdmin() && !$group->festival->isAdmin($user->id)) {
            abort(403, 'Bạn không có quyền xóa nhóm này');
        }
        
        $groupName = $group->name;
        $festivalId = $group->festival_id;
        
        // Cập nhật tất cả users trong group này thành null
        $group->users()->update(['group_id' => null]);
        
        // Log audit trước khi xóa
        AuditLog::log(
            'DELETE',
            'Group',
            $group->id,
            "Xóa nhóm: {$groupName}",
            ['group_data' => $group->toArray()],
            $festivalId
        );
        
        $group->delete();

        return redirect()->route('group.index', ['festival_id' => $festivalId])->with('success', "Nhóm '{$groupName}' đã được xóa thành công!");
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::findOrFail($request->user_id);
        $user->update(['group_id' => $request->group_id]);

        return redirect()->back()->with('success', 'Đã thêm user vào nhóm thành công!');
    }

    public function removeUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['group_id' => null]);

        return redirect()->back()->with('success', 'Đã xóa user khỏi nhóm thành công!');
    }

    public function listGroups()
    {
        $groups = Group::with('users')->paginate(10);
        return view('group.list', compact('groups'));
    }

    public function listUsers()
    {
        $users = User::with('group')->paginate(10);
        return view('group.users', compact('users'));
    }
}