<?php

namespace App\Models;

use App\Enums\GroupRole;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupMembership extends Pivot
{
	use HasSnowflakes;
	
	public $timestamps = true;
	
	protected $table = 'group_memberships';
	
	protected $casts = [
		'is_subscribed' => 'boolean',
		'role' => GroupRole::class,
	];
	
	public function isAdmin(): bool
	{
		return $this->role === GroupRole::Admin;
	}
}
