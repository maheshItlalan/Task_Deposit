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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code');
            $table->foreign('customer_code')->references('customer_code')->on('customers');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('method_id')->constrained('methods');
            $table->decimal('amount', 10, 2);
            $table->decimal('converted_amount', 10, 2)->nullable(); // Converted amount in EUR
            $table->enum('status', ['success', 'failed']);
            $table->timestamp('deposit_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
