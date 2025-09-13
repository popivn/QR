<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // null nếu chưa đăng nhập
            $table->string('ip_address')->nullable(); // null nếu chưa đăng nhập
            $table->string('action'); // hành động: view, scan, login, etc.
            $table->string('resource_type')->nullable(); // loại tài nguyên: statistics, leaderboard, qr_scan, etc.
            $table->unsignedBigInteger('resource_id')->nullable(); // ID của tài nguyên (group_id, student_id, etc.)
            $table->text('description')->nullable(); // mô tả chi tiết
            $table->json('metadata')->nullable(); // dữ liệu bổ sung (user_agent, referer, etc.)
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
