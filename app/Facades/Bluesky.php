<?php

namespace App\Facades;

use App\Services\Bluesky as ServicesBluesky;
use Illuminate\Support\Facades\Facade;

class Bluesky extends Facade
{
	protected static function getFacadeAccessor()
	{
		return ServicesBluesky::class;
	}
}
