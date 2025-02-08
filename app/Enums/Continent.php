<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Continent: string implements HasLabel
{
	case Africa = 'Africa';
	
	case Asia = 'Asia';
	
	case Europe = 'Europe';
	
	case NorthAmerica = 'North America';
	
	case SouthAmerica = 'South America';
	
	case Antarctica = 'Antarctica';
	
	case Australia = 'Australia';
	
	public function getLabel(): ?string
	{
		return $this->value;
	}
}
