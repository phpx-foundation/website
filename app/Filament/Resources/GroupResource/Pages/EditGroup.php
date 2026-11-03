<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditGroup extends EditRecord
{
	protected static string $resource = GroupResource::class;
	
	protected function getHeaderActions(): array
	{
		return [
			Actions\DeleteAction::make(),
		];
	}
	
	protected function mutateFormDataBeforeFill(array $data): array
	{
		/** @var \App\Models\Group $record */
		$record = $this->getRecord();
		
		try {
			$data['email'] = $record->email;
			
			$data['mailcoach_token'] = $record->mailcoach_token;
			$data['mailcoach_endpoint'] = $record->mailcoach_endpoint;
			$data['mailcoach_list'] = $record->mailcoach_list;
			
			$data['bsky_app_password'] = $record->bsky_app_password;
			
			$data['turnstile_site_key'] = $record->turnstile_site_key;
			$data['turnstile_secret_key'] = $record->turnstile_secret_key;
		} catch (Throwable) {
		}
		
		return $data;
	}
}
