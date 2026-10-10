<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_images', function (Blueprint $table) {
            $table->id('id_project_image');
            $table->foreignId('id_project')
                ->constrained('project', 'id_project')
                ->cascadeOnDelete();
            $table->string('path', 2048);
            $table->string('alt_text', 255)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['id_project', 'sort_order']);
            $table->index(['id_project', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_images');
    }
};
