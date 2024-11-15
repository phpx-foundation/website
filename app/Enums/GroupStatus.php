<?php

namespace App\Enums;

enum GroupStatus: string
{
	case Active = 'active';
	
	case Planned = 'planned';
	
	case Prospective = 'prospective';
	
	case Disbanded = 'disbanded';
}
