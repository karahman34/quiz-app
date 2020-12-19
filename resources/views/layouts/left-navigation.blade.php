<div class="min-h-screen sticky top-28 border-r-2">
  {{-- Dashboard --}}
  <x-left-navigation.link text="Dashboard" icon="home" :url="route('dashboard')" :active="request()->routeIs('dashboard')"></x-left-navigation.link>

  {{-- Activities --}}
  <x-left-navigation.link text="Activities" icon="history" url="/" :active="false"></x-left-navigation.link>

  {{-- Logout --}}
  <x-left-navigation.link class="logout-button" text="Logout" icon="logout"></x-left-navigation.link>
</div>