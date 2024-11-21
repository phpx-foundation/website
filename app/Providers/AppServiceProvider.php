<?php

namespace App\Providers;

use App\Enums\DomainStatus;
use App\Models\ExternalGroup;
use App\Models\Group;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\Actions\Facades\Actions;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		//
	}
	
	public function boot(): void
	{
		Model::unguard();
		
		if (App::isProduction()) {
			URL::forceScheme('https');
		}
		
		Actions::registerCommands();
		
		Route::middleware('web')->group(fn() => Actions::registerRoutes());
		
		$this->sharePhpxNetwork();
	}
	
	protected function sharePhpxNetwork(): void
	{
		$this->callAfterResolving(Factory::class, function(Factory $view) {
			$data = Cache::remember('phpx-network', now()->addWeek(), function() {
				$groups = Group::query()
					->where('domain_status', DomainStatus::Confirmed)
					->get()
					->map(fn(Group $group) => [$group::class, $group->attributesToArray()]);
				
				$external = ExternalGroup::query()->get()
					->map(fn(ExternalGroup $group) => [$group::class, $group->attributesToArray()]);
				
				return $groups->merge($external)->values()->toArray();
			});
			
			/** @var \Illuminate\Support\Collection<string, Group|ExternalGroup> $network */
			$network = collect($data)
				->map(function (array $record) {
					[$fqcn, $attributes] = $record;
					return (new $fqcn)->newFromBuilder($attributes);
				});
			
			$view->share('phpx_network', $network);
		});
	}
}
