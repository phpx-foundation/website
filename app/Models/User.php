<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	use HasFactory;
	use Notifiable;
	use HasSnowflakes;
	use SoftDeletes;
	
	protected $hidden = [
		'password',
		'remember_token',
	];
	
	public function current_group(): BelongsTo
	{
		return $this->belongsTo(Group::class, 'current_group_id');
	}
	
	public function groups(): BelongsToMany
	{
		return $this->belongsToMany(Group::class, 'group_memberships')
			->as('group_membership')
			->withPivot('id', 'is_subscribed')
			->withTimestamps()
			->using(GroupMembership::class);
	}
	
	public function meetups(): BelongsToMany
	{
		return $this->belongsToMany(Meetup::class, 'rsvps')
			->as('meetups')
			->withTimestamps()
			->using(Rsvp::class);
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
