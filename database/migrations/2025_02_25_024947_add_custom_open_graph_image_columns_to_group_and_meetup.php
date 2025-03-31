<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->string('custom_open_graph_image')->nullable();
		});

		Schema::table('meetups', function(Blueprint $table) {
			$table->string('custom_open_graph_image')->nullable();
		});
	}

	public function down(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->dropColumn('custom_open_graph_image');
		});

		Schema::table('meetups', function(Blueprint $table) {
			$table->dropColumn('custom_open_graph_image');
		});
	}
};
