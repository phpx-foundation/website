<x-layout title="Create a meetup">
	
	<h1 class="font-mono font-semibold text-white text-2xl sm:text-4xl md:text-5xl lg:text-6xl">
		Create a meetup
	</h1>
	
	<x-flash-message />
	
	<form action="{{ url('meetups') }}" method="post" class="w-full max-w-md transform -rotate-1 ml-8">
		
		@csrf
		
		<div class="flex flex-col gap-4">
			
			<div>
				@isset($group)
					<input type="hidden" name="group_id" value="{{ $group->getKey() }}" />
				@else
					<x-select name="group_id" label="Group">
						@foreach($groups as $group)
							<option value="{{ $group->getKey() }}">
								{{ $group->name }} ({{ $group->domain }})
							</option>
						@endforeach
					</x-select>
				@endisset
			</div>
			
			<x-input name="description" label="Description (markdown allowed)" type="textarea" />
			<x-input name="location" label="Location" />
			<x-input name="capacity" label="Capacity" type="number" default="25" />
			<x-input name="starts_at" label="Starts at" type="datetime-local" />
			<x-input name="ends_at" label="Ends at" type="datetime-local" />
		
		</div>
		
		<div class="mt-3">
			<button class="bg-white px-3 py-1.5 text-black font-semibold transform opacity-90 hover:opacity-100 focus:opacity-100">
				Save
			</button>
		</div>
	</form>

</x-layout>
