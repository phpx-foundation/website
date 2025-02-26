<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->after('mailcoach_list', function (Blueprint $table) {
				$table->string('turnstile_site_key')->nullable();
				$table->text('turnstile_secret_key')->nullable();
			});
		});
	}
	
	public function down(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->dropColumn('turnstile_site_key');
			$table->dropColumn('turnstile_secret_key');
		});
	}
};
