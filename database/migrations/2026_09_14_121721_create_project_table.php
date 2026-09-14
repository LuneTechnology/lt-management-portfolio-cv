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

        // 1. Buat dulu kolom Foreign Key-nya
        $table->unsignedBigInteger('id_category');

        $table->timestamps();

        // 2. Pasang relasi Foreign Key (dengan atau tanpa pengecekan)
        if (Schema::hasTable('category')) {
            $table->foreign('id_category')
                  ->references('id_category')
                  ->on('category')
                  ->onDelete('cascade');
        }
    });
}

public function down(): void
{
    Schema::dropIfExists('project');
}

    /**
     * Reverse the migrations.
     */
};
