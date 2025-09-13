<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUsersUsernameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing users to have username based on email
        $users = User::whereNull('username')->get();
        
        foreach ($users as $user) {
            // Extract username from email (part before @)
            $username = explode('@', $user->email)[0];
            
            // Make sure username is unique
            $originalUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $originalUsername . $counter;
                $counter++;
            }
            
            $user->update(['username' => $username]);
            echo "Updated user {$user->name} with username: {$username}\n";
        }
    }
}
