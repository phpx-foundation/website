<x-layout>
	
	<x-slot:og>
		<meta property="og:url" content="{{ url()->current() }}" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="PHP×" />
		<meta property="og:image" content="{{ asset('world/og.jpg') }}" />
	</x-slot:og>
	
	<div id="globe-visualization" class="w-full h-96" data-points="{{ json_encode($points) }}"></div>
	
	<x-markdown class="my-12" :file="base_path('README.md')" sidebar />
	
	@vite('resources/js/globe.js')
	
</x-layout>
