<?php

namespace App\Filament\Resources\MeetupResource\Actions;

use App\Filament\Resources\MeetupResource\Actions\Concerns\ConfiguresDuplicateAction;
use Filament\Actions\Action;

class DuplicateAction extends Action
{
	use ConfiguresDuplicateAction;
}
