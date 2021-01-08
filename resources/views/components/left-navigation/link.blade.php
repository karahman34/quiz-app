@props(['url', 'text', 'icon', 'active'])

@php
    $classes = ($active ?? false)
                ? 'px-14 bg-indigo-100 py-2 flex justify-center cursor-pointer text-lg font-medium text-indigo-700 hover:text-indigo-900'
                : 'px-14 py-2 flex justify-center cursor-pointer text-lg font-medium text-gray-500 hover:text-gray-800';
@endphp

<a 
  @isset($url) href="{{ $url }}" @endisset 
  {{ $attributes->merge(['class' => $classes]) }}
>
  <span class="mdi mdi-{{ $icon }}"></span>
  <span class="ml-2">{{ $text }}</span>
</a>