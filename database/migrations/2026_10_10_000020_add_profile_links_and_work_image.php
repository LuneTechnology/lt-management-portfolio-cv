<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            if (!Schema::hasColumn('works', 'image')) {
                $table->string('image', 2048)->nullable()->after('place');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'social_links')) {
                $table->json('social_links')->nullable()->after('aboutme');
            }
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            if (Schema::hasColumn('works', 'image')) $table->dropColumn('image');
        });
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'social_links')) $table->dropColumn('social_links');
        });
    }
};
