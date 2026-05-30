<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('status')->default(1)->comment('inactive=0,active=1');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('terms_and_conditions', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->unsignedTinyInteger('status')->default(1)->comment('inactive=0,active=1');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('faqs', function (Blueprint $table): void {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedTinyInteger('status')->default(1)->comment('inactive=0,active=1');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('terms_and_conditions');
        Schema::dropIfExists('categories');
    }
};
