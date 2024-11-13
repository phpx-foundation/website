<x-layout title="Log in">
	
	<h1 class="font-mono font-semibold text-white text-2xl sm:text-4xl md:text-5xl lg:text-6xl">
		Login
	</h1>
	
	<x-flash-message />
	
	<form action="{{ url('login') }}" method="post" class="w-full max-w-md transform -rotate-1 ml-8">
		
		@csrf
		
		<div class="flex flex-col gap-4 mt-10">
			<x-input name="email" label="Email" type="email" placeholder="you@phpxphilly.com" />
			<x-input name="password" label="Password" type="password" />
		</div>
		
		<div class="mt-5 flex flex-col gap-2">
			<label class="font-mono text-lg text-white font-semibold">
				<input type="checkbox" name="remember" value="1" />
				Keep me logged in
			</label>
		</div>
		
		<div class="mt-3">
			<button class="bg-white px-3 py-1.5 text-black font-semibold transform opacity-90 hover:opacity-100 focus:opacity-100">
				Login
			</button>
		</div>
	</form>

</x-layout>
