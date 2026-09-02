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
        // 1. Update Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('seller')->after('email'); // 'admin' or 'seller'
            $table->string('phone')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('phone');
        });

        // 2. Categories Table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->default('🍔');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->string('image_emoji')->nullable()->default('🍔');
            $table->string('image_path')->nullable();
            $table->integer('current_stock')->default(0);
            $table->boolean('track_inventory')->default(false);
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Business Days Table
        Schema::create('business_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('status')->default('open'); // 'open', 'closed'
            $table->decimal('opening_cash_float', 10, 2)->default(0);
            $table->decimal('closing_cash_amount', 10, 2)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('opened_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Sales Table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_day_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('total_profit', 10, 2)->default(0);
            $table->integer('total_items_count')->default(1);
            $table->string('payment_method')->default('cash'); // 'cash', 'bkash', 'nagad', 'card'
            $table->string('status')->default('completed'); // 'completed', 'cancelled'
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Sale Items Table
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('profit', 10, 2)->default(0);
            $table->timestamps();
        });

        // 7. Expenses Table
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_day_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('category')->default('raw_materials'); // 'raw_materials', 'utilities', 'transport', 'rent', 'salaries', 'maintenance', 'other'
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Inventory Logs Table
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('sale'); // 'sale', 'restock', 'adjustment', 'waste'
            $table->integer('quantity_change');
            $table->integer('remaining_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 9. Cart Settings Table
        Schema::create('cart_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_settings');
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('business_days');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'is_active']);
        });
    }
};
