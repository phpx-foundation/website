<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
	use HandlesAuthorization;
	
	public function viewAny(User $user): bool
	{
		return $user->isAnyGroupAdmin();
	}
	
	public function view(User $user, User $target): bool
	{
		return $user->is($target) 
			|| $user->isSuperAdmin()
			|| $user->isOrganizerOfAnyGroupUserBelongsTo($target);
	}
	
	public function create(User $user): bool
	{
		return $user->isSuperAdmin();
	}
	
	public function update(User $user, User $target): bool
	{
		return $user->is($target) || $user->isSuperAdmin();
	}
	
	public function delete(User $user, User $target): bool
	{
		return $user->isSuperAdmin();
	}
	
	public function restore(User $user, User $target): bool
	{
		return $user->isSuperAdmin();
	}
	
	public function forceDelete(User $user, User $target): bool
	{
		return $user->isSuperAdmin();
	}
}
