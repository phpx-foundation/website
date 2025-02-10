<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeetupResource\Pages;
use App\Filament\Resources\MeetupResource\RelationManagers;
use App\Models\Meetup;
use App\Rules\CanUpdateGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MeetupResource extends Resource
{
    protected static ?string $model = Meetup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return Meetup::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group_id')
                    ->relationship(name: 'group', titleAttribute: 'name')
                    ->default(function () {
                        if ($group = request()->attributes->get('group')) {
                            return (string) $group->getKey();
                        }

                        return null;
                    })
                    ->searchable()
                    ->preload()
                    ->label('Group')
                    ->required()
                    ->rules(['required', 'exists:groups,id', new CanUpdateGroup])
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('Start')
                    ->required()
                    ->beforeOrEqual('ends_at')
                    ->rules(['required', 'date']),
                Forms\Components\DateTimePicker::make('ends_at')
                    ->label('End')
                    ->required()
                    ->afterOrEqual('starts_at')
                    ->rules(['required', 'date']),
                Forms\Components\MarkdownEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Date')
                    ->dateTime('M jS, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('group.name')
                    ->sortable()
                    ->hidden(fn () => request()->attributes->get('group') !== null),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('RSVPs')
                    ->counts('users'),
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
            ->defaultSort('starts_at', 'desc')
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
            'index' => Pages\ListMeetups::route('/'),
            'create' => Pages\CreateMeetup::route('/create'),
            'edit' => Pages\EditMeetup::route('/{record}/edit'),
        ];
    }
}
