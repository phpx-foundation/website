<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->dropColumn('og_asset');
		});
	}
	
	public function down(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->string('og_asset')->nullable();
		});
	}
};
