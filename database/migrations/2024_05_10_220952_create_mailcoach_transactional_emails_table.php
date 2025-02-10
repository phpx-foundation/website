<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::create('mailcoach_transactional_emails', function(Blueprint $table) {
			$table->snowflakeId();
			$table->foreignId('group_id')->constrained('groups');
			$table->string('action_name');
			$table->string('mail_name');
			$table->boolean('is_enabled')->default(false);
			$table->timestamps();
			$table->softDeletes();
			
			$table->unique(['group_id', 'action_name']);
		});
	}
	
	public function down(): void
	{
		Schema::dropIfExists('mailcoach_transactional_emails');
	}
};
