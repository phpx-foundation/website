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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class GroupResource extends Resource
{
	protected static ?string $model = Group::class;
	
	protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
	
	public static function getNavigationIcon(): string|Htmlable|null
	{
		if (request()->attributes->has('group')) {
			return 'heroicon-o-cog';
		}
		
		return parent::getNavigationIcon();
	}
	
	public static function getNavigationBadge(): ?string
	{
		if (! request()->attributes->has('group')) {
			return static::getModel()::count();
		}
		
		return parent::getNavigationBadge();
	}
	
	public static function getPluralModelLabel(): string
	{
		if ($group = request()->attributes->get('group')) {
			return $group->name;
		}
		
		return parent::getPluralModelLabel();
	}
	
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
						->columnSpanFull()
						->required(),
					Forms\Components\TextInput::make('domain')
						->required()
						->maxLength(255)
						->disabled(fn() => ! Auth::user()->isSuperAdmin())
						->rules(['']),
					Forms\Components\Select::make('domain_status')
						->required()
						->options(DomainStatus::class)
						->default(DomainStatus::Pending)
						->disabled(fn() => ! Auth::user()->isSuperAdmin()),
					Forms\Components\Select::make('continent')
						->required()
						->options(Continent::class)
						->default(Continent::NorthAmerica),
					Forms\Components\TextInput::make('region')
						->maxLength(255)
						->helperText('eg. “New York” for NYC'),
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
						->maxLength(255)
						->hidden(fn() => ! Auth::user()->isSuperAdmin()),
					Forms\Components\TextInput::make('meetup_url')
						->url()
						->label('Meetup')
						->maxLength(255),
				]),
				Forms\Components\Fieldset::make('Coordinates')->schema([
					Forms\Components\TextInput::make('latitude')
						->numeric()
						->required()
						->rules(['required', 'numeric', 'between:-90,90', 'decimal:2,8']),
					Forms\Components\TextInput::make('longitude')
						->numeric()
						->required()
						->rules(['required', 'numeric', 'between:-180,180', 'decimal:2,8']),
				]),
				Forms\Components\Section::make('Third-Party Integrations')->schema([
					Forms\Components\Fieldset::make('MailCoach')->schema([
						Forms\Components\TextInput::make('mailcoach_token')
							->label('Token')
							->maxLength(255)
							->rules(['nullable']),
						Forms\Components\TextInput::make('mailcoach_endpoint')
							->label('API Endpoint')
							->maxLength(255)
							->url(),
						Forms\Components\TextInput::make('mailcoach_list')
							->label('List UUID')
							->maxLength(255)
							->rules(['nullable', 'uuid']),
					]),
					Forms\Components\Fieldset::make('Bluesky')->schema([
						Forms\Components\TextInput::make('bsky_did')
							->label('DID')
							->maxLength(255),
						Forms\Components\TextInput::make('bsky_app_password')
							->label('App Password'),
					]),
				]),
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
					->formatStateUsing(function(string $state) {
						[$prefix, $zone] = explode('/', $state, 2);
						$zone = str_replace(['_', '/'], [' ', ', '], $zone);
						return new HtmlString("<div class='opacity-50 text-xs'>{$prefix}</div><div>{$zone}</div>");
					})
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
			RelationManagers\UsersRelationManager::class,
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
