<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Organizers extends Page
{
	protected static ?string $navigationIcon = 'heroicon-o-user-group';

	protected static ?string $navigationGroup = 'Resources';

	protected static ?string $title = 'Private Resources';

	protected static ?int $navigationSort = PHP_INT_MIN;

	protected static string $view = 'filament.pages.organizers';
	
	public static function canAccess(): bool
	{
		return Auth::user()->isAnyGroupAdmin() || Auth::user()->isSuperAdmin();
	}
	
	public function getHeading(): string
	{
		return 'Private Organizer Resources';
	}
	
	protected function getViewData(): array
	{
		return [
			'mailcoach_promo_code' => config('phpx.organizer_promo_codes.mailcoach'),
		];
	}
}
