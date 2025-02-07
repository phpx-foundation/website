<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\Meetup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetupPolicy
{
	use HandlesAuthorization;
	
	public function viewAny(User $user): bool
	{
		return true;
	}
	
	public function view(User $user, Meetup $meetup): bool
	{
		return true;
	}
	
	public function create(User $user): bool
	{
		return $user->isSuperAdmin() 
			|| $user->groups->contains(fn(Group $group) => $group->group_membership->isAdmin());
	}
	
	public function update(User $user, Meetup $meetup): bool
	{
		return $user->isSuperAdmin() || $user->isGroupAdmin($meetup->group);
	}
	
	public function delete(User $user, Meetup $meetup): bool
	{
		return $this->update($user, $meetup);
	}
	
	public function restore(User $user, Meetup $meetup): bool
	{
		return $this->update($user, $meetup);
	}
	
	public function forceDelete(User $user, Meetup $meetup): bool
	{
		return false;
	}
}
