<?php

namespace App\Filament\Resources\MeetupResource\Pages;

use App\Actions\GenerateOpenGraphImage;
use App\Filament\Resources\MeetupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeetup extends EditRecord
{
	protected static string $resource = MeetupResource::class;
	
	protected function getHeaderActions(): array
	{
		return [
			Actions\DeleteAction::make(),
		];
	}
	
	protected function afterSave()
	{
		GenerateOpenGraphImage::run($this->getRecord());
	}
}
