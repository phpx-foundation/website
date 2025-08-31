<x-layout>
	
	<x-slot:og>
		<meta property="og:url" content="{{ url()->current() }}" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="Sponsorships - PHP×" />
		<meta property="og:description" content="Support PHP and Laravel meetups worldwide. Learn how to sponsor groups or add sponsorship options to your group." />
		<meta property="og:image" content="{{ asset('world/og.jpg') }}" />
	</x-slot:og>
	
	<div class="max-w-4xl mx-auto">
		
		<div class="flex flex-col gap-6">
			<h1 class="font-mono font-semibold text-white text-4xl sm:text-6xl">
				Sponsorships
			</h1>
			
			<p class="text-gray-300 text-lg">
				PHP× connects sponsors with local PHP and Laravel meetups worldwide. Whether you want to support the community or add sponsorship options to your group, this page has everything you need.
			</p>
		</div>
		
		<!-- For Sponsors Section -->
		<section class="mt-12">
			<h2 class="font-mono font-semibold text-white text-3xl mb-6">
				For Sponsors
			</h2>
			
			<div class="space-y-6 text-gray-300">
				<p class="text-lg">
					Looking to support the PHP and Laravel community? Our meetups are always looking for sponsors to help make events happen and grow the community.
				</p>
				
				<div class="bg-white/5 rounded-lg p-6 border border-white/5">
					<h3 class="font-semibold text-white text-xl mb-4">Why Sponsor PHP× Groups?</h3>
					<ul class="space-y-2">
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-green-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							<span><strong>Connect with developers:</strong> Reach passionate PHP and Laravel developers in local communities</span>
						</li>
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-green-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							<span><strong>Build brand awareness:</strong> Get your company name in front of engaged technical audiences</span>
						</li>
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-green-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							<span><strong>Support the community:</strong> Help maintain and grow the PHP ecosystem globally</span>
						</li>
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-green-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							<span><strong>Talent pipeline:</strong> Meet potential hires in a relaxed, community setting</span>
						</li>
					</ul>
				</div>
				
				<div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-6">
					<h3 class="font-semibold text-blue-300 text-xl mb-3">Get Connected</h3>
					<p class="text-blue-100 mb-4">
						The best way to connect with groups is through our Discord community, where organizers and sponsors can meet and discuss opportunities.
					</p>
					<a href="https://discord.gg/wMy6Eeuwbu" target="_blank" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
						<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
							<path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418Z"/>
						</svg>
						Join PHP× Discord
					</a>
				</div>
			</div>
		</section>

		<!-- Groups Looking for Sponsors -->
		@if($groupsWithSponsorship->count() > 0)
			<section class="mt-12">
				<h2 class="font-mono font-semibold text-white text-3xl mb-6">
					Groups Looking for Sponsors
				</h2>
				
				<p class="text-gray-300 mb-8">
					The following PHP× groups are actively seeking sponsorship. Click on any group to visit their page and see their specific sponsorship packages.
				</p>
				
				<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
					@foreach($groupsWithSponsorship as $group)
						<div class="bg-white/5 rounded-lg p-6 border border-white/5 hover:bg-white/10 transition-colors">
							<div class="flex items-start justify-between mb-4">
								<h3 class="font-semibold text-white text-lg">{{ $group->name }}</h3>
								@if($group->region)
									<span class="text-sm text-gray-400 bg-gray-700 px-2 py-1 rounded">
										{{ $group->region }}
									</span>
								@endif
							</div>
							
							@if($group->description)
								<p class="text-gray-300 text-sm mb-4 line-clamp-3">
									{{ $group->description }}
								</p>
							@endif
							
							<div class="space-y-3">
								<!-- Package Preview -->
								@if(count($group->sponsorship_packages) > 0)
									<div class="border-t border-white/10 pt-3">
										<p class="text-xs text-gray-400 mb-2">Available packages:</p>
										@foreach($group->sponsorship_packages as $package)
											<div class="text-sm text-gray-300">
												<span class="font-medium text-white">{{ $package['name'] }}</span>
												<span class="text-gray-400">
													- {{ strtoupper($package['currency']) }} ${{ number_format($package['amount']) }}
												</span>
											</div>
										@endforeach
									</div>
								@endif
								
								<!-- Contact & Visit -->
								<div class="flex flex-col gap-2 pt-3 border-t border-white/10">
									@if($group->sponsorship_contact_email)
										<a href="mailto:{{ $group->sponsorship_contact_email }}" 
										   class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
											Contact: {{ $group->sponsorship_contact_email }}
										</a>
									@endif
									<a href="https://{{ $group->domain }}" 
									   class="text-sm text-green-400 hover:text-green-300 transition-colors">
										Visit group page →
									</a>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</section>
		@endif

		<!-- For Group Organizers Section -->
		<section class="mt-16">
			<h2 class="font-mono font-semibold text-white text-3xl mb-6">
				For Group Organizers
			</h2>
			
			<div class="space-y-8 text-gray-300">
				
				<div class="bg-white/5 rounded-lg p-6 border border-white/5">
					<h3 class="font-semibold text-white text-xl mb-4">Adding Sponsorship to Your Group</h3>
					<p class="mb-4">
						You can now add sponsorship information to your group's page! This feature allows you to:
					</p>
					<ul class="space-y-2 mb-6">
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-green-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							<span>Define multiple sponsorship packages with different price tiers</span>
						</li>
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-green-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							<span>List specific benefits for each sponsorship level</span>
						</li>
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-green-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							<span>Display on your group page and in the PHP× network</span>
						</li>
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-green-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							<span>Provide easy contact information for potential sponsors</span>
						</li>
					</ul>
					
					<div class="bg-gray-900 rounded-lg p-4">
						<p class="text-sm font-semibold text-white mb-2">Add to your groups.json:</p>
						<pre class="text-sm text-green-300 overflow-x-auto"><code>"sponsorships": {
  "enabled": true,
  "description": "Support our local PHP community",
  "contact_email": "sponsors@yourgroup.com",
  "packages": [
    {
      "name": "Gold Sponsor",
      "currency": "USD",
      "amount": 500,
      "benefits": [
        "Logo on all event materials",
        "5-minute speaking slot",
        "Social media mention",
        "Newsletter inclusion"
      ]
    },
    {
      "name": "Silver Sponsor",
      "currency": "USD",
      "amount": 250,
      "benefits": [
        "Logo on event materials",
        "Social media mention"
      ]
    }
  ]
}</code></pre>
					</div>
				</div>

				<div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-6">
					<h3 class="font-semibold text-yellow-300 text-xl mb-4">Best Practices for Getting Sponsors</h3>
					
					<div class="space-y-4">
						<div>
							<h4 class="font-semibold text-yellow-200 mb-2">Keep It Simple (KISS)</h4>
							<p class="text-yellow-100">
								Sponsors want simplicity. Make it easy for them—the easier it is, the more likely a sponsorship will succeed. 
								Don't enforce complex logistics or complicate things by having sponsors for specific things (unless they specifically ask).
							</p>
						</div>
						
						<div>
							<h4 class="font-semibold text-yellow-200 mb-2">Set Clear Rates</h4>
							<p class="text-yellow-100">
								Set a simple sponsorship rate for all sponsors. If you can do it in an international currency (e.g. USD), even better.
							</p>
						</div>
						
						<div>
							<h4 class="font-semibold text-yellow-200 mb-2">Core Proposition</h4>
							<p class="text-yellow-100 mb-2">Have a core proposition (but be prepared to be flexible):</p>
							<ul class="space-y-1 text-yellow-100 text-sm">
								<li>• Shout outs on social media before, during and after the event</li>
								<li>• Mentions in the next group newsletter</li>
								<li>• Logos on screens (physical and digital) at the event</li>
								<li>• Possible swag items (if you don't mind the logistics)</li>
								<li>• MC thanks sponsors by name during the event</li>
								<li>• Possible short sponsor speaking slot</li>
							</ul>
						</div>
						
						<div>
							<h4 class="font-semibold text-yellow-200 mb-2">Professional Invoicing</h4>
							<p class="text-yellow-100 mb-2">Prepare proper invoices with all necessary details:</p>
							<ul class="space-y-1 text-yellow-100 text-sm">
								<li>• Date of invoice and due date</li>
								<li>• Currency and amount to be paid</li>
								<li>• Your address and their address</li>
								<li>• A unique invoice number</li>
								<li>• Your payment details (Wise is a good option)</li>
							</ul>
						</div>
						
						<div class="pt-4 border-t border-yellow-500/20">
							<p class="font-semibold text-yellow-200">
								Remember: The keys to successful sponsor relations are good communication and effective execution. 
								Be clear about expectations and deliver what you promised.
							</p>
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<div class="mt-16 pt-8 border-t border-white/10">
			<p class="text-center text-gray-400">
				<a href="/" class="hover:text-white transition-colors">← Back to PHP× World</a>
			</p>
		</div>
	
	</div>

</x-layout>