<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements FilamentUser
{
	use HasFactory;
	use Notifiable;
	use HasSnowflakes;
	use SoftDeletes;
	use BelongsToGroups;
	use HasGroupMembership;
	
	protected $hidden = [
		'password',
		'remember_token',
	];
	
	protected static function booted()
	{
		static::deleted(function(User $user) {
			GroupMembership::query()->where('user_id', $user->getKey())->delete();
			Rsvp::query()->where('user_id', $user->getKey())->delete();
		});
	}
	
	public function canAccessPanel(Panel $panel): bool
	{
		return $this->isSuperAdmin()
			|| ($this->hasVerifiedEmail() && $this->isAnyGroupAdmin());
	}
	
	public function isSuperAdmin(): bool
	{
		return $this->hasVerifiedEmail() && in_array($this->email, config('auth.super_admins'));
	}
	
	public function isOrganizerOfAnyGroupUserBelongsTo(User $user): bool
	{
		foreach ($user->groups as $group) {
			if ($this->isGroupAdmin($group)) {
				return true;
			}
		}
		
		return false;
	}
	
	public function meetups(): BelongsToMany
	{
		return $this->belongsToMany(Meetup::class, 'rsvps')
			->as('meetups')
			->withTimestamps()
			->using(Rsvp::class);
	}
	
	protected function scopeWhereMemberOfGroup(Builder $query, Group|iterable|int ...$group): Builder
	{
		// Allow for passing a single array of groups or group IDs rather than unpacking
		if (1 === count($group) && is_array($group)) {
			$group = $group[0];
		}
		
		// Now ensure that we just have IDs
		$group_ids = collect($group)->map(fn(Group|int $group) => $group instanceof Group ? $group->getKey() : $group);
		
		return $query->whereHas('groups', fn(Builder $query) => $query->whereIn('groups.id', $group_ids));
	}
	
	protected function scopeWhereVisibleToUser(Builder $query, ?User $user = null): Builder
	{
		$user ??= Auth::user();
		
		return $this->scopeWhereMemberOfGroup($query, $user->organized_group_ids);
	}
	
	protected function organizedGroups(): Attribute
	{
		return Attribute::get(fn() => $this->groups->filter(fn(Group $group) => $group->group_membership->isAdmin()));
	}
	
	protected function organizedGroupIds(): Attribute
	{
		return Attribute::get(fn() => Cache::remember(
			key: "user:{$this->getKey()}:organized_group_ids",
			ttl: now()->addDay(),
			callback: fn() => $this->organized_groups->pluck('id'),
		));
	}
	
	protected function casts(): array
	{
		return [
			'is_potential_speaker' => 'boolean',
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}
}
