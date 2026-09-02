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
        // For SQLite or MySQL, re-index date without unique constraint to allow multiple shifts/sessions per date
        try {
            Schema::table('business_days', function (Blueprint $table) {
                $table->dropUnique(['date']);
            });
        } catch (\Throwable $e) {
            // Index might not exist or might already be dropped
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('business_days', function (Blueprint $table) {
                $table->unique('date');
            });
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};
