<x-app-layout>
  {{-- The Packet Page --}}
  <packet-page :initial-packet="{{ json_encode($packet) }}"></packet-page>
</x-app-layout>