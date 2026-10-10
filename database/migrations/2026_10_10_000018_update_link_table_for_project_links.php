<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('link')) {
            return;
        }

        Schema::table('link', function (Blueprint $table) {
            $table->string('name', 100)->nullable()->change();
            $table->text('link')->nullable()->change();
        });

        if (!Schema::hasColumn('link', 'type')) {
            Schema::table('link', function (Blueprint $table) {
                $table->string('type', 30)->default('other')->after('link');
            });
        }

        if (!Schema::hasColumn('link', 'sort_order')) {
            Schema::table('link', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('type');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('link')) {
            return;
        }

        if (Schema::hasColumn('link', 'sort_order')) {
            Schema::table('link', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasColumn('link', 'type')) {
            Schema::table('link', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        Schema::table('link', function (Blueprint $table) {
            $table->string('name', 50)->nullable()->change();
            $table->string('link', 2048)->nullable()->change();
        });
    }
};
