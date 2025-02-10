<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetups', function (Blueprint $table) {
            $table->snowflakeId();
            $table->foreignId('group_id')->constrained('groups');
            $table->string('location');
            $table->text('description');
            $table->integer('capacity');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetups');
    }
};
