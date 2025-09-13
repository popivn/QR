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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('festival_id')->nullable()->after('user_id');
            $table->foreign('festival_id')->references('id')->on('festivals')->onDelete('cascade');
            $table->index('festival_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['festival_id']);
            $table->dropIndex(['festival_id']);
            $table->dropColumn('festival_id');
        });
    }
};
