<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        {{-- Icons --}}
        <link rel="stylesheet" href="{{ asset('css/icons.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        {{-- Form Logout --}}
        <form id="logout-form" action="{{ route('logout') }}" method="post">
            @csrf
        </form>
        
        <div class="bg-gray-100 min-h-screen">
            {{-- Navigation --}}
            <div class="fixed w-full z-50">
                @include('layouts.navigation')
            </div>

            <!-- Page Content -->
            <main class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid gap-4 grid-cols-12 pt-12">
                    {{-- Left Navigation --}}
                    <div class="hidden col-span-0 md:block md:col-span-3">
                        <div class="min-h-full">
                            @include('layouts.left-navigation')
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="col-span-12 md:col-span-9">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
