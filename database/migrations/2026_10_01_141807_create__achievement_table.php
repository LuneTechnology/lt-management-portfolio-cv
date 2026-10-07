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
    $table->string('name', 255);          // Title
    $table->string('place', 255);         // Issuer / Organizer
    $table->dateTime('date');
    $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
      $table->unsignedBigInteger('id_project')->nullable();

    // tambahan agar sesuai tampilan
    $table->foreignId('id_category')->nullable()->constrained('categories', 'id_category');
    $table->string('description')->nullable();
    $table->string('type', 20);           // Award, Training
    $table->string('status', 20);         // Verified, Completed
    $table->string('logo')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_achievement');
    }
};
