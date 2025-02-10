<?php

namespace Tests\Feature;

use App\Enums\GroupRole;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_members_of_groups(): void
    {
        $user = User::factory()->create();
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        $group3 = Group::factory()->create();

        $user->joinGroup($group1, role: GroupRole::Admin);
        $user->joinGroup($group3, is_subscribed: true);

        $this->assertCount(2, $user->groups);

        $group1_via_relation = $user->groups->where('id', $group1->id)->first();
        $this->assertEquals(GroupRole::Admin, $group1_via_relation->group_membership->role);
        $this->assertFalse($group1_via_relation->group_membership->is_subscribed);

        $group3_via_relation = $user->groups->where('id', $group3->id)->first();
        $this->assertEquals(GroupRole::Attendee, $group3_via_relation->group_membership->role);
        $this->assertTrue($group3_via_relation->group_membership->is_subscribed);

        $this->assertTrue($user->isGroupAdmin($group1));
        $this->assertFalse($user->isGroupAdmin($group2));
        $this->assertFalse($user->isGroupAdmin($group3));
    }
}
