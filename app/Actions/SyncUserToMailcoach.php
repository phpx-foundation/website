<?php

namespace App\Actions;

use App\Actions\Concerns\FetchesModelsForCommands;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\User;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncUserToMailcoach
{
    use AsAction;
    use FetchesModelsForCommands;

    public function handle(Group $group, User $user): ?bool
    {
        if (! $mailcoach = $group->mailcoach()) {
            return false;
        }

        $mailcoach_list = $mailcoach->emailList($group->mailcoach_list);
        $subscriber = $mailcoach_list->subscriber($user->email);

        $group_membership = GroupMembership::where(['user_id' => $user->getKey(), 'group_id' => $group->getKey()])->sole();

        if ($subscriber === null && $group_membership->is_subscribed) {
            $mailcoach->createSubscriber($group->mailcoach_list, [
                'email' => $user->email,
                'first_name' => $user->name,
                'tags' => [
                    'phpx',
                    $group->domain,
                    $user->is_potential_speaker
                        ? 'speakers'
                        : 'non-speakers',
                ],
            ]);

            return true;
        }

        if ($subscriber && ! $group_membership->is_subscribed) {
            $mailcoach->unsubscribeSubscriber($subscriber->uuid);

            return false;
        }

        return null;
    }

    public function getCommandSignature(): string
    {
        return 'group:sync-mailcoach {group?} {user?}';
    }

    public function asCommand(Command $command): int
    {
        $group = $this->getGroupFromCommand($command);

        if (! $group->mailcoach()) {
            $command->error('This group does not have Mailcoach configured.');

            return 1;
        }

        $result = $this->handle($group, $this->getUserFromCommand($command, $group));

        match ($result) {
            true => $command->info('User subscribed in Mailcoach!'),
            false => $command->info('User unsubscribed in Mailcoach!'),
            null => $command->info('User unchanged in Mailcoach!'),
        };

        return 0;
    }
}
