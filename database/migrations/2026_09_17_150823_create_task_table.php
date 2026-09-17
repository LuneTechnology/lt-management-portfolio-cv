<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id('id_task');

            $table->foreignId('id_experience')
                ->constrained('experiences', 'id_experience')
                ->cascadeOnDelete();

            $table->string('name', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task');
    }
};