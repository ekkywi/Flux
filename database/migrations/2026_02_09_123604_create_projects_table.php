<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('repository_url');
            $table->string('branch')->default('main');
            $table->string('stack')->default('laravel');
            $table->json('build_options')->nullable();
            $table->string('build_command')->nullable();
            $table->string('output_dir')->nullable();
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
