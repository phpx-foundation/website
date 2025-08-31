<?php

namespace App\Actions;

use App\Models\ExternalGroup;
use App\Models\Group;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ListGroups
{
	use AsAction;
	
	public static function routes(Router $router): void
	{
		$router->get('api/groups', static::class);
	}
	
	/** @return Collection<int, \App\Models\Group|\App\Models\ExternalGroup> */
	public function handle(bool $include_external = true): Collection
	{
		$groups = Group::all();
		
		if ($include_external) {
			$groups = $groups->merge(ExternalGroup::all())->values();
		}
		
		return $groups;
	}
	
	public function asController(ActionRequest $request)
	{
		$groups = $this->handle(include_external: ! $request->boolean('no_external'))
			->mapWithKeys(function(Group|ExternalGroup $group) {
				$data = [
					'domain' => $group->domain,
					'name' => $group->name,
					'description' => $group->description,
					'continent' => $group->continent,
					'region' => $group->region,
					'timezone' => $group->timezone,
					'status' => $group->status,
					'frequency' => $group->frequency,
					'links' => [
						'bluesky' => $group->bsky_url,
						'meetup' => $group->meetup_url,
					],
					'coords' => ['latitude' => $group->latitude, 'longitude' => $group->longitude],
					'external' => $group instanceof ExternalGroup,
				];
				
				// Add sponsorship data for non-external groups
				if (! ($group instanceof ExternalGroup) && $group->hasSponsorship()) {
					$data['sponsorship'] = [
						'enabled' => $group->sponsorships_enabled,
						'packages' => $group->sponsorship_packages,
						'contact_email' => $group->sponsorship_contact_email,
						'description' => $group->sponsorship_description,
					];
				}
				
				return [$group->domain => $data];
			});
		
		return response()->json(['groups' => $groups]);
	}
}
