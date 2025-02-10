<?php

namespace App\Rules;

use App\Models\Group;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class CanUpdateGroup implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $group = Group::find($value)) {
            $fail('This is not a valid group.');
        }

        if (! Auth::user()->can('update', $group)) {
            $fail('This is not a group you are an organizer of.');
        }
    }
}
