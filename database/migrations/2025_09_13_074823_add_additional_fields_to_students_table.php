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
        Schema::table('students', function (Blueprint $table) {
            $table->string('holot')->nullable()->after('mssv'); // Họ lót (Last name and middle name)
            $table->string('ten')->nullable()->after('holot'); // Tên (First name)
            $table->string('gioi')->nullable()->after('ten'); // Giới tính (Gender)
            $table->date('ngay_sinh')->nullable()->after('gioi'); // Ngày sinh (Date of birth)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['holot', 'ten', 'gioi', 'ngay_sinh']);
        });
    }
};
