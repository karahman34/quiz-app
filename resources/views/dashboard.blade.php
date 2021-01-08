<x-app-layout>
    {{-- Title --}}
    <x-slot name="title">
        Dashboard
    </x-slot>
    
    {{-- Session Success --}}
    @if (session('session.success') === true)
        <div class="bg-green-500 py-2 px-3 rounded text-white mb-2">
            <span class="inline-block mr-1 mdi mdi-check-circle"></span>
            Thanks for participating to the quiz.
        </div>
    @endif

    {{-- User on session --}}
    @if (session('session.working') === true)
        <div class="bg-yellow-500 py-2 px-3 rounded text-white mb-2">
            <span class="inline-block mr-1 mdi mdi-information"></span>
            You are currently in a session.
            <a class="underline font-medium" href="{{ route('session.start', ['session' => session('session.code')]) }}">Reconnect</a>
        </div>
    @endif

    {{-- The Vue Page --}}
    <dashboard-page></dashboard-page>
</x-app-layout>
