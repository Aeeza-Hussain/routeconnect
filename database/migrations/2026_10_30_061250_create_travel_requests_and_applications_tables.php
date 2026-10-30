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
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('pickup_location');
            $table->string('destination_location');
            $table->date('travel_date');
            $table->string('travel_time')->nullable();
            $table->unsignedInteger('seats_needed')->default(1);
            $table->decimal('budget', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('open'); // open, negotiating, confirmed, completed, cancelled
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['travel_date', 'status']);
        });

        Schema::create('driver_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_request_id')->constrained('travel_requests')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->decimal('offered_fare', 10, 2)->default(0.00);
            $table->string('proposed_time')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 30)->default('pending'); // pending, negotiating, accepted, rejected, confirmed, cancelled
            $table->timestamps();

            $table->index(['travel_request_id', 'status']);
            $table->index(['driver_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_applications');
        Schema::dropIfExists('travel_requests');
    }
};
