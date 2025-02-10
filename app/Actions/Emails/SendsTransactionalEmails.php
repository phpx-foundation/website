<?php

namespace App\Actions\Emails;

use App\Models\Meetup;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

trait SendsTransactionalEmails
{
    protected ?string $abort_reason = null;

    abstract public function handle(Meetup $meetup, User $user): bool;

    public function asCommand(Command $command): int
    {
        $meetup = $this->getMeetupFromCommand($command);
        $user = $this->getUserFromCommand($command, $meetup);

        if ($this->handle($meetup, $user)) {
            $command->info('Email sent!');

            return 0;
        }

        $command->error("Email not sent: '{$this->abort_reason}'");

        return 1;
    }

    protected function sendTransactionalEmail(Meetup $meetup, User $user, array $replacements, ?string $action_name = null): bool
    {
        if (! $meetup->group->email) {
            $this->abort_reason = 'No group email';

            return false;
        }

        if (! $mailcoach = $meetup->group->mailcoach()) {
            $this->abort_reason = 'Mailcoach not configured';

            return false;
        }

        $transactional_email = $meetup->group->mailcoach_transactional_emails()
            ->where('action_name', $action_name ?? static::class)
            ->first();

        if (! $transactional_email) {
            $this->abort_reason = 'Transactional message not configured';

            return false;
        }

        $mailcoach->sendTransactionMail(
            name: $transactional_email->mail_name,
            from: "{$meetup->group->name} <{$meetup->group->email}>",
            to: "{$user->name} <{$user->email}>",
            replacements: Arr::dot($replacements),
        );

        return true;
    }
}
