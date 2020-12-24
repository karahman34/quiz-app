<x-app-layout>
  {{-- Title --}}
  <x-slot name="title">
      {{ $packet->title }}
  </x-slot>
  
  {{-- The Packet Page --}}
  <packet-page :initial-packet="{{ json_encode($packet) }}"></packet-page>
</x-app-layout>