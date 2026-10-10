<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id('id_experience');

            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            $table->foreignId('id_work')
                ->constrained('works', 'id_work')
                ->restrictOnDelete();

            $table->foreignId('id_position_type')
                ->constrained('position_types', 'id_position_type')
                ->restrictOnDelete();

            $table->foreignId('id_work_type')
                ->constrained('work_types', 'id_work_type')
                ->restrictOnDelete();

            $table->foreignId('id_project')
                ->constrained('project', 'id_project')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->index(['id_project', 'id_user']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
