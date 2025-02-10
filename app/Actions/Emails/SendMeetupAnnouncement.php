<?php

namespace App\Actions\Emails;

use App\Actions\Concerns\FetchesModelsForCommands;
use App\Models\Meetup;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class SendMeetupAnnouncement
{
    use AsAction;
    use FetchesModelsForCommands;
    use SendsTransactionalEmails;

    public function handle(Meetup $meetup, User $user): bool
    {
        return $this->sendTransactionalEmail($meetup, $user, [
            'meetup' => $meetup->toArray(),
            'user' => $user->toArray(),
        ]);
    }

    public function getCommandSignature(): string
    {
        return 'email:send-announcement {meetup?} {user?}';
    }
}
