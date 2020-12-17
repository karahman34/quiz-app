@props(['message'])

<small {{ $attributes->merge(['class' => 'block mt-1 text-red-500']) }}>
  {{ $message }}
</small>