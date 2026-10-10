<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stack_types', function (Blueprint $table) {
            $table->id('id_stack_type');
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_types');
    }
};