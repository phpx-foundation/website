<?php

namespace App\Actions;

use App\Models\Group;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class ConfigureGroup
{
	use AsAction;
	
	public function handle(
		string $domain,
		string $name,
		string $region,
		string $description,
		string $timezone = 'America/New_York', // One true timezone
		?string $bsky_url = null,
		string|array $meetup_url = null,
	): Group {
		$group = Group::updateOrCreate([
			'domain' => $domain,
		], [
			'name' => $name,
			'region' => $region,
			'description' => $description,
			'timezone' => $timezone,
			'bsky_url' => $bsky_url ?: null,
			'meetup_url' => is_array($meetup_url) ? implode('|', $meetup_url) : $meetup_url,
		]);
		
		Cache::clear();
		
		SyncDomainsWithForge::run();
		
		app()->instance("group:{$domain}", $group);
		
		return $group;
	}
	
	public function getCommandSignature(): string
	{
		return 'group:configure {--external}';
	}
	
	public function asCommand(Command $command): int
	{
		if ($command->option('external')) {
			return ConfigureExternalGroup::make()->asCommand($command);
		}
		
		$domain = strtolower(text('What is the domain?', default: 'phpx', required: true));
		
		$existing = Group::where('domain', $domain)->firstOrNew(values: [
			'domain' => $domain,
			'name' => 'PHP×',
			'description' => 'A local PHP meetup for web artisans who want to learn and connect.',
			'timezone' => 'America/New_York',
		]);
		
		$name = text('What is the group name?', default: str($existing->name), required: true);
		$region = text('What is the group region?', default: str($existing->region), hint: 'eg. PHP×ATL might be "Atlanta"');
		$description = text('What is the description?', default: str($existing->description), required: true);
		$timezone = suggest('Timezone', DateTimeZone::listIdentifiers(), default: str($existing->timezone), required: true);
		$bsky_url = text('Is there a Bluesky URL?', default: str($existing->bsky_url));
		$meetup_url = text('Is there a Meetup URL?', default: str($existing->meetup_url));
		
		table(['Option', 'Value'], [
			['Name', $name],
			['Description', $description],
			['Timezone', $timezone],
			['Bluesky', $bsky_url],
			['Meetup', $meetup_url],
		]);
		
		if (confirm('Is this correct?')) {
			$group = $this->handle(
				domain: $domain,
				name: $name,
				region: $region,
				description: $description,
				timezone: $timezone,
				bsky_url: $bsky_url,
				meetup_url: $meetup_url,
			);
			
			$command->info($group->wasRecentlyCreated
				? "Created group <{$group->getKey()}>!"
				: "Updated group <{$group->getKey()}>!");
			
			return 0;
		}
		
		return 1;
	}
}
