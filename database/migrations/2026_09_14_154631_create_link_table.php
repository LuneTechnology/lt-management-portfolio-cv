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
    Schema::create('link', function (Blueprint $table) {
        $table->id('id_link');
        $table->string('name', 50)->nullable();
        $table->string('link', 50)->nullable();

        // 1. Buat dulu kolom Foreign Key-nya
        $table->unsignedBigInteger('id_project');

        $table->timestamps();

        // 2. Pasang relasi Foreign Key (dengan atau tanpa pengecekan)
        if (Schema::hasTable('project')) {
            $table->foreign('id_project')
                  ->references('id_project')
                  ->on('project')
                  ->onDelete('cascade');
        }
    });
}

public function down(): void
{
    Schema::dropIfExists('link');
}

    /**
     * Reverse the migrations.
     */
};