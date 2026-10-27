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
            if (!Schema::hasColumn('users', 'license_issue_date')) {
                $table->date('license_issue_date')->nullable()->after('license_no');
            }
            if (!Schema::hasColumn('users', 'license_expiry_date')) {
                $table->date('license_expiry_date')->nullable()->after('license_issue_date');
            }
            if (!Schema::hasColumn('users', 'cnic_front_image')) {
                $table->string('cnic_front_image')->nullable()->after('cnic');
            }
            if (!Schema::hasColumn('users', 'cnic_back_image')) {
                $table->string('cnic_back_image')->nullable()->after('cnic_front_image');
            }
            if (!Schema::hasColumn('users', 'license_front_image')) {
                $table->string('license_front_image')->nullable()->after('license_expiry_date');
            }
            if (!Schema::hasColumn('users', 'license_back_image')) {
                $table->string('license_back_image')->nullable()->after('license_front_image');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('users', 'privacy_accepted_at')) {
                $table->timestamp('privacy_accepted_at')->nullable()->after('terms_accepted_at');
            }
            if (!Schema::hasColumn('users', 'declaration_confirmed_at')) {
                $table->timestamp('declaration_confirmed_at')->nullable()->after('privacy_accepted_at');
            }
            if (!Schema::hasColumn('users', 'verification_consent_at')) {
                $table->timestamp('verification_consent_at')->nullable()->after('declaration_confirmed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'license_issue_date',
                'license_expiry_date',
                'cnic_front_image',
                'cnic_back_image',
                'license_front_image',
                'license_back_image',
                'emergency_contact_name',
                'emergency_contact_phone',
                'terms_accepted_at',
                'privacy_accepted_at',
                'declaration_confirmed_at',
                'verification_consent_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
