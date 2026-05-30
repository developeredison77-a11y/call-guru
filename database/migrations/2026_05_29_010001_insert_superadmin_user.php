<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $existing = DB::table('users')->where('email', 'superadmin@callguru.com')->first();

        if ($existing) {
            DB::table('users')
                ->where('id', $existing->id)
                ->update([
                    'name' => 'Super Admin',
                    'password' => Hash::make('password'),
                    'type' => 1,
                    'updated_at' => now(),
                ]);

            return;
        }

        $mobileNumber = '9999999999';

        for ($number = 9999999999; $number >= 9999999900; $number--) {
            $candidate = (string) $number;

            $exists = DB::table('users')
                ->where('country_code', '+91')
                ->where('mobile_number', $candidate)
                ->exists();

            if (! $exists) {
                $mobileNumber = $candidate;
                break;
            }
        }

        DB::table('users')->insert([
                'name' => 'Super Admin',
                'email' => 'superadmin@callguru.com',
                'password' => Hash::make('password'),
                'type' => 1,
                'country_code' => '+91',
                'mobile_number' => $mobileNumber,
                'date_of_birth' => null,
                'sex' => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'superadmin@callguru.com')
            ->where('type', 1)
            ->delete();
    }
};
