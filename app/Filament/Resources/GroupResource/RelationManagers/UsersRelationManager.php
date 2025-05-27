<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Enums\GroupRole;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** @property \App\Models\Group $ownerRecord */
class UsersRelationManager extends RelationManager
{
	protected static string $relationship = 'users';
	
	protected static ?string $title = 'Members';
	
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
				Tables\Columns\IconColumn::make('is_subscribed')
					->state(fn(User $user) => $user->group_membership?->is_subscribed)
					->boolean()
					->label('Subscribed')
					->sortable(),
				Tables\Columns\IconColumn::make('is_potential_speaker')
					->label('Potential Speaker')
					->boolean()
					->sortable(),
			])
			->filters([])
			->headerActions([
				// Tables\Actions\CreateAction::make(),
			])
			->actions([
				// Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make()->label('Remove'),
				Tables\Actions\Action::make('remove-admin')
					->label('Remove Admin')
					->icon('heroicon-m-shield-exclamation')
					->color(Color::Red)
					->requiresConfirmation()
					->modalDescription('Are you sure you want to remove this user’s administrative access to the group?')
					->action(function(User $record) {
						$record->setGroupRole($this->ownerRecord, GroupRole::Attendee);
					})
					->visible(function(User $record) {
						return $record->isNot(auth()->user()) && $record->isGroupAdmin($this->ownerRecord);
					}),
				Tables\Actions\Action::make('make-admin')
					->label('Make Admin')
					->icon('heroicon-m-shield-check')
					->requiresConfirmation()
					->modalDescription('Are you sure you want to give this user administrative access to the group?')
					->action(function(User $record) {
						$record->setGroupRole($this->ownerRecord, GroupRole::Admin);
					})
					->visible(function(User $record) {
						return $record->isNot(auth()->user()) && ! $record->isGroupAdmin($this->ownerRecord);
					}),
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
			]);
	}
}
