<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $current = DB::table('settings')->where('key', 'theme_color')->value('value');

        if ($current === null) {
            DB::table('settings')->insert([
                'key' => 'theme_color',
                'value' => '#ED701D',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        if (strtolower((string) $current) === '#2563eb') {
            DB::table('settings')
                ->where('key', 'theme_color')
                ->update([
                    'value' => '#ED701D',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->where('key', 'theme_color')
            ->where('value', '#ED701D')
            ->update([
                'value' => '#2563eb',
                'updated_at' => now(),
            ]);
    }
};
