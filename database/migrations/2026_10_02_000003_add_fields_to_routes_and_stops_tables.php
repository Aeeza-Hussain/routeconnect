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
        Schema::table('routes', function (Blueprint $table) {
            if (!Schema::hasColumn('routes', 'start_location')) {
                $table->string('start_location')->nullable()->after('name');
            }
            if (!Schema::hasColumn('routes', 'end_location')) {
                $table->string('end_location')->nullable()->after('start_location');
            }
            if (!Schema::hasColumn('routes', 'status')) {
                $table->string('status')->default('Active')->after('end_location');
            }
        });

        Schema::table('stops', function (Blueprint $table) {
            if (!Schema::hasColumn('stops', 'location')) {
                $table->string('location')->nullable()->after('name');
            }
            if (!Schema::hasColumn('stops', 'status')) {
                $table->string('status')->default('Active')->after('location');
            }
        });

        Schema::table('route_stops', function (Blueprint $table) {
            $table->unique(['route_id', 'stop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route_stops', function (Blueprint $table) {
            $table->dropUnique(['route_id', 'stop_id']);
        });

        Schema::table('stops', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('stops', 'location')) $drops[] = 'location';
            if (Schema::hasColumn('stops', 'status')) $drops[] = 'status';
            if (!empty($drops)) $table->dropColumn($drops);
        });

        Schema::table('routes', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('routes', 'start_location')) $drops[] = 'start_location';
            if (Schema::hasColumn('routes', 'end_location')) $drops[] = 'end_location';
            if (Schema::hasColumn('routes', 'status')) $drops[] = 'status';
            if (!empty($drops)) $table->dropColumn($drops);
        });
    }
};
