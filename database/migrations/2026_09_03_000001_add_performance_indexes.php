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
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index(['business_day_id', 'status']);
            $table->index('payment_method');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->index(['sale_id', 'product_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_date');
            $table->index('business_day_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_available', 'sort_order']);
        });

        Schema::table('business_days', function (Blueprint $table) {
            $table->index(['status', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_days', function (Blueprint $table) {
            $table->dropIndex(['status', 'date']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_available', 'sort_order']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_date']);
            $table->dropIndex(['business_day_id']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['sale_id', 'product_id']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['business_day_id', 'status']);
            $table->dropIndex(['payment_method']);
        });
    }
};
