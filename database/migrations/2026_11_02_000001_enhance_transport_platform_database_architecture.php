<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Shared Ride Listings: Add total_seats and performance indexes to trips table
        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'total_seats')) {
                $table->unsignedInteger('total_seats')->nullable()->after('departure_time');
            }

            // Performance indexes for scheduled listings & availability queries
            $table->index(['trip_date', 'status'], 'trips_trip_date_status_index');
            $table->index(['status', 'departure_time'], 'trips_status_departure_time_index');
        });

        // Database-agnostic backfill: total_seats on existing trips from vehicles
        if (Schema::hasTable('trips') && Schema::hasTable('vehicles')) {
            $trips = DB::table('trips')->whereNull('total_seats')->get();
            foreach ($trips as $trip) {
                $vehicle = DB::table('vehicles')->where('id', $trip->vehicle_id)->first();
                if ($vehicle && isset($vehicle->total_seats)) {
                    DB::table('trips')->where('id', $trip->id)->update(['total_seats' => $vehicle->total_seats]);
                }
            }
        }

        // 2. Driver Applications: Add available_seats
        Schema::table('driver_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('driver_applications', 'available_seats')) {
                $table->unsignedInteger('available_seats')->nullable()->after('vehicle_id');
            }
        });

        // 3. Passenger Travel Requests: Add intermediate stops, preferred vehicle type, expiry, and single driver selection
        Schema::table('travel_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_requests', 'intermediate_stops')) {
                $table->json('intermediate_stops')->nullable()->after('destination_location');
            }
            if (!Schema::hasColumn('travel_requests', 'preferred_vehicle_type')) {
                $table->string('preferred_vehicle_type', 50)->nullable()->after('seats_needed');
            }
            if (!Schema::hasColumn('travel_requests', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('travel_requests', 'selected_application_id')) {
                $table->foreignId('selected_application_id')
                    ->nullable()
                    ->after('expires_at')
                    ->constrained('driver_applications')
                    ->nullOnDelete();
            }

            // Indexes for open request matching and expiry monitoring
            $table->index(['status', 'travel_date'], 'travel_requests_status_travel_date_index');
            $table->index(['expires_at', 'status'], 'travel_requests_expires_at_status_index');
        });

        // 4. Private Vehicle Bookings & Shared Bookings: Add direct driver reference and availability indexes
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'driver_id')) {
                $table->foreignId('driver_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Fast availability and conflict prevention indexes
            $table->index(
                ['vehicle_id', 'start_datetime', 'end_datetime', 'booking_status'],
                'bookings_vehicle_dates_status_index'
            );
            $table->index(['driver_id', 'booking_status'], 'bookings_driver_status_index');
            $table->index(['booking_type', 'booking_status'], 'bookings_type_status_index');
        });

        // Database-agnostic backfill: driver_id on existing bookings
        if (Schema::hasTable('bookings')) {
            $bookings = DB::table('bookings')->whereNull('driver_id')->get();
            foreach ($bookings as $booking) {
                $driverId = null;
                if (!empty($booking->trip_id) && Schema::hasTable('trips')) {
                    $driverId = DB::table('trips')->where('id', $booking->trip_id)->value('user_id');
                } elseif (!empty($booking->vehicle_id) && Schema::hasTable('vehicles')) {
                    $driverId = DB::table('vehicles')->where('id', $booking->vehicle_id)->value('user_id');
                }
                if ($driverId) {
                    DB::table('bookings')->where('id', $booking->id)->update(['driver_id' => $driverId]);
                }
            }
        }

        // 5. Messaging Architecture: Add direct relational references on conversations
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'trip_id')) {
                $table->foreignId('trip_id')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('trips')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('conversations', 'booking_id')) {
                $table->foreignId('booking_id')
                    ->nullable()
                    ->after('trip_id')
                    ->constrained('bookings')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('conversations', 'travel_request_id')) {
                $table->foreignId('travel_request_id')
                    ->nullable()
                    ->after('booking_id')
                    ->constrained('travel_requests')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('conversations', 'driver_application_id')) {
                $table->foreignId('driver_application_id')
                    ->nullable()
                    ->after('travel_request_id')
                    ->constrained('driver_applications')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop foreign keys and columns on conversations
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'driver_application_id')) {
                $table->dropConstrainedForeignId('driver_application_id');
            }
            if (Schema::hasColumn('conversations', 'travel_request_id')) {
                $table->dropConstrainedForeignId('travel_request_id');
            }
            if (Schema::hasColumn('conversations', 'booking_id')) {
                $table->dropConstrainedForeignId('booking_id');
            }
            if (Schema::hasColumn('conversations', 'trip_id')) {
                $table->dropConstrainedForeignId('trip_id');
            }
        });

        // 2. Drop foreign keys first, then indexes and columns on bookings
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'driver_id')) {
                $table->dropConstrainedForeignId('driver_id');
            }
            if ($this->hasIndex('bookings', 'bookings_vehicle_dates_status_index')) {
                $table->dropIndex('bookings_vehicle_dates_status_index');
            }
            if ($this->hasIndex('bookings', 'bookings_driver_status_index')) {
                $table->dropIndex('bookings_driver_status_index');
            }
            if ($this->hasIndex('bookings', 'bookings_type_status_index')) {
                $table->dropIndex('bookings_type_status_index');
            }
        });

        // 3. Drop foreign keys first, then indexes and columns on travel_requests
        Schema::table('travel_requests', function (Blueprint $table) {
            if (Schema::hasColumn('travel_requests', 'selected_application_id')) {
                $table->dropConstrainedForeignId('selected_application_id');
            }
            if ($this->hasIndex('travel_requests', 'travel_requests_status_travel_date_index')) {
                $table->dropIndex('travel_requests_status_travel_date_index');
            }
            if ($this->hasIndex('travel_requests', 'travel_requests_expires_at_status_index')) {
                $table->dropIndex('travel_requests_expires_at_status_index');
            }

            $drops = [];
            if (Schema::hasColumn('travel_requests', 'expires_at')) {
                $drops[] = 'expires_at';
            }
            if (Schema::hasColumn('travel_requests', 'preferred_vehicle_type')) {
                $drops[] = 'preferred_vehicle_type';
            }
            if (Schema::hasColumn('travel_requests', 'intermediate_stops')) {
                $drops[] = 'intermediate_stops';
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });

        // 4. Drop columns on driver_applications
        Schema::table('driver_applications', function (Blueprint $table) {
            if (Schema::hasColumn('driver_applications', 'available_seats')) {
                $table->dropColumn('available_seats');
            }
        });

        // 5. Drop indexes and columns on trips
        Schema::table('trips', function (Blueprint $table) {
            if ($this->hasIndex('trips', 'trips_trip_date_status_index')) {
                $table->dropIndex('trips_trip_date_status_index');
            }
            if ($this->hasIndex('trips', 'trips_status_departure_time_index')) {
                $table->dropIndex('trips_status_departure_time_index');
            }
            if (Schema::hasColumn('trips', 'total_seats')) {
                $table->dropColumn('total_seats');
            }
        });
    }

    /**
     * Check if a table has an index.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$table}')");
            return in_array($indexName, array_column($indexes, 'name'));
        }

        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return !empty($indexes);
    }
};
