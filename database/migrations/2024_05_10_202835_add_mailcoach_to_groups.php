<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->after('timezone', function(Blueprint $table) {
				$table->text('mailcoach_token')->nullable();
				$table->string('mailcoach_endpoint')->nullable();
				$table->string('mailcoach_list')->nullable();
			});
		});
	}
	
	public function down(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->dropColumn('mailcoach_token');
			$table->dropColumn('mailcoach_endpoint');
			$table->dropColumn('mailcoach_list');
		});
	}
};
