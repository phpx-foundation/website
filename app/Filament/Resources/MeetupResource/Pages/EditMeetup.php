<?php

namespace App\Filament\Resources\MeetupResource\Pages;

use App\Actions\GenerateOpenGraphImage;
use App\Filament\Resources\MeetupResource;
use App\Models\Meetup;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\HtmlString;

class EditMeetup extends EditRecord
{
	protected static string $resource = MeetupResource::class;

	protected function getHeaderActions(): array
	{
		return [
			Actions\DeleteAction::make(),
			Actions\Action::make('bsky')->label('Announce on Bluesky')
				->form($this->getBskyFormFields())->action(function () {}),
			Actions\Action::make('view')
				->icon('heroicon-o-arrow-top-right-on-square')
				->iconPosition(IconPosition::After)
				->url(fn(Meetup $record) => $record->rsvp_url)
				->openUrlInNewTab(),
		];
	}

	protected function getBskyFormFields()
	{
		$formFields = [
			Textarea::make('post')->rows(4)->default("📆 Meetup @ {$this->record->location}\n\n{$this->record->range()}"),
		];
		if ($ogImage = $this->record->open_graph_image_url) {
			$formFields[] = Checkbox::make('include_image')->default(true)->live();
			$formFields[] = Placeholder::make('image')->content(new HtmlString('<img src="' . $ogImage . '">'))->visible(fn($get) => $get('include_image'))->live();
		}

		$formFields[] = TagsInput::make('tags')->default(['#Meetup', '#PHP', '#Laravel']);
		return $formFields;
	}

	protected function afterSave()
	{
		GenerateOpenGraphImage::run($this->getRecord());
	}
}
