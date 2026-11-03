<x-layout title="Get Updates">
	
	<h1 class="font-mono font-semibold text-white text-2xl sm:text-4xl md:text-5xl lg:text-6xl">
		<span x-data x-typed="['{{ $group->name }} Newsl', '{{ $group->name }} Updates']">{{ $group->name }}</span>
	</h1>
	
	<x-flash-message />
	
	<p class="my-8 text-xl max-w-3xl">
		Don’t call it a newsletter. We’ll only ever email you about upcoming events or calls for speakers. Promise.
	</p>
	
	<x-join-form />

</x-layout>
