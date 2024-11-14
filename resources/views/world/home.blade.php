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
	
	<meta property="og:url" content="{{ url()->current() }}" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="PHP×" />
	<meta property="og:image" content="{{ asset('world/og.jpg') }}" />

</head>
<body class="flex min-h-full font-sans">
<div {{ $attributes->merge(['class' => 'flex w-full flex-col bg-dots']) }}>
	
	{{-- Content --}}
	<div class="w-full max-w-4xl mx-auto flex flex-col items-start justify-center px-4 py-8">
		<x-phpx-dropdown />
		<div class="flex items-start justify-start">
			<article class="max-w-none flex-shrink mb-64">
				<x-markdown class="my-12" :file="resource_path('markdown/world.md')" />
			</article>
			<aside
				x-data="onThisPage"
				x-on:scroll.window.throttle.50ms="onScroll()"
				x-show="headings.length > 1"
				class="hidden top-4 w-64 flex-shrink-0 min-h-0 sticky overflow-y-auto py-8 pl-6 lg:block"
			>
				<h4 class="mb-2 block text-sm font-bold uppercase opacity-70 text-white">
					On this page
				</h4>
				
				<ul>
					<template x-for="heading in headings">
						<li
							class="text-sm"
							:class="{
								'mt-3': heading.level === 2 || heading.level === 1,
								'pl-2': heading.level === 3,
								'pl-4': heading.level === 4,
								'pl-6': heading.level === 5,
								'pl-8': heading.level === 6
							}"
						>
							<a
								:href="`#${heading.permalink}`"
								class="text-white hover:opacity-90"
								:class="{ 
									'font-medium opacity-70': active_permalink === heading.permalink, 
									'opacity-50': active_permalink !== heading.permalink,
								}"
								x-text="heading.title"
							></a>
						</li>
					</template>
				</ul>
			</aside>
		</div>
	</div>

</div>
</body>
</html>
