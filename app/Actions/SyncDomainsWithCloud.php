<?php

namespace App\Actions;

use App\Models\Group;
use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class SyncDomainsWithCloud
{
	use AsAction;

	/** @var Collection<string, array>|null */
	protected ?Collection $existing = null;

	/** @return Collection<string, array> Newly created domains, keyed by name */
	public function handle(): Collection
	{
		return $this->missing()
			->mapWithKeys(fn(string $domain) => [$domain => $this->createDomain($domain)]);
	}

	public function getCommandSignature(): string
	{
		return 'cloud:sync-domains {--force}';
	}

	public function asCommand(Command $command): int
	{
		$missing = $this->missing();

		table(
			headers: ['Domain', 'Status'],
			rows: $this->groupDomains()
				->merge($this->existing()->keys())
				->unique()
				->sort()
				->map(fn(string $domain) => [
					$domain,
					$missing->contains($domain)
						? 'Will be added'
						: $this->statusFor($this->existing()->get($domain)),
				]),
		);

		if ($missing->isEmpty()) {
			info('No changes necessary.');
			return 0;
		}

		if ($command->option('force') || confirm('Add missing domains to Laravel Cloud?')) {
			try {
				$created = $this->handle();
			} catch (Throwable $exception) {
				error($exception->getMessage());
				return 1;
			}

			foreach ($created as $domain => $attributes) {
				info("Added {$domain}. DNS records:");
				table(
					headers: ['Record', 'Value'],
					rows: collect(data_get($attributes, 'dns_records', []))
						->map(fn($value, $key) => [$key, is_array($value) ? json_encode($value) : (string) $value])
						->values(),
				);
			}

			return 0;
		}

		return 1;
	}

	protected function createDomain(string $domain): array
	{
		return $this->cloud()
			->post("environments/{$this->environmentId()}/domains", [
				'name' => $domain,
				'cloudflare_strategy' => 'dns_proxy',
				'allow_downtime' => false,
			])
			->json('data.attributes', []);
	}

	/** @return Collection<int, string> */
	protected function missing(): Collection
	{
		return $this->groupDomains()
			->diff($this->existing()->keys())
			->values();
	}

	/** @return Collection<int, string> */
	protected function groupDomains(): Collection
	{
		return Group::query()->pluck('domain')->unique()->sort()->values();
	}

	/** @return Collection<string, array> Domain attributes keyed by name */
	protected function existing(): Collection
	{
		if (null !== $this->existing) {
			return $this->existing;
		}

		$domains = collect();
		$url = "environments/{$this->environmentId()}/domains";

		while ($url) {
			$response = $this->cloud()->get($url);

			foreach ($response->json('data', []) as $domain) {
				$domains->put(data_get($domain, 'attributes.name'), data_get($domain, 'attributes', []));
			}

			$url = $response->json('links.next');
		}

		return $this->existing = $domains;
	}

	protected function statusFor(array $attributes): string
	{
		return collect(['hostname_status', 'ssl_status', 'origin_status'])
			->map(fn(string $key) => str($key)->before('_status')->append(': ', data_get($attributes, $key, 'unknown')))
			->implode(', ');
	}

	protected function cloud(): PendingRequest
	{
		return Http::baseUrl(config('services.cloud.url'))
			->withToken(config('services.cloud.token'))
			->acceptJson()
			->throw();
	}

	protected function environmentId(): string
	{
		return config('services.cloud.environment');
	}
}
