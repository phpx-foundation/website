<?php

use App\Enums\DomainStatus;
use App\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->string('domain_status')->default(DomainStatus::Pending)->index()->after('domain');
		});
		
		Group::query()->update(['domain_status' => DomainStatus::Confirmed]);
	}
	
	public function down(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->dropColumn('domain_status');
		});
	}
};
