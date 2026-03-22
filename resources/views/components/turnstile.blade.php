@props([
	'site_key' => app(\App\Models\Group::class)->turnstile_site_key, 
])

@unless(empty($site_key))
	
	@once
		<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
	@endonce
	
	<div {{ $attributes->merge([
		'class' => 'cf-turnstile',
		'data-sitekey' => $site_key,	
	]) }}></div>

@endunless
