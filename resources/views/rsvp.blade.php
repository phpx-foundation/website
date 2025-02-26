<x-layout title="RSVP">
	
	<x-slot:og>
		<meta property="og:url" content="{{ url()->current() }}" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="Meetup @ {{ $meetup->location }} - {{ $group->name }}" />
		<meta name="twitter:card" content="summary_large_image" />
		<meta property="twitter:domain" content="{{ parse_url(url()->current(), PHP_URL_HOST) }}" />
		<meta property="twitter:url" content="{{ url()->current() }}" />
		<meta name="twitter:title" content="Meetup @ {{ $meetup->location }} - {{ $group->name }}" />
		<meta name="twitter:creator" content="{{ str($group->twitter_url)->after('twitter.com/')->prepend('@') }}" />
		<meta name="description" content="Meetup @ {{ $meetup->location }} on {{ $meetup->range() }}" />
		<meta property="og:description" content="Meetup @ {{ $meetup->location }} on {{ $meetup->range() }}" />
		<meta name="twitter:description" content="Meetup @ {{ $meetup->location }} on {{ $meetup->range() }}" />
		@if($meetup->open_graph_image_url)
			<meta property="og:image" content="{{ $meetup->open_graph_image_url }}" />
			<meta name="twitter:image" content="{{ $meetup->open_graph_image_url }}" />
		@endif
	</x-slot:og>
	
	<h1 class="font-mono font-semibold text-white text-2xl sm:text-4xl md:text-5xl lg:text-6xl">
		Meetup @ {{ $meetup->location }}
	</h1>
	
	<p class="mt-1 font-semibold">
		{{ $meetup->range() }}
	</p>
	
	<x-flash-message />
	
	{{-- Show the capacity message only if we don't have a flash message --}}
	@if(session()->has('message'))
	@elseif($meetup->remaining() === 1)
		<x-alert message="There is only 1 spot left!" />
	@elseif($meetup->remaining() > 0 && $meetup->remaining() < 10)
		<x-alert message="There are only {{ $meetup->remaining() }} spots left!" />
	@elseif(! $meetup->remaining())
		<x-alert message="We are at capacity, so if you register now you will be waitlisted…" />
	@endunless
	
	<div class="prose prose-invert my-8 text-xl max-w-3xl">
		{{ $meetup }}
	</div>
	
	<form
		action="/meetups/{{ $meetup->getKey() }}/rsvps"
		method="post"
		class="w-full transform -rotate-1 md:ml-8"
	>
		@csrf
		
		<div class="max-w-md">
			<x-input name="name" label="Your Name" />
		</div>
		
		<div class="mt-5 max-w-md">
			<x-input name="email" label="Email" type="email" :placeholder="'you@'.$group->domain" />
		</div>
		
		<div class="mt-5 flex flex-col gap-2">
			<label class="font-mono text-lg text-white font-semibold">
				<input type="checkbox" name="speaker" value="1" />
				I'm interested in speaking
			</label>
			<label class="font-mono text-lg text-white font-semibold">
				<input type="checkbox" name="subscribe" value="1" checked />
				Send me updates
			</label>
		</div>
		
		<x-turnstile class="mt-5" />
		
		<div class="mt-5">
			<button class="bg-white px-3 py-1.5 text-black font-semibold transform opacity-90 hover:opacity-100 focus:opacity-100">
				RSVP
			</button>
		</div>
	</form>

</x-layout>
