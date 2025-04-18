<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
	protected static ?string $model = User::class;
	
	protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
	
	public static function getNavigationBadge(): ?string
	{
		return User::whereVisibleToUser()->count();
	}
	
	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Forms\Components\Select::make('current_group_id')
					->relationship('current_group', 'name')
					->label('Current Group')
					->default(function() {
						if ($group = request()->attributes->get('group')) {
							return (string) $group->getKey();
						}
						
						return null;
					})
					->searchable()
					->preload()
					->dehydrated(false)
					->disabled()
					->columnSpanFull(),
				Forms\Components\TextInput::make('name')
					->required()
					->maxLength(255),
				Forms\Components\TextInput::make('password')
					->password()
					->required()
					->maxLength(255),
				Forms\Components\TextInput::make('email')
					->email()
					->required()
					->maxLength(255),
				Forms\Components\DateTimePicker::make('email_verified_at'),
				Forms\Components\Toggle::make('is_potential_speaker')
					->required(),
			]);
	}
	
	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('name')
					->searchable(),
				Tables\Columns\TextColumn::make('email')
					->searchable(),
				Tables\Columns\TextColumn::make('current_group.name')
					->numeric()
					->sortable(),
				Tables\Columns\IconColumn::make('is_potential_speaker')
					->boolean(),
				Tables\Columns\TextColumn::make('email_verified_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('created_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('updated_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('deleted_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
			])
			->modifyQueryUsing(fn(Builder $query) => $query->with('groups')->whereVisibleToUser())
			->filters([
				Tables\Filters\Filter::make('likely_bots')
					->label('Likely bots')
					->query(fn(Builder $query) => $query->whereRaw("REGEXP_LIKE (`name`, '^[[:alpha:]]+[[:upper:]][[:alpha:]]+$', 'c')")),
				Tables\Filters\Filter::make('likely_spam')
					->label('Likely spam')
					->query(fn(Builder $query) => $query->where(
						fn(Builder $query) => $query
							->orWhereLike('name', 'http:')
							->orWhereLike('name', 'https:')
					)),
				Tables\Filters\Filter::make('common_emails')
					->label('Common email domains')
					->query(fn(Builder $query) => $query->where('email', 'REGEXP', '(gmail.com|outlook.com|yahoo.com|msn.com|live.com|hotmail.com|hotmail.co.uk)$')),
			])
			->actions([
				Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make(),
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
			]);
	}
	
	public static function getRelations(): array
	{
		return [
			RelationManagers\GroupsRelationManager::class,
		];
	}
	
	public static function getPages(): array
	{
		return [
			'index' => Pages\ListUsers::route('/'),
			'create' => Pages\CreateUser::route('/create'),
			'edit' => Pages\EditUser::route('/{record}/edit'),
		];
	}
}
