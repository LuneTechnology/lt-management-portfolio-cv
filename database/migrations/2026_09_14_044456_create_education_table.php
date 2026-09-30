<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->id('id_education');

            $table->string('name');
            $table->string('major');
            $table->string('place');
            $table->string('level', 20);

            $table->date('date_in');
            $table->date('date_out')->nullable();

            $table->decimal('gpa', 3, 2)->nullable();

            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};