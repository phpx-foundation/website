<x-layout class="justify-between">
	<div class="flex flex-col gap-4">
		<h1 class="font-mono font-semibold text-white text-4xl sm:text-6xl md:text-7xl lg:text-8xl">
			PHP<span x-data x-typed="['×{{ $group->label() }}']"></span>
		</h1>
		<h2 class="font-semibold">
			A bi-monthly<span x-data x-typed.cursorless.delay.3000="['(ish)']"></span> meetup for PHP artisans.
		</h2>
		<div class="flex gap-4 items-center">
			@isset($next_meetup)
				<a href="{{ url("/meetups/{$next_meetup->getKey()}/rsvps") }}" class="bg-white px-4 py-2.5 text-black font-semibold text-lg transform opacity-90 hover:opacity-100 hover:-rotate-2">
					RSVP<span class="hidden sm:inline"> for {{ $next_meetup->starts_at->format('M jS') }}</span><span class="not-sr-only">_</span>
				</a>
				<span class="w-2"></span>
			@endisset
			<a href="{{ url('join') }}" class="group relative">
				<div class="absolute bg-white whitespace-nowrap px-2 py-1 text-black font-mono font-bold -top-full -left-2 transform transition-all ease-out duration-100 opacity-0 rotate-6 pointer-events-none translate-y-3 group-hover:-rotate-3 group-hover:block group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto">
					Get Updates
				</div>
				<svg class="fill-white w-10 h-10 opacity-50 transform group-hover:opacity-90 group-hover:-rotate-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
					<path d="M215.4 96H144 107.8 96v8.8V144v40.4 89L.2 202.5c1.6-18.1 10.9-34.9 25.7-45.8L48 140.3V96c0-26.5 21.5-48 48-48h76.6l49.9-36.9C232.2 3.9 243.9 0 256 0s23.8 3.9 33.5 11L339.4 48H416c26.5 0 48 21.5 48 48v44.3l22.1 16.4c14.8 10.9 24.1 27.7 25.7 45.8L416 273.4v-89V144 104.8 96H404.2 368 296.6 215.4zM0 448V242.1L217.6 403.3c11.1 8.2 24.6 12.7 38.4 12.7s27.3-4.4 38.4-12.7L512 242.1V448v0c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64v0zM176 160H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16z" />
				</svg>
			</a>
			@if($group->bsky_url)
				<a href="{{ $group->bsky_url }}" class="group relative" target="_blank">
					<div class="absolute bg-white whitespace-nowrap px-2 py-1 text-black font-mono font-bold -top-full -left-2 transform transition-all ease-out duration-100 opacity-0 rotate-6 pointer-events-none translate-y-3 group-hover:-rotate-3 group-hover:block group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto">
						Bluesky
					</div>
					<x-icon.bsky class="fill-white w-10 h-10 opacity-50 transform group-hover:opacity-90 group-hover:-rotate-2" />
				</a>
			@endif
			@if($group->meetup_url)
				<a href="{{ $group->meetup_url }}" class="group relative" target="_blank">
					<div class="absolute bg-white whitespace-nowrap px-2 py-1 text-black font-mono font-bold -top-full -left-2 transform transition-all ease-out duration-100 opacity-0 rotate-6 pointer-events-none translate-y-3 group-hover:-rotate-3 group-hover:block group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto">
						Meetup.com
					</div>
					<x-icon.meetup class="fill-white w-10 h-10 opacity-50 transform group-hover:opacity-90 group-hover:-rotate-2" />
				</a>
			@endif
			<a href="https://discord.gg/wMy6Eeuwbu" class="group relative" target="_blank">
				<div class="absolute bg-white whitespace-nowrap px-2 py-1 text-black font-mono font-bold -top-full -left-2 transform transition-all ease-out duration-100 opacity-0 rotate-6 pointer-events-none translate-y-3 group-hover:-rotate-3 group-hover:block group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto">
					Discord
				</div>
				<svg class="fill-white w-10 h-10 opacity-50 transform group-hover:opacity-90 group-hover:-rotate-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
					<path d="M524.5 69.8a1.5 1.5 0 0 0 -.8-.7A485.1 485.1 0 0 0 404.1 32a1.8 1.8 0 0 0 -1.9 .9 337.5 337.5 0 0 0 -14.9 30.6 447.8 447.8 0 0 0 -134.4 0 309.5 309.5 0 0 0 -15.1-30.6 1.9 1.9 0 0 0 -1.9-.9A483.7 483.7 0 0 0 116.1 69.1a1.7 1.7 0 0 0 -.8 .7C39.1 183.7 18.2 294.7 28.4 404.4a2 2 0 0 0 .8 1.4A487.7 487.7 0 0 0 176 479.9a1.9 1.9 0 0 0 2.1-.7A348.2 348.2 0 0 0 208.1 430.4a1.9 1.9 0 0 0 -1-2.6 321.2 321.2 0 0 1 -45.9-21.9 1.9 1.9 0 0 1 -.2-3.1c3.1-2.3 6.2-4.7 9.1-7.1a1.8 1.8 0 0 1 1.9-.3c96.2 43.9 200.4 43.9 295.5 0a1.8 1.8 0 0 1 1.9 .2c2.9 2.4 6 4.9 9.1 7.2a1.9 1.9 0 0 1 -.2 3.1 301.4 301.4 0 0 1 -45.9 21.8 1.9 1.9 0 0 0 -1 2.6 391.1 391.1 0 0 0 30 48.8 1.9 1.9 0 0 0 2.1 .7A486 486 0 0 0 610.7 405.7a1.9 1.9 0 0 0 .8-1.4C623.7 277.6 590.9 167.5 524.5 69.8zM222.5 337.6c-29 0-52.8-26.6-52.8-59.2S193.1 219.1 222.5 219.1c29.7 0 53.3 26.8 52.8 59.2C275.3 311 251.9 337.6 222.5 337.6zm195.4 0c-29 0-52.8-26.6-52.8-59.2S388.4 219.1 417.9 219.1c29.7 0 53.3 26.8 52.8 59.2C470.7 311 447.5 337.6 417.9 337.6z" />
				</svg>
			</a>
		</div>
	</div>
	
	@isset($next_meetup)
		<x-slot:footer>
			<a href="{{ url("/meetups/{$next_meetup->getKey()}/rsvps") }}" class="group flex items-center gap-2 p-4 text-center justify-center xl:text-xl">
				<svg class="w-12 h-12 transform group-hover:-rotate-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"
					/>
				</svg>
				<div class="text-left">
					<div class="group-hover:underline">
						Join us for our next meetup @ <strong>{{ $next_meetup->location }}</strong>
					</div>
					<div class="text-sm opacity-70">
						{{ $next_meetup->range() }}
					</div>
				</div>
			</a>
		</x-slot:footer>
	@endisset
</x-layout>
