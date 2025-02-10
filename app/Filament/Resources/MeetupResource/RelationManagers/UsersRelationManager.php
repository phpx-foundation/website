<?php

namespace App\Filament\Resources\MeetupResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
	protected static string $relationship = 'users';
	
	protected static ?string $title = 'RSVPs';
	
	public function form(Form $form): Form
	{
		return $form
			->schema([
				Forms\Components\TextInput::make('name')
					->required()
					->maxLength(255),
			]);
	}
	
	public function table(Table $table): Table
	{
		return $table
			->recordTitleAttribute('name')
			->columns([
				Tables\Columns\TextColumn::make('name'),
				Tables\Columns\TextColumn::make('email')
					->url(fn(User $user) => "mailto:{$user->email}"),
				Tables\Columns\IconColumn::make('is_potential_speaker')
					->label('Potential Speaker'),
			])
			->filters([
				
			])
			->headerActions([
				// Tables\Actions\CreateAction::make(),
			])
			->actions([
				// Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make()->label('Remove'),
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
			]);
	}
}
