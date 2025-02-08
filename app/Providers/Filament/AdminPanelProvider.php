<?php

namespace App\Providers\Filament;

use App\Enums\RootDomains;
use App\Http\Middleware\SetGroupFromDomainMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
	public function panel(Panel $panel): Panel
	{
		return $panel
			->default()
			->login()
			// ->registration()
			->passwordReset()
			->emailVerification()
			->profile()
			->id('admin')
			->path('admin')
			->colors(['primary' => Color::Amber])
			->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
			->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
			->pages([
				// Pages\Dashboard::class,
			])
			->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
			->navigationItems([
				NavigationItem::make('Homepage')
					->group('Resources')
					->url(url('/'))
					->openUrlInNewTab()
					->icon('heroicon-o-home')
					->hidden(fn() => ! request()->attributes->has('group')),
				NavigationItem::make('PHP×World')
					->group('Resources')
					->url('https://'.RootDomains::Production->value)
					->openUrlInNewTab()
					->icon('heroicon-o-globe-alt'),
				NavigationItem::make('For Organizers')
					->group('Resources')
					->url('https://'.RootDomains::Production->value.'/organizers')
					->openUrlInNewTab()
					->icon('heroicon-o-document'),
				NavigationItem::make('Running Events')
					->group('Resources')
					->url('https://'.RootDomains::Production->value.'/running-events')
					->openUrlInNewTab()
					->icon('heroicon-o-document'),
			])
			->widgets([
				// Widgets\AccountWidget::class,
				// PlausibleWidget::class,
			])
			->middleware([
				EncryptCookies::class,
				AddQueuedCookiesToResponse::class,
				StartSession::class,
				AuthenticateSession::class,
				ShareErrorsFromSession::class,
				VerifyCsrfToken::class,
				SubstituteBindings::class,
				DisableBladeIconComponents::class,
				DispatchServingFilamentEvent::class,
			])
			->middleware([
				SetGroupFromDomainMiddleware::class,
			], isPersistent: true)
			->authMiddleware([
				Authenticate::class,
			]);
	}
}
