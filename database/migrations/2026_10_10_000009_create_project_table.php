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
        Schema::create('project', function (Blueprint $table) {
            $table->id('id_project');

            $table->string('name', 50);

            $table->date('date_in');

            $table->date('date_out')->nullable();

            $table->foreignId('id_category')
                ->constrained('category', 'id_category')
                ->restrictOnDelete();

            $table->foreignId('id_work')
                ->constrained('works', 'id_work')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};