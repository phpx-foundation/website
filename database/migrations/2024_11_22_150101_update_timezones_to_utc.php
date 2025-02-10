<?php

use App\Models\Meetup;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
	public function up(): void
	{
		$this->changeTimezones('America/New_York', 'UTC');
	}
	
	public function down(): void
	{
		$this->changeTimezones('UTC', 'America/New_York');
	}
	
	protected function changeTimezones(string $from, string $to): void
	{
		config()->set('app.timezone', $from);
		
		Meetup::query()
			->withWhereHas('group')
			->orderBy('id')
			->each(function(Meetup $meetup) use ($from, $to) {
				$starts_at = $meetup->starts_at->clone();
				$ends_at = $meetup->ends_at->clone();
				
				try {
					config()->set('app.timezone', $to);
					
					$meetup->starts_at = $starts_at;
					$meetup->ends_at = $ends_at;
					$meetup->save();
				} finally {
					config()->set('app.timezone', $from);
				}
			});
		
		config()->set('app.timezone', $to);
	}
};
