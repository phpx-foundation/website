<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
	use HandlesAuthorization;
	
	public function viewAny(User $user): bool
	{
		return true;
	}
	
	public function view(User $user, Group $group): bool
	{
		return true;
	}
	
	public function create(User $user): bool
	{
		return false;
	}
	
	public function update(User $user, Group $group): bool
	{
		return $user->isGroupAdmin($group);
	}
	
	public function delete(User $user, Group $group): bool
	{
		return false;
	}
	
	public function restore(User $user, Group $group): bool
	{
		return false;
	}
	
	public function forceDelete(User $user, Group $group): bool
	{
		return false;
	}
}
