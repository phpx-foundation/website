<?php

namespace App\Filament\Resources;

use App\Enums\Continent;
use App\Enums\DomainStatus;
use App\Enums\GroupStatus;
use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class GroupResource extends Resource
{
	protected static ?string $model = Group::class;
	
	protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
	
	public static function getNavigationBadge(): ?string
	{
		return Group::count();
	}
	
	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Tabs::make()
					->schema([
						static::getFormGeneralTab(),
						static::getFormContactTab(),
						static::getFormIntegrationsTab(),
					])
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
			->filters([])
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
	
	protected static function getFormGeneralTab(): Tab
	{
		return Tab::make('General')->columns(2)->schema(
			[
				Section::make('General')->collapsible()->columns(3)->schema([
					Forms\Components\TextInput::make('name')
						->required()
						->maxLength(255)
						->unique(ignoreRecord: true),
					Forms\Components\Select::make('status')
						->required()
						->options(GroupStatus::class)
						->default(GroupStatus::Planned),
					Forms\Components\TextInput::make('frequency')
						->required()
						->maxLength(255)
						->default('bi-monthly'),
					
					Forms\Components\Textarea::make('description')
						->columnSpanFull()
						->required(),
					
					Forms\Components\TextInput::make('domain')
						->required()
						->maxLength(255)
						->disabled(fn() => ! Auth::user()->isSuperAdmin())
						->dehydrated(fn() => ! Auth::user()->isSuperAdmin())
						->unique(ignoreRecord: true),
					Forms\Components\Select::make('domain_status')
						->required()
						->options(DomainStatus::class)
						->default(DomainStatus::Pending)
						->disabled(fn() => ! Auth::user()->isSuperAdmin())
						->dehydrated(fn() => ! Auth::user()->isSuperAdmin()),
				]),
				Section::make('Location')->collapsible()->collapsed()->columns(3)->schema([
					Forms\Components\Select::make('continent')
						->required()
						->options(Continent::class)
						->default(Continent::NorthAmerica),
					Forms\Components\TextInput::make('region')
						->maxLength(255)
						->helperText('eg. “New York” for NYC'),
					Forms\Components\Select::make('timezone')
						->searchable()
						->options(
							collect(timezone_identifiers_list())
								->mapWithKeys(fn(string $timezone) => [$timezone => str($timezone)->after('/')->replace('_', ' ')->toString()])
								->groupBy(fn(string $timezone, string $key) => str($key)->before('/')->replace('_', ' ')->toString(), preserveKeys: true)
						)
						->required()
						->default('America/New_York'),
					Forms\Components\TextInput::make('latitude')
						->numeric()
						->required()
						->rules(['required', 'numeric', 'between:-90,90', 'decimal:2,8']),
					Forms\Components\TextInput::make('longitude')
						->numeric()
						->required()
						->rules(['required', 'numeric', 'between:-180,180', 'decimal:2,8']),
				]),
			]
		);
	}
	
	protected static function getFormContactTab(): Tab
	{
		return Tab::make('Contact/Links')->schema(
			[
				Forms\Components\TextInput::make('email')
					->email()
					->maxLength(255),
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
			]
		);
	}
	
	protected static function getFormIntegrationsTab(): Tab
	{
		return Tab::make('Integrations')->schema([
			Section::make('MailCoach')
				->collapsible()
				->tap(static::integrationIcon(['mailcoach_token', 'mailcoach_endpoint', 'mailcoach_list']))
				->collapsed(fn($record) => ! static::anyFieldIsEmpty($record, ['mailcoach_token', 'mailcoach_endpoint', 'mailcoach_list']))
				->schema([
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
			Section::make('Bluesky')
				->collapsible()
				->tap(static::integrationIcon(['bsky_did', 'bsky_app_password']))
				->collapsed(fn($record) => ! static::anyFieldIsEmpty($record, ['bsky_did', 'bsky_app_password']))
				->schema([
					Forms\Components\TextInput::make('bsky_did')
						->label('DID')
						->maxLength(255),
					Forms\Components\TextInput::make('bsky_app_password')
						->label('App Password'),
				]),
			Section::make('Cloudflare Turnstile')
				->collapsible()
				->collapsed(fn($record) => ! static::anyFieldIsEmpty($record, ['turnstile_site_key', 'turnstile_secret_key']))
				->tap(static::integrationIcon(['turnstile_site_key', 'turnstile_secret_key']))
				->schema([
					Forms\Components\TextInput::make('turnstile_site_key')
						->label('Site Key')
						->maxLength(255),
					Forms\Components\TextInput::make('turnstile_secret_key')
						->label('Secret Key'),
				]),
		]);
	}
	
	protected static function anyFieldIsEmpty($record, array $fields): bool
	{
		foreach ($fields as $field) {
			if (empty(data_get($record, $field))) {
				return true;
			}
		}
		
		return false;
	}
	
	protected static function integrationIcon($fields): \Closure
	{
		return fn(Section $section) => $section
			->icon(fn($record) => static::anyFieldIsEmpty($record, $fields) ? 'heroicon-m-link-slash' : 'heroicon-m-link')
			->iconColor(fn($record) => static::anyFieldIsEmpty($record, $fields) ? 'warning' : 'success');
	}
}
