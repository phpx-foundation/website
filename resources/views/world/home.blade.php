<x-layout scripts="resources/js/globe.js">
	
	<x-slot:og>
		<meta property="og:url" content="{{ url()->current() }}" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="PHP×" />
		<meta property="og:image" content="{{ asset('world/og.jpg') }}" />
	</x-slot:og>
	
	<x-slot:before>
		<div id="globe-visualization" class="w-full h-32 -mb-4 sm:h-40 md:h-56 lg:h-96" data-points="{{ json_encode($points) }}"></div>
		@env('local')
			<div id="debug" class="absolute top-4 right-4 bg-red-800 text-white p-4 rounded"></div>
		@endenv
	</x-slot:before>
	
	<x-markdown :file="base_path('README.md')" sidebar />
	
	<p>
		<a href="/terms">Terms of Use</a>
	</p>

</x-layout>
