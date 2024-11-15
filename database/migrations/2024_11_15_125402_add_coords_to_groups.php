<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->after('timezone', function(Blueprint $table) {
				$table->decimal('latitude', 10, 7)->nullable();
				$table->decimal('longitude', 10, 7)->nullable();
			});
		});
		
		Schema::table('external_groups', function(Blueprint $table) {
			$table->after('region', function(Blueprint $table) {
				$table->decimal('latitude', 10, 7)->nullable();
				$table->decimal('longitude', 10, 7)->nullable();
			});
		});
	}
	
	public function down(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->dropColumn('latitude');
			$table->dropColumn('longitude');
		});
		
		Schema::table('external_groups', function(Blueprint $table) {
			$table->dropColumn('latitude');
			$table->dropColumn('longitude');
		});
	}
};
