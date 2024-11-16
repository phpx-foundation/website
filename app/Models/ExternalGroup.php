<?php

namespace App\Models;

use App\Enums\GroupStatus;
use Exception;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalGroup extends Model
{
	use HasSnowflakes;
	use SoftDeletes;
	
	protected static function booted()
	{
		static::creating(function(self $external) {
			if (Group::whereDomain($external->domain)->exists()) {
				throw new Exception('Cannot create an external group with a domain for an existing group.');
			}
		});
	}
	
	protected function casts(): array
	{
		return [
			'latitude' => 'float',
			'longitude' => 'float',
		];
	}
	
	public function label(): string
	{
		return $this->region ?? str($this->name)->afterLast('×')->trim()->toString();
	}
}
