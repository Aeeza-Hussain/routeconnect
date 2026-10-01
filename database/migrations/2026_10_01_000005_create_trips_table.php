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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Driver
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->date('trip_date');
            $table->time('departure_time');
            $table->integer('available_seats');
            $table->decimal('fare', 8, 2)->nullable();
            $table->string('pickup_point')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, boarding, departed, delayed, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
