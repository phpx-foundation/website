<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;

enum GroupStatus: string implements HasDescription
{
    case Active = 'active';

    case Planned = 'planned';

    case Prospective = 'prospective';

    case Disbanded = 'disbanded';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::Active => 'Group has had meetings recently',
            self::Planned => 'Group plans to hold a meeting soon',
            self::Prospective => 'Group would like to organize, but may need co-organizers',
            self::Disbanded => 'Group is no longer active',
        };
    }
}
