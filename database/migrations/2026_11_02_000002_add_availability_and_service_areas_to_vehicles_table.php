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
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'rental_rate_per_hour')) {
                $table->decimal('rental_rate_per_hour', 10, 2)->nullable()->after('rental_rate_per_day');
            }
            if (!Schema::hasColumn('vehicles', 'pickup_location')) {
                $table->string('pickup_location')->nullable()->after('rental_rate_per_hour');
            }
            if (!Schema::hasColumn('vehicles', 'service_area')) {
                $table->string('service_area')->nullable()->after('pickup_location');
            }
            if (!Schema::hasColumn('vehicles', 'available_from')) {
                $table->dateTime('available_from')->nullable()->after('service_area');
            }
            if (!Schema::hasColumn('vehicles', 'available_to')) {
                $table->dateTime('available_to')->nullable()->after('available_from');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $drops = [];
            foreach (['rental_rate_per_hour', 'pickup_location', 'service_area', 'available_from', 'available_to'] as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $drops[] = $column;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};

