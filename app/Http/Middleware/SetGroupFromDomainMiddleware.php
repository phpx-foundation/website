<?php

namespace App\Http\Middleware;

use App\Enums\DomainStatus;
use App\Enums\RootDomains;
use App\Models\Group;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetGroupFromDomainMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		if ($this->isRootDomain($request)) {
			return $next($request);
		}
		
		if (! $group = $this->group($request)) {
			throw new NotFoundHttpException();
		}
		
		$this->updateDomainStatusIfNecessary($group);
		
		Container::getInstance()->instance(Group::class, $group);
		Container::getInstance()->instance("group:{$group->domain}", $group);
		Context::add('group_id', $group->getKey());
		View::share('group', $group);
		$request->attributes->set('group', $group);
		
		config(['app.timezone' => $group->timezone]);
		
		return $next($request);
	}
	
	protected function group(Request $request): ?Group
	{
		$host = str($request->host())->after('www.');
		
		if (App::isLocal()) {
			$host = $host->replaceEnd('.test', '.com');
		}
		
		$attributes = Cache::remember(
			key: "group:{$host}",
			ttl: now()->addWeek(),
			callback: fn() => Group::toBase()->where('domain', $host)->first()
		);
		
		return $attributes
			? (new Group())->newFromBuilder($attributes)
			: null;
	}
	
	protected function updateDomainStatusIfNecessary(Group $group): void
	{
		if ($group->domain_status !== DomainStatus::Confirmed) {
			$group->update(['domain_status' => DomainStatus::Confirmed]);
			Cache::forget('phpx-network');
		}
	}
	
	protected function isRootDomain(Request $request): bool
	{
		return collect(RootDomains::cases())
			->map(fn(RootDomains $case) => $case->value)
			->contains($request->host());
	}
}
