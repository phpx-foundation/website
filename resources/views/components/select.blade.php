@props(['name', 'label', 'id' => null])

<div>
	<label class="block font-mono font-bold text-white" for="{{ $name }}">
		{{ $label }}
	</label>
	
	<select {{ $attributes->merge([
	'class' => 'font-mono text-black p-2 font-semibold w-full',
	'type' => 'text',
	'name' => $name,
	'id' => $id ?? $name,
]) }}>
		{{ $slot }}
	</select>
	
	@error($name)
	<div class="bg-red-50 mt-1 mb-3 p-2 border-l-8 border-red-400 font-mono font-bold w-full flex items-center gap-2 transform text-red-600">
		<svg class="w-5 h-5 fill-red-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
			<path d="M64 80c-8.8 0-16 7.2-16 16V416c0 8.8 7.2 16 16 16H384c8.8 0 16-7.2 16-16V96c0-8.8-7.2-16-16-16H64zM0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm224 32c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM192 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
		</svg>
		{{ $message }}
	</div>
	@enderror
</div>
