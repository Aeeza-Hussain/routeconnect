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
        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'expected_arrival_time')) {
                $table->time('expected_arrival_time')->nullable()->after('departure_time');
            }
            if (!Schema::hasColumn('trips', 'notes')) {
                $table->text('notes')->nullable()->after('pickup_point');
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('driver_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('trips', 'expected_arrival_time')) $drops[] = 'expected_arrival_time';
            if (Schema::hasColumn('trips', 'notes')) $drops[] = 'notes';
            if (!empty($drops)) $table->dropColumn($drops);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
