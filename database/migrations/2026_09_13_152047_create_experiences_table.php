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
    Schema::create('experiences', function (Blueprint $table) 
        {
            $table->id('id_experience');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_work');
            $table->unsignedBigInteger('id_position_type');
            $table->unsignedBigInteger('id_work_type');
            $table->unsignedBigInteger('id_project');
            $table->timestamps();

            $table->foreign('id_work')->references('id_work')->on('works')->onDelete('cascade');
            $table->foreign('id_position_type')->references('id_position_type')->on('position_types')->onDelete('cascade');
            $table->foreign('id_work_type')->references('id_work_type')->on('work_types')->onDelete('cascade');

            if (Schema::hasTable('users')) {
                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            }

            if (Schema::hasTable('projects')) {
                $table->foreign('id_project')->references('id_project')->on('projects')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
