<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use BelongsToGroups;
    use HasFactory;
    use HasGroupMembership;
    use HasSnowflakes;
    use Notifiable;
    use SoftDeletes;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isSuperAdmin()
            || ($this->hasVerifiedEmail() && $this->isAnyGroupAdmin());
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasVerifiedEmail() && in_array($this->email, config('auth.super_admins'));
    }

    public function meetups(): BelongsToMany
    {
        return $this->belongsToMany(Meetup::class, 'rsvps')
            ->as('meetups')
            ->withTimestamps()
            ->using(Rsvp::class);
    }

    protected function casts(): array
    {
        return [
            'is_potential_speaker' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
