<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\GroupRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class GroupsRelationManager extends RelationManager
{
	protected static string $relationship = 'groups';
	
	public function form(Form $form): Form
	{
		return $form
			->schema([
				Forms\Components\TextInput::make('name')
					->required()
					->maxLength(255)
					->disabled()
					->dehydrated(false),
				Forms\Components\Select::make('group_membership.role')
					->options(GroupRole::class)
					->required(),
				Forms\Components\Toggle::make('group_membership.is_subscribed')
					->label('Subscribed to newsletter'),
			]);
	}
	
	public function table(Table $table): Table
	{
		return $table
			->recordTitleAttribute('name')
			->columns([
				Tables\Columns\TextColumn::make('name'),
				Tables\Columns\TextColumn::make('role')
					->badge()
					->color(fn(GroupRole $state) => match ($state) {
						GroupRole::Admin => 'danger',
						GroupRole::Attendee => 'gray',
					}),
				Tables\Columns\ToggleColumn::make('is_subscribed')
					->label('Newsletter'),
			])
			->filters([
				//
			])
			->headerActions([
				Tables\Actions\AttachAction::make()
					->form(fn(Tables\Actions\AttachAction $action) => [
						$action->getRecordSelect(),
						Forms\Components\Select::make('role')
							->options(GroupRole::class)
							->required(),
						Forms\Components\Toggle::make('is_subscribed')
							->label('Subscribed to newsletter'),
					])
			])
			->actions([
				Tables\Actions\EditAction::make()
					->mutateRecordDataUsing(fn(Model $record, array $data): array => [
						...$data,
						'group_membership' => $record->group_membership->toArray(),
					])
					->using(function(Model $record, array $data): Model {
						$record->group_membership->update(Arr::only($data['group_membership'], [
							'role',
							'is_subscribed',
						]));
						return $record;
					}),
				Tables\Actions\DetachAction::make(),
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
			]);
	}
}
