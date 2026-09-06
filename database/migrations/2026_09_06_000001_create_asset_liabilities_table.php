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
        Schema::create('asset_liabilities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'asset' or 'liability'
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->date('record_date');
            $table->time('record_time');
            $table->timestamps();

            $table->index(['type', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_liabilities');
    }
};
