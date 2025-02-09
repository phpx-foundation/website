<?php

namespace App\Models\Scopes;

use App\Enums\GroupRole;
use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OrganizedByAuthenticatedUser implements Scope
{
	public function apply(Builder $builder, Model $model)
	{
		if (Auth::user()->isSuperAdmin()) {
			return;
		}
		
		match($model::class) {
			Group::class => $builder->whereHas('users', $this->subquery(...)),
			Meetup::class => $builder->whereHas('group.users', $this->subquery(...)),
		};
	}
	
	protected function subquery(Builder $query): void
	{
		$query->where('users.id', Auth::id());
		$query->where('group_memberships.role', GroupRole::Admin);
	}
}
