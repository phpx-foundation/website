<?php

namespace App\Actions;

use App\Models\ExternalGroup;
use App\Models\Group;
use BackedEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Lorisleiva\Actions\Concerns\AsAction;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class SyncGroups
{
	use AsAction;
	
	protected ?Collection $groups = null;
	
	public function handle(): Collection
	{
		$groups = $this->groups()
			->each(fn(Group|ExternalGroup $g) => $g->save());
		
		if (App::isProduction()) {
			SyncDomainsWithForge::run();
		}
		
		return $groups;
	}
	
	public function getCommandSignature(): string
	{
		return 'group:sync {--force}';
	}
	
	public function asCommand(Command $command): int
	{
		$groups = $this->groups();
		
		if ($groups->isEmpty()) {
			info('No changes necessary.');
			
			return 0;
		}
		
		foreach ($groups as $group) {
			$action = $group->exists ? 'Update' : 'Create';
			$type = $group instanceof ExternalGroup ? 'external' : 'PHP×';
			info("{$action} {$type} group: {$group->domain}");
			table(
				headers: ['Attribute', 'Before', 'After'],
				rows: collect($group->getDirty())
					->map(fn($value, $attribute) => [
						$attribute,
						$this->valueForTable($group->getOriginal($attribute)),
						$this->valueForTable($value),
					]),
			);
		}
		
		if ($command->option('force') || confirm('Save these changes?')) {
			$this->handle();
			info('Changes saved.');
			return 0;
		}
		
		return 1;
	}
	
	protected function syncConfigWithGroup(string $domain, array $config): Group
	{
		$group = Group::where('domain', $domain)->firstOrNew();
		
		$group->domain ??= $domain;
		
		$group->forceFill(Arr::only($config, [
			'name',
			'region',
			'continent',
			'description',
			'timezone',
			'bsky_url',
			'meetup_url',
			'status',
			'frequency',
			'latitude',
			'longitude',
		]));
		
		$this->syncSponsorship($group, $config);
		
		return $group;
	}
	
	protected function syncConfigWithExternalGroup(string $domain, array $config): ExternalGroup
	{
		$external_group = ExternalGroup::where('domain', $domain)->firstOrNew();
		
		$external_group->domain ??= $domain;
		
		$external_group->forceFill(Arr::only($config, [
			'name',
			'region',
			'continent',
			'latitude',
			'longitude',
		]));
		
		return $external_group;
	}
	
	protected function syncSponsorship(Group $group, array $config): void
	{
		if (isset($config['sponsorships'])) {
			$sponsorship = $config['sponsorships'];
			
			$group->sponsorships_enabled = (bool) ($sponsorship['enabled'] ?? false);
			$group->sponsorship_packages = $sponsorship['packages'] ?? [];
			$group->sponsorship_contact_email = $sponsorship['contact_email'] ?? null;
			$group->sponsorship_description = $sponsorship['description'] ?? null;
		} else {
			$group->sponsorships_enabled = false;
			$group->sponsorship_packages = [];
			$group->sponsorship_contact_email = null;
			$group->sponsorship_description = null;
		}
	}
	
	/** @return Collection<string, Group|ExternalGroup> */
	protected function groups(): Collection
	{
		return $this->groups ??= collect(json_decode(file_get_contents(base_path('groups.json')), true))
			->map(fn($config, $domain) => data_get($config, 'external', false)
				? $this->syncConfigWithExternalGroup($domain, $config)
				: $this->syncConfigWithGroup($domain, $config))
			->filter(fn(Group|ExternalGroup $g) => $g->isDirty());
	}
	
	protected function valueForTable($attribute): string
	{
		return match (true) {
			$attribute instanceof BackedEnum => $attribute->value,
			is_array($attribute) => implode(', ', $attribute),
			default => (string) $attribute,
		};
	}
}
