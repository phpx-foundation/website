<?php

namespace App\Filament\Resources\MeetupResource\Pages;

use App\Actions\GenerateOpenGraphImage;
use App\Filament\Resources\MeetupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeetup extends CreateRecord
{
	protected static string $resource = MeetupResource::class;
	
	protected function afterCreate()
	{
		GenerateOpenGraphImage::run($this->getRecord());
	}
}
