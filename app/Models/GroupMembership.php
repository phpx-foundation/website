<?php

namespace App\Models;

use App\Enums\GroupRole;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Cache;

class GroupMembership extends Pivot
{
	use HasSnowflakes;
	
	public $timestamps = true;
	
	protected $table = 'group_memberships';
	
	protected $casts = [
		'is_subscribed' => 'boolean',
		'role' => GroupRole::class,
	];
	
	protected static function booted()
	{
		$clearOrganizedGroupIdsCache = fn(GroupMembership $membership) => Cache::forget("user:{$membership->user_id}:organized_group_ids");
		
		static::saved($clearOrganizedGroupIdsCache);
		static::deleted($clearOrganizedGroupIdsCache);
	}
	
	public function isAdmin(): bool
	{
		return $this->role === GroupRole::Admin;
	}
}
