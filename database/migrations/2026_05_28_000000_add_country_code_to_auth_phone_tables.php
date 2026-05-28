<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code', 5)->default('+91')->after('name');
            $table->dropUnique(['mobile_number']);
            $table->unique(['country_code', 'mobile_number']);
        });

        Schema::table('otp_verifications', function (Blueprint $table) {
            $table->string('country_code', 5)->default('+91')->after('id');
            $table->dropUnique(['mobile_number']);
            $table->unique(['country_code', 'mobile_number']);
        });
    }

    public function down(): void
    {
        Schema::table('otp_verifications', function (Blueprint $table) {
            $table->dropUnique(['country_code', 'mobile_number']);
            $table->dropColumn('country_code');
            $table->unique('mobile_number');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['country_code', 'mobile_number']);
            $table->dropColumn('country_code');
            $table->unique('mobile_number');
        });
    }
};
