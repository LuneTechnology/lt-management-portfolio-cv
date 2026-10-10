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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id('id_achievement');

            $table->string('name', 255);
            $table->string('place', 255);
            $table->dateTime('date');

            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            $table->foreignId('id_project')
                ->nullable()
                ->constrained('project', 'id_project')
                ->nullOnDelete();

            $table->foreignId('id_category')
                ->constrained('category', 'id_category')
                ->restrictOnDelete();

            $table->string('description')->nullable();
            $table->string('type', 20);
            $table->string('status', 20);
            $table->string('logo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};