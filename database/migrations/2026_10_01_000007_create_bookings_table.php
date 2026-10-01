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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Passenger
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('from_stop_id')->constrained('stops')->onDelete('cascade');
            $table->foreignId('to_stop_id')->constrained('stops')->onDelete('cascade');
            $table->integer('seats')->default(1);
            $table->string('seat_numbers')->nullable(); // e.g. "4, 5"
            $table->decimal('total_fare', 8, 2)->nullable();
            $table->string('booking_reference')->nullable()->unique();
            $table->string('status')->default('confirmed'); // confirmed, cancelled, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
