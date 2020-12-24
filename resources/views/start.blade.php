<x-app-layout>
  {{-- Title --}}
  <x-slot name="title">
      Good Luck
  </x-slot>
  
  <start-page :session="{{ json_encode($session) }}"></start-page>
</x-app-layout>