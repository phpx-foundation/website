@props(['footer' => null, 'title' => null, 'og' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased bg-black text-white/50">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $title ? "{$title} - {$group->name}" : $group->name }}</title>
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=fira-code:300,400,500,600,700" rel="stylesheet" />
	<style>
	[x-cloak] {
		display: none !important;
	}
	</style>
	@vite('resources/css/app.css')
	@vite('resources/js/app.js')
	
	@isset($og)
		{{ $og }}
	@else
	<meta property="og:url" content="{{ url()->current() }}" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="{{ $title ? "{$title} - {$group->name}" : $group->name }}" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta property="twitter:domain" content="{{ parse_url(url()->current(), PHP_URL_HOST) }}" />
	<meta property="twitter:url" content="{{ url()->current() }}" />
	<meta name="twitter:title" content="{{ $title ? "{$title} - {$group->name}" : $group->name }}" />
	<meta name="twitter:creator" content="{{ str($group->twitter_url)->after('twitter.com/')->prepend('@') }}" />
	@if($group->description)
		<meta name="description" content="{{ $group->description }}" />
		<meta property="og:description" content="{{ $group->description }}" />
		<meta name="twitter:description" content="{{ $group->description }}" />
	@endif
	@if($group->og_asset)
		<meta property="og:image" content="{{ asset("og/{$group->og_asset}") }}" />
		<meta name="twitter:image" content="{{ asset("og/{$group->og_asset}") }}" />
	@endif
	@endisset
	
	<script defer data-domain="{{ $group->domain }}" src="https://plausible.io/js/script.js"></script>
</head>
<body class="flex min-h-full font-sans">
<div {{ $attributes->merge(['class' => 'flex w-full flex-col bg-dots']) }}>
	{{-- Header --}}
	<div class="w-full max-w-4xl mx-auto flex items-center gap-4 p-4">
		@if(url()->current() == url('/'))
			<x-phpx-dropdown />
		@else
			<x-phpx-home />
		@endif
	</div>
	
	{{-- Content --}}
	<div class="w-full max-w-4xl mx-auto flex flex-col items-start justify-center px-4 py-8">
		{{ $slot }}
	</div>
	
	{{-- Footer --}}
	@isset($footer)
		<div class="w-full bg-white text-black border-2 border-black font-semibold {{ str($attributes->get('class'))->contains('justify-') ? '' : 'mt-auto' }}">
			<div class="w-full max-w-4xl mx-auto">
				{{ $footer }}
			</div>
		</div>
	@else
		<div></div>
	@endisset

</div>

<!-- Host: {{ config('app.hostname') }} -->

</body>
</html>
