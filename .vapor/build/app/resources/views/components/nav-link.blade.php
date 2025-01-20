@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 rounded-md border-b-2 border-purple-500 bg-purple-50 text-sm font-medium leading-5 text-purple-700 focus:outline-none focus:border-purple-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-2 rounded-md border-b-2 border-transparent text-sm font-medium leading-5 text-gray-600 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
