<?php

namespace App\Rules;

use App\Models\Group;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TurnstileRule implements ValidationRule
{
	public static function rules(?Group $group = null): array
	{
		$group ??= app(Group::class);
		
		if (empty($group->turnstile_secret_key)) {
			return [];
		}
		
		return [
			'cf-turnstile-response' => [
				'required',
				'string',
				new static($group->turnstile_secret_key),
			],
		];
	}
	
	public function __construct(
		protected string $secret_key,
	) {
	}
	
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		$response = $this->verify($value);
		
		if (! $response->json('success', false)) {
			$fail('There was an issue with your request.');
		}
	}
	
	protected function verify(string $response): Response
	{
		return Http::retry(3, 100)
			->asForm()
			->acceptJson()
			->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
				'secret' => $this->secret_key,
				'response' => $response,
			]);
	}
}
