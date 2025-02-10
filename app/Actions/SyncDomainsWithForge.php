<?php

namespace App\Actions;

use App\Models\Group;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Laravel\Forge\Facades\Forge;
use Laravel\Forge\Resources\Site;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\table;

class SyncDomainsWithForge
{
    use AsAction;

    protected ?Collection $aliases = null;

    protected ?Site $site = null;

    public function handle(): Site
    {
        return Forge::addSiteAliases(
            serverId: config('services.forge.server'),
            siteId: config('services.forge.site'),
            aliases: $this->aliases()->values()->toArray(),
        );
    }

    public function getCommandSignature(): string
    {
        return 'forge:sync-domains {--force}';
    }

    public function asCommand(Command $command): int
    {
        $changes = $this->aliases()->diff($this->site()->aliases);

        table(
            headers: ['Domain', 'Status'],
            rows: $this->aliases()->map(fn ($alias) => [
                $alias,
                $changes->contains($alias) ? 'Will be added' : 'Exists',
            ]),
        );

        if ($command->option('force') || confirm('Save these changes?')) {
            try {
                $site = $this->handle();
            } catch (Throwable $exception) {
                error($exception->getMessage());

                return 1;
            }

            info('New aliases: '.implode(', ', $site->aliases));

            return 0;
        }

        return 1;
    }

    protected function site(): Site
    {
        return $this->site ??= Forge::site(
            serverId: config('services.forge.server'),
            siteId: config('services.forge.site'),
        );
    }

    protected function aliases(): Collection
    {
        return $this->aliases ??= Group::query()->pluck('domain')
            ->merge($this->site()->aliases)
            ->unique()
            ->sort();
    }
}
