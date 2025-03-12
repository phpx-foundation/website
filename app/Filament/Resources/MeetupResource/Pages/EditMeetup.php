<?php

namespace App\Filament\Resources\MeetupResource\Pages;

use App\Actions\GenerateOpenGraphImage;
use App\Data\BskyImageData;
use App\Facades\Bluesky;
use App\Filament\Resources\MeetupResource;
use App\Models\Meetup;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Notifications\Notification;
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
				->form($this->getBskyFormFields())->action(function($form) {
					$state = $form->getState();
					if ($state['include_image']) {
						$img = new BskyImageData(
							$this->record->open_graph_image_file,
							$this->record->group->name,
							"Meetup @ {$this->record->location} on {$this->record->range()}",
							$this->record->rsvp_url
						);
					} else {
						$img = null;
					}
					Bluesky::post($this->record->group, $state['post'], $state['tags'], $img);
					Notification::make()->title('Posted to bsky')->success()->send();
				}),
			Actions\Action::make('view')
				->icon('heroicon-o-arrow-top-right-on-square')
				->iconPosition(IconPosition::After)
				->url(fn(Meetup $record) => $record->rsvp_url)
				->openUrlInNewTab(),
		];
	}

	protected function getBskyFormFields()
	{
		$viewUrl = route('meetup.show-rsvp', $this->record);
		$formFields = [
			MarkdownEditor::make('post')->default("📆 Meetup @ [{$this->record->location}]($viewUrl)\n\n{$this->record->range()}"),
		];
		if ($ogImage = $this->record->open_graph_image_url) {
			$formFields[] = Checkbox::make('include_image')->default(true)->live();
			$formFields[] = Placeholder::make('image')->content(new HtmlString('<img src="'.$ogImage.'">'))->visible(fn($get) => $get('include_image'))->live();
		}

		$formFields[] = TagsInput::make('tags')->default(['#Meetup', '#PHP', '#Laravel']);
		return $formFields;
	}

	protected function afterSave()
	{
		GenerateOpenGraphImage::run($this->getRecord());
	}
}
