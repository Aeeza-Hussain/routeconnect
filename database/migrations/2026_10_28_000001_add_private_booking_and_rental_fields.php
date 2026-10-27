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
        // 1. Add rental configuration fields to vehicles table
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'rental_rate_per_day')) {
                $table->decimal('rental_rate_per_day', 10, 2)->default(4000.00)->after('total_seats');
            }
            if (!Schema::hasColumn('vehicles', 'is_available_for_rental')) {
                $table->boolean('is_available_for_rental')->default(true)->after('rental_rate_per_day');
            }
            if (!Schema::hasColumn('vehicles', 'image')) {
                $table->string('image')->nullable()->after('is_available_for_rental');
            }
            if (!Schema::hasColumn('vehicles', 'description')) {
                $table->text('description')->nullable()->after('image');
            }
        });

        // 2. Add private booking fields to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            // Make trip_id nullable for private vehicle bookings
            $table->unsignedBigInteger('trip_id')->nullable()->change();

            if (!Schema::hasColumn('bookings', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->after('trip_id')->constrained('vehicles')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'booking_type')) {
                $table->string('booking_type', 30)->default('shared')->after('vehicle_id'); // 'shared' or 'private'
            }
            if (!Schema::hasColumn('bookings', 'pickup_location')) {
                $table->string('pickup_location')->nullable()->after('to_stop_id');
            }
            if (!Schema::hasColumn('bookings', 'destination_location')) {
                $table->string('destination_location')->nullable()->after('pickup_location');
            }
            if (!Schema::hasColumn('bookings', 'start_datetime')) {
                $table->dateTime('start_datetime')->nullable()->after('destination_location');
            }
            if (!Schema::hasColumn('bookings', 'end_datetime')) {
                $table->dateTime('end_datetime')->nullable()->after('start_datetime');
            }
            if (!Schema::hasColumn('bookings', 'days')) {
                $table->unsignedInteger('days')->nullable()->after('end_datetime');
            }
            if (!Schema::hasColumn('bookings', 'daily_rate')) {
                $table->decimal('daily_rate', 10, 2)->nullable()->after('days');
            }
            if (!Schema::hasColumn('bookings', 'passenger_notes')) {
                $table->text('passenger_notes')->nullable()->after('daily_rate');
            }
        });

        // 3. Create vehicle unavailabilities / blocked dates table
        if (!Schema::hasTable('vehicle_unavailabilities')) {
            Schema::create('vehicle_unavailabilities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->string('reason')->nullable();
                $table->timestamps();

                $table->index(['vehicle_id', 'start_date', 'end_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_unavailabilities');

        Schema::table('bookings', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('bookings', 'passenger_notes')) $drops[] = 'passenger_notes';
            if (Schema::hasColumn('bookings', 'daily_rate')) $drops[] = 'daily_rate';
            if (Schema::hasColumn('bookings', 'days')) $drops[] = 'days';
            if (Schema::hasColumn('bookings', 'end_datetime')) $drops[] = 'end_datetime';
            if (Schema::hasColumn('bookings', 'start_datetime')) $drops[] = 'start_datetime';
            if (Schema::hasColumn('bookings', 'destination_location')) $drops[] = 'destination_location';
            if (Schema::hasColumn('bookings', 'pickup_location')) $drops[] = 'pickup_location';
            if (Schema::hasColumn('bookings', 'booking_type')) $drops[] = 'booking_type';

            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('vehicles', 'description')) $drops[] = 'description';
            if (Schema::hasColumn('vehicles', 'image')) $drops[] = 'image';
            if (Schema::hasColumn('vehicles', 'is_available_for_rental')) $drops[] = 'is_available_for_rental';
            if (Schema::hasColumn('vehicles', 'rental_rate_per_day')) $drops[] = 'rental_rate_per_day';

            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
