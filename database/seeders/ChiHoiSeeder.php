<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

class ChiHoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dữ liệu các chi hội
        $chiHoiData = [
            [
                'group_name' => 'Chi hội PTSV & CTXH An Giang 1',
                'username' => 'CHSVAG1',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH An Giang 2',
                'username' => 'CHSVAG2',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH An Giang 3',
                'username' => 'CHSVAG3',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH An Giang 4',
                'username' => 'CHSVAG4',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Cần Thơ',
                'username' => 'CHSVCT',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Cà Mau',
                'username' => 'CHSVCM',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Đồng Tháp',
                'username' => 'CHSVDT',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Vĩnh Long 1',
                'username' => 'CHSVVL1',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Vĩnh Long 2',
                'username' => 'CHSVVL2',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Tp Hồ Chí Minh',
                'username' => 'CHSV_TPHCM',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Đông Nam Bộ',
                'username' => 'CHSV_DNB',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Miền Bắc & Miền Trung 1',
                'username' => 'CHSV_MBMT1',
                'password' => '12345'
            ],
            [
                'group_name' => 'Chi hội PTSV & CTXH Miền Bắc & Miền Trung 2',
                'username' => 'CHSV_MBMT2',
                'password' => '12345'
            ]
        ];

        echo "Bắt đầu tạo dữ liệu các chi hội...\n";

        foreach ($chiHoiData as $index => $data) {
            // Tạo Group
            $group = Group::create([
                'name' => $data['group_name'],
                'description' => 'Chi hội ' . $data['group_name']
            ]);

            // Tạo User cho chi hội
            $user = User::create([
                'name' => $data['group_name'],
                'username' => $data['username'],
                'email' => strtolower($data['username']) . '@vttu.edu.vn',
                'password' => Hash::make($data['password']),
                'role_id' => 2, // User role (không phải admin)
                'group_id' => $group->id
            ]);

            echo "✓ Đã tạo: {$data['group_name']} - Username: {$data['username']}\n";
        }

        echo "\nHoàn thành! Đã tạo thành công " . count($chiHoiData) . " chi hội.\n";
        echo "Tất cả chi hội đều có password: 12345\n";
    }
}
