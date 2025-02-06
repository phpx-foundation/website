<?php

namespace App\Models;

use App\Enums\GroupRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BelongsToGroups
{
	public function joinGroup(
		Group $group,
		bool $current = false,
		bool $is_subscribed = false,
		GroupRole $role = GroupRole::Attendee
	): static {
		$this->groups()->syncWithoutDetaching([
			$group->getKey() => [
				'is_subscribed' => $is_subscribed,
				'role' => $role,
			],
		]);
		
		$this->unsetRelation('groups');
		
		if ($current || null === $this->current_group_id) {
			$this->update(['current_group_id' => $group->getKey()]);
			$this->setRelation('current_group', $group);
		}
		
		return $this;
	}
	
	public function isGroupAdmin(Group $group): bool
	{
		return $this->groups
			->first(fn(Group $candidate) => $candidate->is($group))
			?->group_membership
			->isAdmin() ?? false;
	}
	
	public function current_group(): BelongsTo
	{
		return $this->belongsTo(Group::class, 'current_group_id');
	}
	
	public function groups(): BelongsToMany
	{
		return $this->belongsToMany(Group::class, 'group_memberships')
			->as('group_membership')
			->withPivot('id', 'is_subscribed', 'role')
			->withTimestamps()
			->using(GroupMembership::class);
	}
}
