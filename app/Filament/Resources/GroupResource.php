<?php

namespace App\Filament\Resources;

use App\Enums\Continent;
use App\Enums\DomainStatus;
use App\Enums\GroupStatus;
use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GroupResource extends Resource
{
	protected static ?string $model = Group::class;
	
	protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
	
	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Forms\Components\Fieldset::make('Group')->schema([
					Forms\Components\TextInput::make('name')
						->columnSpanFull()
						->required()
						->maxLength(255),
					Forms\Components\Textarea::make('description')
						->columnSpanFull(),
					Forms\Components\TextInput::make('domain')
						->required()
						->maxLength(255),
					Forms\Components\Select::make('domain_status')
						->required()
						->options(DomainStatus::class)
						->default(DomainStatus::Pending),
					Forms\Components\Select::make('continent')
						->required()
						->options(Continent::class)
						->default(Continent::NorthAmerica),
					Forms\Components\TextInput::make('region')
						->maxLength(255),
					Forms\Components\Select::make('status')
						->required()
						->options(GroupStatus::class)
						->default(GroupStatus::Planned),
					Forms\Components\TextInput::make('frequency')
						->required()
						->maxLength(255)
						->default('bi-monthly'),
					Forms\Components\Select::make('timezone')
						->searchable()
						->options(
							collect(timezone_identifiers_list())
								->mapWithKeys(fn(string $timezone) => [$timezone => str($timezone)->after('/')->replace('_', ' ')->toString()])
								->groupBy(fn(string $timezone, string $key) => str($key)->before('/')->replace('_', ' ')->toString(), preserveKeys: true)
						)
						->required()
						->default('America/New_York'),
				]),
				Forms\Components\Fieldset::make('Contact')->schema([
					Forms\Components\TextInput::make('email')
						->email()
						->maxLength(255),
				]),
				Forms\Components\Fieldset::make('Links')->schema([
					Forms\Components\TextInput::make('bsky_url')
						->url()
						->label('Bluesky')
						->maxLength(255),
					Forms\Components\TextInput::make('twitter_url')
						->url()
						->label('Twitter')
						->maxLength(255),
					Forms\Components\TextInput::make('meetup_url')
						->url()
						->label('Meetup')
						->maxLength(255),
				]),
				Forms\Components\TextInput::make('latitude')
					->numeric(),
				Forms\Components\TextInput::make('longitude')
					->numeric(),
				Forms\Components\TextInput::make('mailcoach_endpoint')
					->maxLength(255),
				Forms\Components\TextInput::make('mailcoach_list')
					->maxLength(255),
				Forms\Components\TextInput::make('bsky_did')
					->maxLength(255),
				Forms\Components\Textarea::make('bsky_app_password')
					->columnSpanFull(),
			]);
	}
	
	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('domain')
					->searchable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('domain_status')
					->searchable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('name')
					->searchable(),
				Tables\Columns\TextColumn::make('continent')
					->searchable(),
				Tables\Columns\TextColumn::make('region')
					->searchable(),
				Tables\Columns\TextColumn::make('status')
					->searchable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('frequency')
					->searchable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('timezone')
					->searchable(),
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
				Tables\Columns\TextColumn::make('bsky_did')
					->searchable()
					->toggleable(isToggledHiddenByDefault: true),
			])
			->modifyQueryUsing(function(Builder $query) {
				if ($group = request()->attributes->get('group')) {
					$query->where('id', $group->id);
				}
			})
			->filters([
				//
			])
			->actions([
				Tables\Actions\EditAction::make(),
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
			//
		];
	}
	
	public static function getPages(): array
	{
		return [
			'index' => Pages\ListGroups::route('/'),
			'create' => Pages\CreateGroup::route('/create'),
			'edit' => Pages\EditGroup::route('/{record}/edit'),
		];
	}
}
