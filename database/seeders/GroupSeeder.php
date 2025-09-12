<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các nhóm mẫu
        $groups = [
            [
                'name' => 'Group A',
                'description' => 'Nhóm A - Nhóm phát triển phần mềm'
            ],
            [
                'name' => 'Group B', 
                'description' => 'Nhóm B - Nhóm thiết kế giao diện'
            ],
            [
                'name' => 'Group C',
                'description' => 'Nhóm C - Nhóm quản lý dự án'
            ]
        ];

        foreach ($groups as $group) {
            \App\Models\Group::create($group);
        }

        // Tạo user admin mẫu
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@vttu.edu.vn',
            'password' => bcrypt('password'),
            'role_id' => 1, // Admin
            'group_id' => 1 // Thuộc Group A
        ]);

        // Tạo user thường mẫu
        \App\Models\User::create([
            'name' => 'User 1',
            'email' => 'user1@vttu.edu.vn', 
            'password' => bcrypt('password'),
            'role_id' => 2, // User
            'group_id' => 1 // Thuộc Group A
        ]);

        \App\Models\User::create([
            'name' => 'User 2',
            'email' => 'user2@vttu.edu.vn',
            'password' => bcrypt('password'), 
            'role_id' => 2, // User
            'group_id' => 2 // Thuộc Group B
        ]);
    }
}
