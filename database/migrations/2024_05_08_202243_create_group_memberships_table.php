<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_memberships', function (Blueprint $table) {
            $table->snowflakeId();
            $table->foreignId('group_id')->constrained('groups');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_subscribed')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'group_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_group_id')->nullable()->after('id')->constrained('groups');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('current_group_id');
        });

        Schema::dropIfExists('group_memberships');
    }
};
