<?php

use App\Enums\Continent;
use App\Models\ExternalGroup;
use App\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->string('continent', 20)->after('region')->default(Continent::NorthAmerica);
		});
		
		Schema::table('external_groups', function(Blueprint $table) {
			$table->string('continent', 20)->after('region')->default(Continent::NorthAmerica);
		});
		
		Group::findByDomain('phpxvie.com')?->update(['continent' => Continent::Europe]);
		Group::findByDomain('phpxpnq.com')?->update(['continent' => Continent::Asia]);
		Group::findByDomain('phpxcebu.com')?->update(['continent' => Continent::Asia]);
		Group::findByDomain('phpxadl.com')?->update(['continent' => Continent::Australia]);
		
		ExternalGroup::findByDomain('phpstoke.co.uk')?->update(['continent' => Continent::Europe]);
		ExternalGroup::findByDomain('laravel.swiss')?->update(['continent' => Continent::Europe]);
	}
	
	public function down(): void
	{
		Schema::table('groups', function(Blueprint $table) {
			$table->dropColumn('continent');
		});
	}
};
