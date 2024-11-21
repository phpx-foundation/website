<?php

namespace App\Models;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;

trait HasDomain
{
	public static function findByDomain(string $domain): ?static
	{
		if (App::isLocal()) {
			$domain = str($domain)->replaceEnd('.test', '.com')->toString();
		}
		
		$container = Container::getInstance();
		$id = class_basename(static::class).":{$domain}";
		
		if (! $container->has($id)) {
			if (! $group = static::firstWhere('domain', $domain)) {
				return null;
			}
			
			$container->instance($id, $group);
		}
		
		return $container->get($id);
	}
}
