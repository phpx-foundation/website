<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('bsky_did')->nullable();
            $table->text('bsky_app_password')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('bsky_did');
            $table->dropColumn('bsky_app_password');
        });
    }
};
