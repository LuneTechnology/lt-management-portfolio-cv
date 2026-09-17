<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stack', function (Blueprint $table) {
            $table->id('id_stack');
            $table->string('nama');
            $table->string('tag', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack');
    }
};