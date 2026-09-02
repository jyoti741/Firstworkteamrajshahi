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
        Schema::table('business_days', function (Blueprint $table) {
            if (! Schema::hasColumn('business_days', 'closing_cost')) {
                $table->decimal('closing_cost', 10, 2)->default(0)->after('closing_cash_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_days', function (Blueprint $table) {
            if (Schema::hasColumn('business_days', 'closing_cost')) {
                $table->dropColumn('closing_cost');
            }
        });
    }
};
