<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_stack', function (Blueprint $table) {
            $table->foreignId('id_experience')
                ->constrained('experiences', 'id_experience')
                ->cascadeOnDelete();

            $table->foreignId('id_stack')
                ->constrained('stack', 'id_stack')
                ->cascadeOnDelete();

            $table->unique([
                'id_experience',
                'id_stack'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_stack');
    }
};