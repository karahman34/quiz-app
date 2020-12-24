<div class="py-10 sticky top-16">
  {{-- Dashboard --}}
  <x-left-navigation.link text="Dashboard" icon="home" :url="route('dashboard')" :active="request()->routeIs('dashboard')"></x-left-navigation.link>

  {{-- Join --}}
  <x-left-navigation.link text="Join Session" icon="share" :url="route('session.view.join')" :active="request()->routeIs('session.view.join')"></x-left-navigation.link>

  {{-- Logout --}}
  <x-left-navigation.link class="logout-button" text="Logout" icon="logout"></x-left-navigation.link>
</div>