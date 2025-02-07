@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge([
	'class' => 'text-black border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-black dark:focus:border-indigo-600 focus:ring-black dark:focus:ring-indigo-600 rounded-md shadow-sm'
]) }}>
