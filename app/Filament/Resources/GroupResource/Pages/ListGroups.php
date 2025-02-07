<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListGroups extends ListRecords
{
	protected static string $resource = GroupResource::class;
	
	protected function getHeaderActions(): array
	{
		return [
			Actions\CreateAction::make(),
		];
	}
	
	public function table(Table $table): Table
	{
		if ($group = request()->attributes->get('group')) {
			redirect()->to(GroupResource::getUrl('edit', ['record' => $group]));
		}
		
		return parent::table($table);
	}
}
