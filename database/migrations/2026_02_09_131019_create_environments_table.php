<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('environments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('server_id')->nullable()->constrained('servers')->onDelete('set null');
            $table->integer('port')->nullable();
            $table->string('name');
            $table->string('branch')->default('main');
            $table->string('url')->nullable();
            $table->enum('type', ['production', 'staging', 'development'])->default('development');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('environments');
    }
};
