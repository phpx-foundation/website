<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Meetup extends Model implements Htmlable
{
	use HasSnowflakes;
	use HasFactory;
	
	protected $casts = [
		'starts_at' => 'datetime',
		'ends_at' => 'datetime',
	];
	
	protected $visible = [
		'id',
		'group',
		'description',
		'location',
		'capacity',
		'rsvp_url',
		'date_day',
		'date_range',
		'starts_at',
		'ends_at',
		'external_url',
	];
	
	protected $appends = [
		'rsvp_url',
		'date_day',
		'date_range',
	];
	
	protected static function booted()
	{
		static::saved(fn(Meetup $meetup) => Cache::forget("group:{$meetup->group_id}:next-meetup"));
	}
	
	public function scopeFuture(Builder $query, ?CarbonInterface $at = null): Builder
	{
		return $query->where('ends_at', '>', $at ?? now());
	}
	
	public function group(): BelongsTo
	{
		return $this->belongsTo(Group::class);
	}
	
	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'rsvps')
			->as('rsvp')
			->withTimestamps()
			->using(Rsvp::class);
	}
	
	public function toHtml(): string
	{
		return Str::markdown($this->description);
	}
	
	public function remaining(): int
	{
		$rsvps = $this->users_count ?? $this->loadCount('users')->users_count;
		
		return max(0, $this->capacity - $rsvps);
	}
	
	public function range(): string
	{
		$starts_at = $this->starts_at->timezone($this->group->timezone);
		$ends_at = $this->ends_at->timezone($this->group->timezone);
		
		if ($starts_at->eq($ends_at)) {
			return $starts_at->format("l, F jS Y \a\\t g:ia T");
		}
		
		if ($starts_at->isSameDay($ends_at)) {
			$start = $starts_at->format("l, F jS Y \\f\\r\o\m g:ia");
			$end = $ends_at->format('g:ia T');
			return "$start to $end";
		}
		
		$start = $starts_at->format('F jS');
		$end = $ends_at->format("F jS Y");
		return "{$start}–{$end}";
	}
	
	protected function openGraphImageFile(): Attribute
	{
		return Attribute::get(function() {
			$filename = "og/meetups/{$this->getKey()}.png";
			$path = storage_path("app/public/{$filename}");
			
			return file_exists($path) ? $path : null;
		});
	}
	
	protected function openGraphImageUrl(): Attribute
	{
		return Attribute::get(function() {
			$filename = "og/meetups/{$this->getKey()}.png";
			$path = storage_path("app/public/{$filename}");
			
			if (file_exists($path)) {
				return asset("storage/{$filename}").'?t='.filemtime($path);
			}
			
			return null;
		});
	}
	
	protected function rsvpUrl(): Attribute
	{
		return Attribute::get(fn() => $this->group->url("meetups/{$this->getKey()}/rsvps"));
	}
	
	protected function dateDay(): Attribute
	{
		return Attribute::get(fn() => $this->starts_at->timezone(config('app.timezone'))->format('F jS'));
	}
	
	protected function dateRange(): Attribute
	{
		return Attribute::get(fn() => $this->range());
	}
}
