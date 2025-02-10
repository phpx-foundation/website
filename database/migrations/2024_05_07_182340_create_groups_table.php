<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->snowflakeId();
            $table->string('domain')->unique();
            $table->string('name')->unique();
            $table->string('twitter_url')->nullable();
            $table->string('meetup_url')->nullable();
            $table->string('timezone')->default('America/New_York');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
