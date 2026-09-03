<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_day_id')->nullable()->constrained('business_days')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event'); // 'opened', 'closed'
            $table->timestamp('occurred_at')->index();
            $table->decimal('opening_cash_float', 10, 2)->nullable();
            $table->decimal('closing_cash_amount', 10, 2)->nullable();
            $table->decimal('closing_cost', 10, 2)->nullable();
            $table->decimal('sales_total', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Backfill existing business_days records if any exist
        try {
            $days = DB::table('business_days')->orderBy('id')->get();
            foreach ($days as $day) {
                if ($day->opened_at) {
                    DB::table('cart_status_logs')->insert([
                        'business_day_id' => $day->id,
                        'user_id' => $day->opened_by_id,
                        'event' => 'opened',
                        'occurred_at' => $day->opened_at,
                        'opening_cash_float' => $day->opening_cash_float ?? 0,
                        'notes' => 'Shift opened',
                        'created_at' => $day->opened_at,
                        'updated_at' => $day->opened_at,
                    ]);
                }

                if ($day->closed_at) {
                    DB::table('cart_status_logs')->insert([
                        'business_day_id' => $day->id,
                        'user_id' => $day->closed_by_id,
                        'event' => 'closed',
                        'occurred_at' => $day->closed_at,
                        'closing_cash_amount' => $day->closing_cash_amount,
                        'closing_cost' => $day->closing_cost ?? 0,
                        'notes' => $day->notes ?? 'Shift closed',
                        'created_at' => $day->closed_at,
                        'updated_at' => $day->closed_at,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore during rollback/fresh
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_status_logs');
    }
};
