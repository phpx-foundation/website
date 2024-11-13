@props(['footer' => null, 'title' => null, 'og' => null])
@php View::share('group', (object) ['name' => 'Anywhere', 'domain' => 'phpx.world']); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased bg-black text-white/50">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PHP×</title>
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=fira-code:300,400,500,600,700" rel="stylesheet" />
	<style>
	[x-cloak] {
		display: none !important;
	}
	</style>
	@vite('resources/css/app.css')
	@vite('resources/js/app.js')
</head>
<body class="flex min-h-full font-sans">
<div {{ $attributes->merge(['class' => 'flex w-full flex-col bg-dots']) }}>
	
	{{-- Content --}}
	<div class="w-full max-w-4xl h-full mx-auto flex flex-col items-center justify-center px-4 py-8">
		<x-phpx-dropdown />
	</div>
	
</div>
</body>
</html>
