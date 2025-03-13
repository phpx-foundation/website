<?php

namespace App\Models;

use Carbon\CarbonImmutable;
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
		'group_id',
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
		if (! $this->starts_at) {
			return '';
		}
		
		if ($this->starts_at->eq($this->ends_at)) {
			return $this->starts_at->format("l, F jS Y \a\\t g:ia T");
		}
		
		if ($this->starts_at->isSameDay($this->ends_at)) {
			$start = $this->starts_at->format("l, F jS Y \\f\\r\o\m g:ia");
			$end = $this->ends_at->format('g:ia T');
			
			return "{$start} to {$end}";
		}
		
		$start = $this->starts_at->format('F jS');
		$end = $this->ends_at->format('F jS Y');
		
		return "{$start}–{$end}";
	}
	
	public function externalRsvpPlatformName(): ?string
	{
		return match (true) {
			null === $this->external_rsvp_url => null,
			Str::contains($this->external_rsvp_url, 'meetup.com') => 'Meetup',
			Str::contains($this->external_rsvp_url, 'eventy.io') => 'Eventy',
			Str::contains($this->external_rsvp_url, 'guild.host') => 'Guild',
			Str::contains($this->external_rsvp_url, 'lu.ma') => 'luma',
			default => null,
		};
	}
	
	protected function startsAt(): Attribute
	{
		return Attribute::make(
			get: fn($value) => $value ? CarbonImmutable::make($value)->timezone($this->group->timezone) : null,
			set: fn($value) => $this->asDateTime($value)->timezone(config('app.timezone')),
		);
	}
	
	protected function endsAt(): Attribute
	{
		return Attribute::make(
			get: fn($value) => $value ? CarbonImmutable::make($value)->timezone($this->group->timezone) : null,
			set: fn($value) => $this->asDateTime($value)->timezone(config('app.timezone')),
		);
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
		return Attribute::get(fn() => $this->group?->url("meetups/{$this->getKey()}/rsvps"));
	}
	
	protected function dateDay(): Attribute
	{
		return Attribute::get(fn() => $this->starts_at?->timezone(config('app.timezone'))->format('F jS'));
	}
	
	protected function dateRange(): Attribute
	{
		return Attribute::get(fn() => $this->range());
	}
}
