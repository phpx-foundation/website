<?php

namespace App\Providers;

use App\Enums\DomainStatus;
use App\Models\ExternalGroup;
use App\Models\Group;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
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
		
		Actions::registerCommands();
		
		Route::middleware('web')->group(fn() => Actions::registerRoutes());
		
		$this->sharePhpxNetwork();
	}
	
	protected function sharePhpxNetwork()
	{
		$this->callAfterResolving(Factory::class, function(Factory $view) {
			$network = Cache::remember('phpx-network', now()->addWeek(), function() {
				try {
					return Group::query()
						->select('domain', 'name', 'region')
						->where('domain_status', DomainStatus::Confirmed)
						->get()
						->mapWithKeys(fn(Group $group) => [$group->domain => $group->label()])
						->toArray();
				} catch (Throwable) {
					return [];
				}
			});
			
			$external = Cache::remember('phpx-network-external', now()->addWeek(), function() {
				try {
					return ExternalGroup::query()
						->select('domain', 'name', 'region')
						->get()
						->mapWithKeys(fn(ExternalGroup $g) => [$g->domain => $g->label()])
						->toArray();
				} catch (Throwable) {
					return [];
				}
			});
			
			$view->share('phpx_network', $network);
			$view->share('phpx_external', $external);
		});
	}
}
