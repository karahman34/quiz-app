<x-app-layout>
  {{-- The Packet Page --}}
  <packet-page :packet="{{ json_encode($packet) }}"></packet-page>
</x-app-layout>