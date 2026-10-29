<?php

namespace App\Filament\Resources\MeetupResource\Actions\Concerns;

use App\Filament\Resources\MeetupResource;
use App\Models\Meetup;
use Filament\Support\Facades\FilamentIcon;

trait ConfiguresDuplicateAction
{
	public static function getDefaultName(): ?string
	{
		return 'duplicate';
	}
	
	protected function setUp(): void
	{
		parent::setUp();
		
		$this->icon(FilamentIcon::resolve('actions::replicate-action') ?? 'heroicon-m-square-2-stack');
		$this->color('gray');
		$this->url(fn(Meetup $record) => MeetupResource::getUrl('create', ['duplicate' => $record->getKey()]));
		$this->authorize(fn(Meetup $record): bool => MeetupResource::canCreate() && MeetupResource::canReplicate($record));
	}
}
