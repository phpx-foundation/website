<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::create('rsvps', function(Blueprint $table) {
			$table->snowflakeId();
			$table->foreignId('meetup_id')->constrained('meetups');
			$table->foreignId('user_id')->constrained('users');
			$table->timestamps();
		});
	}
	
	public function down(): void
	{
		Schema::dropIfExists('rsvps');
	}
};
