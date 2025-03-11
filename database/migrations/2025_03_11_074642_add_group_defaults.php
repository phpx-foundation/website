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
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('default_capacity')->nullable()->after('frequency');
            $table->time('default_end')->nullable()->after('frequency');
            $table->time('default_start')->nullable()->after('frequency');
            $table->string('default_location')->nullable()->after('frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('default_capacity');
            $table->dropColumn('default_end');
            $table->dropColumn('default_start');
            $table->dropColumn('default_location');
        });
    }
};
