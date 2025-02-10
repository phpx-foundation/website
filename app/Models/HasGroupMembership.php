<?php

namespace App\Models;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasGroupMembership
{
    protected function groupMembership(): Attribute
    {
        return Attribute::get(function () {
            $membership = $this->getRelation('group_membership');

            if ($membership instanceof GroupMembership) {
                return $membership;
            }

            throw new BadMethodCallException('The group_membership relation must be loaded as a pivot.');
        });
    }
}
