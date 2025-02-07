<div class="flex justify-center">
	<div class="relative">
		<a
			href="/"
			class="flex items-center gap-2 bg-black border-2 border-white text-white font-mono font-bold"
		>
			<span class="px-2.5 py-1.5 pr-1">PHP<span class="inline-block ml-0.5">×</span></span>
			<span class="border-l-2 border-gray-200 bg-white text-black px-2.5 py-1.5 flex items-center">
				@isset($group)
					{{ str($group->name)->after('×') }}
				@else
					World
				@endisset
			</span>
		</a>
	</div>
</div>
