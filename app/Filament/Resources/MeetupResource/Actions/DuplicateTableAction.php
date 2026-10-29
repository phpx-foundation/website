<?php

namespace App\Filament\Resources\MeetupResource\Actions;

use App\Filament\Resources\MeetupResource\Actions\Concerns\ConfiguresDuplicateAction;
use Filament\Tables\Actions\Action;

class DuplicateTableAction extends Action
{
	use ConfiguresDuplicateAction;
}
