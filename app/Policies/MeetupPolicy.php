<?php

namespace App\Policies;

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
		return false;
	}
	
	public function update(User $user, Meetup $meetup): bool
	{
		return false;
	}
	
	public function delete(User $user, Meetup $meetup): bool
	{
		return false;
	}
	
	public function restore(User $user, Meetup $meetup): bool
	{
		return false;
	}
	
	public function forceDelete(User $user, Meetup $meetup): bool
	{
		return false;
	}
}
