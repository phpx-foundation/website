<?php

namespace App\Models;

use App\Enums\DomainStatus;
use App\Enums\GroupStatus;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Stringable;
use Revolution\Bluesky\Contracts\Factory;
use Revolution\Bluesky\Facades\Bluesky;
use Spatie\MailcoachSdk\Mailcoach;

class Group extends Model
{
	use SoftDeletes;
	use HasFactory;
	use HasSnowflakes;
	use HasDomain;
	use HasGroupMembership;

	protected $visible = [
		'id',
		'domain',
		'domain_status',
		'name',
		'region',
		'continent',
		'description',
		'timezone',
		'frequency',
		'status',
		'bsky_url',
		'bsky_did',
		'twitter_url',
		'meetup_url',
		'latitude',
		'longitude',
		'created_at',
	];

	protected $appends = [
		'label',
	];

	protected $mostRecentMeetup = null;

	protected static function booted()
	{
		static::saved(function(Group $group) {
			Cache::forget('phpx-network');
			Cache::forget("group:{$group->domain}");
		});
	}

	public function latestMeetup(): Attribute
	{
		return Attribute::make(function() {
			// Persisting to the instance in memory to reduce db lookups
			if ($this->mostRecentMeetup) {
				return $this->mostRecentMeetup;
			}
			$this->mostRecentMeetup = $this->meetups()->first();
			return $this->mostRecentMeetup;
		});
	}

	public function defaultLocation(): Attribute
	{
		return Attribute::make(fn() => $this->latestMeetup?->location);
	}

	public function defaultStart(): Attribute
	{
		return Attribute::make(fn() => $this->latestMeetup?->starts_at?->toTimeString());
	}

	public function defaultEnd(): Attribute
	{
		return Attribute::make(fn() => $this->latestMeetup?->ends_at?->toTimeString());
	}

	public function defaultStartDate(): Attribute
	{
		return Attribute::make(function() {
			if (empty($this->default_start)) {
				return null;
			}
			// Otherwise, today with the start time
			return (new Carbon())->setTimezone($this->timezone ?? config('app.timezone'))->setTimeFromTimeString($this->default_start);
		});
	}

	public function defaultEndDate(): Attribute
	{
		return Attribute::make(function() {
			if (empty($this->default_end)) {
				return null;
			}
			// Otherwise, today with the start time
			return (new Carbon())->setTimezone($this->timezone ?? config('app.timezone'))->setTimeFromTimeString($this->default_end);
		});
	}

	public function isActive(): bool
	{
		return GroupStatus::Active === $this->status;
	}

	public function isPlanned(): bool
	{
		return GroupStatus::Planned === $this->status;
	}

	public function isProspective(): bool
	{
		return GroupStatus::Prospective === $this->status;
	}

	public function isDisbanded(): bool
	{
		return GroupStatus::Disbanded === $this->status;
	}

	public function mailcoach(): ?Mailcoach
	{
		if (! isset($this->mailcoach_token, $this->mailcoach_list, $this->mailcoach_endpoint)) {
			return null;
		}

		return new Mailcoach($this->mailcoach_token, $this->mailcoach_endpoint);
	}

	public function bsky(): Factory|Bluesky|null
	{
		if (! isset($this->bsky_did, $this->bsky_app_password)) {
			return null;
		}

		return Bluesky::login($this->bsky_did, $this->bsky_app_password);
	}

	public function url(string $path, array $parameters = [], bool $secure = true): string
	{
		$generator = app(UrlGenerator::class);

		try {
			$generator->forceRootUrl('https://'.$this->domain);
			return $generator->to($path, $parameters, $secure);
		} finally {
			$generator->forceRootUrl(null);
		}
	}

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'group_memberships')
			->as('group_membership')
			->withPivot('id', 'role', 'is_subscribed')
			->withTimestamps()
			->using(GroupMembership::class);
	}

	public function meetups(): HasMany
	{
		return $this->hasMany(Meetup::class)->orderBy('starts_at', 'desc');
	}

	public function mailcoach_transactional_emails(): HasMany
	{
		return $this->hasMany(MailcoachTransactionalEmail::class);
	}

	protected function casts(): array
	{
		return [
			'mailcoach_token' => 'encrypted',
			'bsky_app_password' => 'encrypted',
			'turnstile_secret_key' => 'encrypted',
			'status' => GroupStatus::class,
			'domain_status' => DomainStatus::class,
			'latitude' => 'float',
			'longitude' => 'float',
		];
	}

	protected function label(): Attribute
	{
		return Attribute::get(fn() => $this->region ?? str($this->name)->afterLast('×')->trim()->toString());
	}

	protected function airportCode(): Attribute
	{
		return Attribute::get(
			fn(): Stringable => str($this->name)->afterLast('×')->trim()->upper(),
		);
	}

	protected function openGraphImageUrl(): Attribute
	{
		return Attribute::get(function() {
			$filename = $this->airport_code->lower()->finish('.png');
			$path = public_path("og/{$filename}");

			if (file_exists($path)) {
				return asset("og/{$filename}").'?t='.filemtime($path);
			}

			return null;
		});
	}

	protected function meetupUrlArray(): Attribute
	{
		return Attribute::get(fn() => str($this->meetup_url)
			->explode(',')
			->map(fn($url) => trim($url))
			->filter()
			->values()
			->toArray());
	}
}
