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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_activated')->default(true)->after('role');
            $table->timestamp('activated_at')->nullable()->after('is_activated');
            $table->foreignId('activated_by')->nullable()->after('activated_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['activated_by']);
            $table->dropColumn(['is_activated', 'activated_at', 'activated_by']);
        });
    }
};
