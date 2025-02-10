<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->after('name', function(Blueprint $table) {
				$table->text('description')->nullable();
				$table->string('og_asset')->nullable();
			});
		});
	}
	
	public function down(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->dropColumn('description');
			$table->dropColumn('og_asset');
		});
	}
};
