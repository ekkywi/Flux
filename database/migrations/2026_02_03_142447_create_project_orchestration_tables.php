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
            $table->string('slug')->unique();
            $table->string('stack_type')->default('laravel');
            $table->jsonb('stack_options')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_members', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_owner')->default(false);
            $table->timestamps();
            $table->unique(['project_id', 'user_id']);
        });

        Schema::create('project_environments', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignUuid('server_app_id')->nullable()->constrained('servers');
            $table->foreignUuid('server_db_id')->nullable()->constrained('servers');
            $table->jsonb('env_vars')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_environtments');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('project');
    }
};
