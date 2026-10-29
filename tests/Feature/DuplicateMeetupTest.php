<?php

namespace Tests\Feature;

use App\Actions\GenerateOpenGraphImage;
use App\Enums\GroupRole;
use App\Filament\Resources\MeetupResource;
use App\Filament\Resources\MeetupResource\Pages\CreateMeetup;
use App\Filament\Resources\MeetupResource\Pages\EditMeetup;
use App\Filament\Resources\MeetupResource\Pages\ListMeetups;
use App\Models\Group;
use App\Models\Meetup;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DuplicateMeetupTest extends TestCase
{
	use RefreshDatabase;

	protected Group $group;

	protected Meetup $meetup;

	protected function setUp(): void
	{
		parent::setUp();

		$this->group = Group::factory()->create();
		app()->instance(Group::class, $this->group);
		app()->instance("group:{$this->group->domain}", $this->group);

		$user = User::factory()->create();
		$this->group->users()->attach($user, ['role' => GroupRole::Admin]);
		$this->actingAs($user);

		$this->meetup = Meetup::factory()->for($this->group)->create([
			'location' => 'Original Location',
			'capacity' => 42,
			'description' => 'Original description',
			'external_rsvp_url' => 'https://example.com/rsvp',
			'starts_at' => now()->subMonth()->hour(18)->minute(30)->second(0),
			'ends_at' => now()->subMonth()->hour(20)->minute(0)->second(0),
		]);
	}

	public function test_next_month_dates_move_to_a_month_after_the_latest_meetup_and_keep_time_of_day(): void
	{
		$this->setMeetupLocalTimes($this->meetup, '2026-10-08 18:30', '2026-10-08 20:00');

		$dates = $this->meetup->nextMonthDates();

		$this->assertSame('2026-11-08 18:30', $this->localTime($dates['starts_at']), 'Local time-of-day is kept across the DST change');
		$this->assertSame('2026-11-08 20:00', $this->localTime($dates['ends_at']));
		$this->assertSame('2026-10-08 18:30', $this->localTime($this->meetup->fresh()->starts_at), 'Source meetup must not be mutated');
	}

	public function test_duplicating_an_older_meetup_uses_the_groups_latest_future_meetup_date(): void
	{
		$this->setMeetupLocalTimes($this->meetup, '2026-10-08 18:30', '2026-10-08 20:00');
		$this->setMeetupLocalTimes(Meetup::factory()->for($this->group)->create(), '2027-01-14 17:00', '2027-01-14 19:00');

		$dates = $this->meetup->nextMonthDates();

		$this->assertSame('2027-02-14 18:30', $this->localTime($dates['starts_at']));
		$this->assertSame('2027-02-14 20:00', $this->localTime($dates['ends_at']));
	}

	public function test_duplicate_ignores_meetups_from_other_groups(): void
	{
		$this->setMeetupLocalTimes($this->meetup, '2026-10-08 18:30', '2026-10-08 20:00');
		$this->setMeetupLocalTimes(Meetup::factory()->for(Group::factory())->create(), '2027-06-01 18:00', '2027-06-01 20:00');

		$dates = $this->meetup->nextMonthDates();

		$this->assertSame('2026-11-08 18:30', $this->localTime($dates['starts_at']));
	}

	public function test_duplicate_does_not_overflow_into_the_following_month(): void
	{
		$this->setMeetupLocalTimes($this->meetup, '2027-01-31 18:30', '2027-01-31 20:00');

		$dates = $this->meetup->nextMonthDates();

		$this->assertSame('2027-02-28 18:30', $this->localTime($dates['starts_at']));
	}

	public function test_duplicate_preserves_duration_when_the_meetup_ends_on_a_later_day(): void
	{
		$this->setMeetupLocalTimes($this->meetup, '2027-05-10 22:00', '2027-05-11 01:00');

		$dates = $this->meetup->nextMonthDates();

		$this->assertSame('2027-06-10 22:00', $this->localTime($dates['starts_at']));
		$this->assertSame('2027-06-11 01:00', $this->localTime($dates['ends_at']));
	}

	public function test_create_page_is_prefilled_from_the_duplicated_meetup(): void
	{
		GenerateOpenGraphImage::mock()->shouldReceive('handle')->once()->andReturn('og.png');

		$this->setMeetupLocalTimes($this->meetup, '2026-10-08 18:30', '2026-10-08 20:00');
		$this->meetup->users()->attach(User::factory()->create());
		Storage::fake('public')->put('og/custom/original.png', 'image');
		$this->meetup->update(['custom_open_graph_image' => 'og/custom/original.png']);

		$component = Livewire::withQueryParams(['duplicate' => (string) $this->meetup->getKey()])
			->test(CreateMeetup::class)
			->assertFormSet([
				'group_id' => $this->group->getKey(),
				'location' => 'Original Location',
				'capacity' => 42,
				'description' => 'Original description',
				'external_rsvp_url' => 'https://example.com/rsvp',
			]);

		$this->assertSame('2026-11-08 18:30:00', $component->get('data.starts_at'));
		$this->assertSame('2026-11-08 20:00:00', $component->get('data.ends_at'));

		$component->call('create')->assertHasNoFormErrors();

		$duplicate = Meetup::query()->whereKeyNot($this->meetup->getKey())->sole();

		$this->assertSame($this->group->getKey(), $duplicate->group_id);
		$this->assertSame('Original Location', $duplicate->location);
		$this->assertSame(42, $duplicate->capacity);
		$this->assertSame('Original description', $duplicate->description);
		$this->assertSame('https://example.com/rsvp', $duplicate->external_rsvp_url);
		$this->assertSame('2026-11-08 18:30', $this->localTime($duplicate->starts_at));
		$this->assertSame('2026-11-08 20:00', $this->localTime($duplicate->ends_at));
		$this->assertSame(0, $duplicate->users()->count(), 'RSVPs must not be copied');
		$this->assertNull($duplicate->custom_open_graph_image, 'The custom Open Graph image must not be shared with the duplicate');
		$this->assertSame(1, $this->meetup->users()->count());
		$this->assertSame('og/custom/original.png', $this->meetup->fresh()->custom_open_graph_image);
	}

	public function test_create_page_without_duplicate_is_prefilled_with_defaults_from_the_latest_meetup(): void
	{
		$this->setMeetupLocalTimes($this->meetup, '2026-10-08 18:30', '2026-10-08 20:00');

		$state = Livewire::test(CreateMeetup::class)
			->assertFormSet([
				'group_id' => $this->group->getKey(),
				'location' => 'Original Location',
				'capacity' => 42,
				'description' => null,
			])
			->get('data');

		$this->assertSame('2026-11-08 18:30:00', $state['starts_at']);
		$this->assertSame('2026-11-08 20:00:00', $state['ends_at']);
	}

	public function test_create_page_defaults_come_from_the_meetup_with_the_latest_start_not_the_latest_created(): void
	{
		$this->setMeetupLocalTimes($this->meetup, '2026-11-18 18:30', '2026-11-18 20:00');

		$this->travel(1)->minute();

		$recently_created_past_meetup = Meetup::factory()->for($this->group)->create([
			'location' => 'Older Location',
			'capacity' => 10,
		]);
		$this->setMeetupLocalTimes($recently_created_past_meetup, '2026-11-04 17:00', '2026-11-04 19:00');

		$state = Livewire::test(CreateMeetup::class)
			->assertFormSet([
				'location' => 'Original Location',
				'capacity' => 42,
			])
			->get('data');

		$this->assertSame('2026-12-18 18:30:00', $state['starts_at']);
		$this->assertSame('2026-12-18 20:00:00', $state['ends_at']);
	}

	public function test_create_page_ignores_unknown_duplicate_ids(): void
	{
		Livewire::withQueryParams(['duplicate' => '123'])
			->test(CreateMeetup::class)
			->assertFormSet(['description' => null])
			->assertSuccessful();
	}

	public function test_create_page_ignores_array_duplicate_params(): void
	{
		Livewire::withQueryParams(['duplicate' => [(string) $this->meetup->getKey()]])
			->test(CreateMeetup::class)
			->assertFormSet(['description' => null])
			->assertSuccessful();
	}

	public function test_create_page_ignores_meetups_the_user_cannot_update(): void
	{
		$other_meetup = Meetup::factory()->for(Group::factory())->create([
			'description' => 'Another group\'s description',
		]);

		Livewire::withQueryParams(['duplicate' => (string) $other_meetup->getKey()])
			->test(CreateMeetup::class)
			->assertFormSet(['description' => null])
			->assertSuccessful();
	}

	public function test_duplicate_action_is_available_on_the_table_and_edit_page(): void
	{
		$url = MeetupResource::getUrl('create', ['duplicate' => $this->meetup->getKey()]);

		Livewire::test(ListMeetups::class)
			->assertTableActionExists('duplicate')
			->assertTableActionHasUrl('duplicate', $url, $this->meetup);

		Livewire::test(EditMeetup::class, ['record' => $this->meetup->getKey()])
			->assertActionExists('duplicate')
			->assertActionHasUrl('duplicate', $url);
	}

	public function test_duplicate_action_is_visible_to_super_admins_without_group_memberships(): void
	{
		$super_admin = User::factory()->create();
		config(['auth.super_admins' => [$super_admin->email]]);
		$this->actingAs($super_admin);

		$this->assertTrue($super_admin->isSuperAdmin());
		$this->assertCount(0, $super_admin->groups);

		Livewire::test(ListMeetups::class)
			->assertTableActionVisible('duplicate', $this->meetup);
	}

	/**
	 * Guards that the action's visibility follows MeetupResource::canCreate(). An attendee-only
	 * user can't reach the panel in practice (User::canAccessPanel() requires a group admin), but
	 * Livewire::test() skips panel middleware, which lets us exercise the authorization wiring.
	 */
	public function test_duplicate_action_is_hidden_from_users_who_cannot_create_meetups(): void
	{
		$member = User::factory()->create();
		$this->group->users()->attach($member, ['role' => GroupRole::Attendee]);
		$this->actingAs($member);

		$this->assertFalse(MeetupResource::canCreate());

		Livewire::test(ListMeetups::class)
			->assertTableActionHidden('duplicate', $this->meetup);
	}

	/**
	 * In the panel, OrganizedByAuthenticatedUser already hides other groups' meetups from the table.
	 * Livewire::test() skips that middleware, which lets us check the per-record authorization itself.
	 */
	public function test_duplicate_action_is_hidden_on_meetups_the_user_cannot_replicate(): void
	{
		$other_admin = User::factory()->create();
		Group::factory()->create()->users()->attach($other_admin, ['role' => GroupRole::Admin]);
		$this->actingAs($other_admin);

		$this->assertTrue(MeetupResource::canCreate());
		$this->assertFalse(MeetupResource::canReplicate($this->meetup));

		Livewire::test(ListMeetups::class)
			->assertTableActionHidden('duplicate', $this->meetup);
	}

	/**
	 * Under the current policy anyone who can edit a meetup can also create one, so
	 * the create ability is denied directly to exercise the edit page's wiring.
	 */
	public function test_duplicate_action_is_hidden_on_the_edit_page_when_the_user_cannot_create_meetups(): void
	{
		Gate::before(fn(User $user, string $ability) => $ability === 'create' ? false : null);

		$this->assertFalse(MeetupResource::canCreate());

		Livewire::test(EditMeetup::class, ['record' => $this->meetup->getKey()])
			->assertActionHidden('duplicate');
	}

	public function test_create_another_after_duplicating_falls_back_to_the_default_form(): void
	{
		GenerateOpenGraphImage::mock()->shouldReceive('handle')->once()->andReturn('og.png');

		$this->setMeetupLocalTimes($this->meetup, '2026-10-08 18:30', '2026-10-08 20:00');

		$component = Livewire::withQueryParams(['duplicate' => (string) $this->meetup->getKey()])
			->test(CreateMeetup::class)
			->assertFormSet(['description' => 'Original description'])
			->call('create', another: true)
			->assertHasNoFormErrors()
			->assertFormSet([
				'group_id' => $this->group->getKey(),
				'location' => 'Original Location',
				'capacity' => 42,
				'description' => null,
				'external_rsvp_url' => null,
			]);

		$this->assertSame(2, Meetup::query()->count());
		$this->assertSame('2026-12-08 18:30:00', $component->get('data.starts_at'), 'Defaults now come from the duplicate just created');
		$this->assertSame('2026-12-08 20:00:00', $component->get('data.ends_at'));
	}

	protected function setMeetupLocalTimes(Meetup $meetup, string $starts_at, string $ends_at): void
	{
		$meetup->update([
			'starts_at' => Carbon::parse($starts_at, $meetup->group->timezone)->utc(),
			'ends_at' => Carbon::parse($ends_at, $meetup->group->timezone)->utc(),
		]);
	}

	protected function localTime(CarbonInterface $date): string
	{
		return $date->avoidMutation()->timezone($this->group->timezone)->format('Y-m-d H:i');
	}
}
