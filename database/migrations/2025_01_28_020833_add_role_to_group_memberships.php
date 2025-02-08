<?php

use App\Enums\GroupRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('group_memberships', function(Blueprint $table) {
			$table->string('role')->after('is_subscribed')->default(GroupRole::Attendee);
		});
	}
	
	public function down(): void
	{
		Schema::table('group_memberships', function(Blueprint $table) {
			$table->dropColumn('role');
		});
	}
};
