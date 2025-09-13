<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin user
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@vttu.edu.vn',
            'password' => Hash::make('admin123'),
            'role_id' => 1, // Admin role
        ]);

        // Tạo user thường
        User::create([
            'name' => 'Nguyễn Văn A',
            'username' => 'nguyenvana',
            'email' => 'nguyenvana@vttu.edu.vn',
            'password' => Hash::make('password123'),
            'role_id' => 2, // User role
        ]);

        // Tạo user thường khác
        User::create([
            'name' => 'Trần Thị B',
            'username' => 'tranthib',
            'email' => 'tranthib@vttu.edu.vn',
            'password' => Hash::make('password123'),
            'role_id' => 2, // User role
        ]);

        // Tạo user demo theo URL bạn cung cấp
        User::create([
            'name' => 'Trung Hiếu',
            'username' => 'trunghieu3832',
            'email' => 'trunghieu3832@vttu.edu.vn',
            'password' => Hash::make('password123'),
            'role_id' => 2, // User role
        ]);

        echo "Đã tạo thành công các user mẫu:\n";
        echo "- admin (admin@vttu.edu.vn) - Quyền Admin\n";
        echo "- nguyenvana (nguyenvana@vttu.edu.vn) - Quyền User\n";
        echo "- tranthib (tranthib@vttu.edu.vn) - Quyền User\n";
        echo "- trunghieu3832 (trunghieu3832@vttu.edu.vn) - Quyền User\n";
    }
}
