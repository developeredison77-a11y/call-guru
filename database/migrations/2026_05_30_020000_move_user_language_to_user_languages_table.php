<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_languages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'language_id']);
        });

        DB::table('users')
            ->whereNotNull('language')
            ->orderBy('id')
            ->each(function (object $user): void {
                DB::table('user_languages')->insert([
                    'user_id' => $user->id,
                    'language_id' => $user->language,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['language']);
            $table->dropColumn('language');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedInteger('language')->nullable()->after('sex');
            $table->foreign('language')->references('id')->on('languages');
        });

        DB::table('user_languages')
            ->orderBy('id')
            ->each(function (object $userLanguage): void {
                DB::table('users')
                    ->where('id', $userLanguage->user_id)
                    ->whereNull('language')
                    ->update(['language' => $userLanguage->language_id]);
            });

        Schema::dropIfExists('user_languages');
    }
};
