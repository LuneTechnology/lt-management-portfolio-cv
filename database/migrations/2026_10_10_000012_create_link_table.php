<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('link', function (Blueprint $table) {
            $table->id('id_link');
            $table->string('name', 50)->nullable();
            $table->string('link', 2048)->nullable();
            $table->foreignId('id_project')
                ->constrained('project', 'id_project')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link');
    }
};
