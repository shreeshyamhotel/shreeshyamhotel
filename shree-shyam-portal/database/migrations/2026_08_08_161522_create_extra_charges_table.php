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
        Schema::create('extra_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_record_id')->constrained('stay_records')->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->string('description'); // e.g., "Restaurant Bill", "Laundry Service"
            $table->string('bill_number')->nullable(); // optional invoice reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_charges');
    }
};
