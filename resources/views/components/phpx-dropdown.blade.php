<ui-dropdown position="bottom start" data-flux-dropdown>
	{{-- Button --}}
	<button class="flex items-center gap-2 bg-black border-2 border-white text-white font-mono font-bold">
		<span class="px-2.5 py-1.5 pr-1">PHP<span class="inline-block ml-0.5">×</span></span>
		<span class="border-l-2 border-gray-200 bg-white text-black px-2.5 py-1.5 flex items-center">
				{{ isset($group) ? str($group->name)->after('×') : 'World' }}
				<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black" viewBox="0 0 20 20" fill="currentColor">
	                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
	            </svg>
			</span>
	</button>
	
	{{-- Panel --}}
	<ui-menu popover="manual" class="bg-transparent m-0 p-0">
		<div class="bg-transparent relative">
			<div class="max-w-4xl mx-auto border-2 border-white bg-black text-white font-mono font-bold shadow-sharp">
				<div class="grid grid-cols-2 gap-0.5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
					@foreach($phpx_network->groupBy('continent')->sortByDesc(fn($c) => $c->count()) as $continent)
						<h3 class="px-4 col-span-full bg-white text-black text-xs outline outline-2 outline-white">
							{{ $continent->first()->continent }}
						</h3>
						@foreach($continent->sortBy('label') as $network_group)
							<a
								href="{{ isset($group) && $network_group->domain === $group->domain ? url('/') : "https://{$network_group->domain}/" }}"
								@class([
									'flex items-center justify-center gap-2 w-full px-4 py-2.5 text-right text-sm outline outline-2 outline-white',
									'focus:bg-white focus:text-black',
									'bg-black text-white hover:bg-white hover:text-black' => ! isset($group) || $network_group->domain !== $group->domain,
									'bg-white/50 text-black cursor-default' => isset($group) && $network_group->domain === $group->domain,
								])
								data-flux-menu-item
							>
								{{ str($network_group->label)->after('×') }}
								@if(isset($group) && $network_group->domain === $group->domain)
									{{--
									<svg class="w-5 h-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
										<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
									</svg>
									--}}
								@else
									{{--
									<svg class="w-5 h-5 fill-current ml-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
										<path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
									</svg>
									--}}
								@endif
							</a>
						@endforeach
					@endforeach
				</div>
			</div>
		</div>
	</ui-menu>
</ui-dropdown>
