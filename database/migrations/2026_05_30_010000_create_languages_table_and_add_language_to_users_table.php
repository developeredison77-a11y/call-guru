<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->unsignedTinyInteger('status')->default(1)->comment('inactive=0,active=1');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedInteger('language')->nullable()->after('sex');
            $table->foreign('language')->references('id')->on('languages');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['language']);
            $table->dropColumn('language');
        });

        Schema::dropIfExists('languages');
    }
};
