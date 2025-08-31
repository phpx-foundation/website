@props(['group'])

@if($group->hasSponsorship())
	<div class="w-full mt-8">
		<h1 class="font-mono font-semibold text-white text-2xl mb-4">
			Sponsor Our Meetup
		</h3>
		
		@if($group->sponsorship_description)
			<p class="text-gray-300 mb-6">{{ $group->sponsorship_description }}</p>
		@endif
		
		<div class="space-y-4">
			@foreach($group->sponsorship_packages as $package)
				<div class="bg-white/5 rounded-lg p-4 border border-white/5">
					<div class="flex justify-between items-start mb-3">
						<h4 class="text-lg font-semibold text-white">{{ $package['name'] }}</h4>
						<span class="text-xl font-bold text-white">
							{{ strtoupper($package['currency']) }} ${{ number_format($package['amount']) }}
						</span>
					</div>
					
					@if(isset($package['benefits']) && count($package['benefits']) > 0)
						<ul class="text-sm text-gray-300 space-y-1">
							@foreach($package['benefits'] as $benefit)
								<li class="flex items-start gap-2">
									<svg class="w-4 h-4 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
										<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
									</svg>
									{{ $benefit }}
								</li>
							@endforeach
						</ul>
					@endif
				</div>
			@endforeach
		</div>
		
		@if($group->sponsorship_contact_email)
			<div class="mt-6 pt-6 border-t border-white/10">
				<div class="flex items-center gap-3">
					<svg class="w-6 h-6 text-white opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
					</svg>
					<div>
						<p class="text-sm text-gray-300">Interested in sponsoring?</p>
						<a href="mailto:{{ $group->sponsorship_contact_email }}" class="text-white hover:underline font-semibold">
							{{ $group->sponsorship_contact_email }}
						</a>
					</div>
				</div>
			</div>
		@endif
	</div>
@endif
