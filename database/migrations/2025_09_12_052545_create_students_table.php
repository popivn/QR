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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('mssv')->unique(); // Mã số sinh viên
            $table->string('name')->nullable(); // Tên sinh viên
            $table->string('class')->nullable(); // Lớp
            $table->string('qr_code_path')->nullable(); // Đường dẫn file QR code
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
