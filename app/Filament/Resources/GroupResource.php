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

	public static function getGeneralTab(): Tab
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

	public static function getContactTab(): Tab
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

	public static function getIntegrationsTab(): Tab
	{
		return Tab::make('Integrations')->schema([
			Section::make('MailCoach')->collapsible()->iconColor(function($record) {
				return static::emptyFields($record, ['mailcoach_token', 'mailcoach_endpoint', 'mailcoach_list']) ? 'warning' : 'success';
			})->icon(function($record) {
				return static::getIntegrationIcon($record, ['mailcoach_token', 'mailcoach_endpoint', 'mailcoach_list']);
			})->collapsed(function($record) {
				return ! static::emptyFields($record, ['mailcoach_token', 'mailcoach_endpoint', 'mailcoach_list']);
			})->schema([
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
			Section::make('Bluesky')->collapsible()->iconColor(function($record) {
				return static::emptyFields($record, ['bsky_did', 'bsky_app_password']) ? 'warning' : 'success';
			})->icon(function($record) {
				return static::getIntegrationIcon($record, ['bsky_did', 'bsky_app_password']);
			})->collapsed(function($record) {
				return ! static::emptyFields($record, ['bsky_did', 'bsky_app_password']);
			})->schema([
				Forms\Components\TextInput::make('bsky_did')
					->label('DID')
					->maxLength(255),
				Forms\Components\TextInput::make('bsky_app_password')
					->label('App Password'),
			]),
			Section::make('Cloudflare Turnstile')->collapsible()->collapsed(function($record) {
				return ! static::emptyFields($record, ['turnstile_site_key', 'turnstile_secret_key']);
			})->iconColor(function($record) {
				return static::emptyFields($record, ['turnstile_site_key', 'turnstile_secret_key']) ? 'warning' : 'success';
			})->icon(function($record) {
				return static::getIntegrationIcon($record, ['turnstile_site_key', 'turnstile_secret_key']);
			})->schema([
				Forms\Components\TextInput::make('turnstile_site_key')
					->label('Site Key')
					->maxLength(255),
				Forms\Components\TextInput::make('turnstile_secret_key')
					->label('Secret Key'),
			]),
		]);
	}

	public static function getDefaultsTab(): Tab
	{
		return Tab::make('Meetup Defaults')->columns(2)->schema([
			TextInput::make('default_location'),
			TextInput::make('default_capacity')->numeric(),
			TimePicker::make('default_start'),
			TimePicker::make('default_end'),
		]);
	}

	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Tabs::make()->schema([
					static::getGeneralTab(),
					static::getDefaultsTab(),
					static::getContactTab(),
					static::getIntegrationsTab(),
				])->columnSpanFull(),
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

	protected static function emptyFields($record, $fields)
	{
		foreach ($fields as $field) {
			if (empty($record->{$field})) {
				return true;
			}
		}
		return false;
	}

	protected static function getIntegrationIcon($record, $fields)
	{
		return static::emptyFields($record, $fields) ? 'heroicon-m-link-slash' : 'heroicon-m-link';
	}
}
