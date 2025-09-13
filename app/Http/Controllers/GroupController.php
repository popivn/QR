<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Admin có thể truy cập tất cả nhóm mà không cần thuộc về nhóm cụ thể
        if ($user->isAdmin()) {
            $groups = Group::with('users')->get();
            return view('group.admin-index', compact('groups'));
        }
        
        $group = $user->group;
        
        // Nếu user thường chưa có group, hiển thị thông báo chờ admin phân công
        if (!$group) {
            return view('group.waiting', compact('user'));
        }
        
        return view('group.index', compact('group'));
    }

    public function create()
    {
        return view('group.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:groups,name',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Group::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('group.index')->with('success', 'Nhóm đã được tạo thành công!');
    }

    public function show($id)
    {
        $group = Group::with('users')->findOrFail($id);
        return view('group.show', compact('group'));
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);
        return view('group.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:groups,name,' . $id,
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('group.index')->with('success', 'Nhóm đã được cập nhật thành công!');
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        
        // Cập nhật tất cả users trong group này thành null
        $group->users()->update(['group_id' => null]);
        
        $group->delete();

        return redirect()->route('group.index')->with('success', 'Nhóm đã được xóa thành công!');
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