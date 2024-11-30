<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
	
	protected static ?int $navigationSort = 9999;

    protected static string $view = 'filament.pages.settings';
	
	public static function canAccess(): bool
	{
		return request()->attributes->has('group');
	}
}
