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
        Schema::create('stay_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->dateTime('check_in');
            $table->dateTime('expected_check_out')->nullable();
            $table->dateTime('actual_check_out')->nullable();
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->decimal('price_per_night', 8, 2);
            $table->decimal('advance_payment', 8, 2)->default(0);
            $table->string('payment_mode')->nullable(); // Cash, UPI, Card
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->string('status')->default('Active'); // Active, Completed
            $table->string('purpose')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stay_records');
    }
};
