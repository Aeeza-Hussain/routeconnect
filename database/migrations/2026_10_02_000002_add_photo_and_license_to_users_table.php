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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'license_no')) {
                $table->string('license_no')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('license_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('users', 'license_no')) {
                $drops[] = 'license_no';
            }
            if (Schema::hasColumn('users', 'profile_photo')) {
                $drops[] = 'profile_photo';
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
