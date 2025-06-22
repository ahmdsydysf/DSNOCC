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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('project_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('folders')->onDelete('cascade');
            $table->string('name');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('folder_id')->nullable()->constrained('folders')->onDelete('cascade');
            $table->string('name');
            $table->string('path');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('projects');
    }
};
