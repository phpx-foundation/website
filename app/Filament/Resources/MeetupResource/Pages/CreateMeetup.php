<?php

namespace App\Filament\Resources\MeetupResource\Pages;

use App\Actions\GenerateOpenGraphImage;
use App\Filament\Resources\MeetupResource;
use App\Models\Meetup;
use Filament\Resources\Pages\CreateRecord;

class CreateMeetup extends CreateRecord
{
	protected static string $resource = MeetupResource::class;
	
	protected function fillForm(): void
	{
		$this->callHook('beforeFill');
		
		$source = $this->getDuplicateSource();
		
		$this->form->fill($source ? $this->getDuplicateFormData($source) : null);
		
		$this->callHook('afterFill');
	}
	
	protected function getDuplicateSource(): ?Meetup
	{
		$id = request()->query('duplicate');
		
		if (! is_string($id)) {
			return null;
		}
		
		$source = static::getResource()::resolveRecordRouteBinding($id);
		
		if (! $source || ! static::getResource()::canReplicate($source)) {
			return null;
		}
		
		return $source;
	}
	
	/**
	 * Filling a form skips every field's default(), so any field added to
	 * MeetupResource::form() must also be added here. The custom Open Graph image
	 * is left out on purpose: it often shows event-specific details, and the
	 * duplicate shouldn't share the source meetup's file.
	 *
	 * @return array<string, mixed>
	 */
	protected function getDuplicateFormData(Meetup $source): array
	{
		return [
			...$source->only([
				'group_id',
				'location',
				'capacity',
				'description',
				'external_rsvp_url',
			]),
			...$source->nextMonthDates(),
		];
	}
	
	protected function afterCreate()
	{
		GenerateOpenGraphImage::run($this->getRecord());
	}
	
	protected function mutateFormDataBeforeCreate(array $data): array
	{
		if ($group = request()->attributes->get('group')) {
			$data['group_id'] = $group->getKey();
		}
		
		return $data;
	}
}
