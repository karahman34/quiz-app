<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @isset($title)
                {{ $title }} - {{ config('app.name', 'Laravel') }}
            @else
                {{ config('app.name', 'Laravel') }}
            @endisset
        </title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        {{-- Additional --}}
        <link rel="stylesheet" href="{{ asset('css/additional.css') }}">

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
            <div class="sticky top-0 z-50">
                @include('layouts.navigation')
            </div>

            <!-- Page Content -->
            <main id="app" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex gap-10 relative">
                    {{-- Left Navigation --}}
                    <div class="flex-shrink-0 min-h-screen hidden md:block">
                        <div class="h-full border-r-2">
                            @include('layouts.left-navigation')
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="py-6 w-full md:py-10">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
