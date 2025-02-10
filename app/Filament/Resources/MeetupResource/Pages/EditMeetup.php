<?php

namespace App\Filament\Resources\MeetupResource\Pages;

use App\Actions\GenerateOpenGraphImage;
use App\Filament\Resources\MeetupResource;
use App\Models\Meetup;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;

class EditMeetup extends EditRecord
{
    protected static string $resource = MeetupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->iconPosition(IconPosition::After)
                ->url(fn (Meetup $record) => $record->rsvp_url)
                ->openUrlInNewTab(),
        ];
    }

    protected function afterSave()
    {
        GenerateOpenGraphImage::run($this->getRecord());
    }
}
