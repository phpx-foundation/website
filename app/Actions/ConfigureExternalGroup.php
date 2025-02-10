<?php

namespace App\Actions;

use App\Models\ExternalGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\text;

class ConfigureExternalGroup
{
    use AsAction;

    public function handle(
        string $domain,
        string $name,
        string $region,
    ): ExternalGroup {
        $group = ExternalGroup::updateOrCreate([
            'domain' => $domain,
        ], [
            'name' => $name,
            'region' => $region ?: null,
        ]);

        Cache::clear();

        return $group;
    }

    public function getCommandSignature(): string
    {
        return 'group:configure-external';
    }

    public function isCommandHidden(): bool
    {
        return true;
    }

    public function asCommand(Command $command): int
    {
        $domain = strtolower(text('What is the domain?', default: '', required: true));

        $existing = ExternalGroup::where('domain', $domain)->firstOrNew(values: [
            'domain' => $domain,
        ]);

        $name = text('What is the group name?', default: str($existing->name), required: true);
        $region = text('What is the group region?', default: str($existing->region), hint: 'eg. PHP×ATL might be "Atlanta"');

        $group = $this->handle(
            domain: $domain,
            name: $name,
            region: $region,
        );

        $command->info($group->wasRecentlyCreated
            ? "Created external group <{$group->getKey()}>!"
            : "Updated external group <{$group->getKey()}>!");

        return 0;
    }
}
