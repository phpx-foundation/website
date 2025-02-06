<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;

enum GroupRole: string implements HasDescription
{
	case Admin = 'admin';
	
	case Attendee = 'attendee';
	
	public function getDescription(): ?string
	{
		return $this->name;
	}
}
