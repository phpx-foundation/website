<?php

namespace App\Enums;

enum DomainStatus: string
{
	case Confirmed = 'confirmed';
	
	case Pending = 'pending';
}
