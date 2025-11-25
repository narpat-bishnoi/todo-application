<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'TODO Application')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div x-data="{ show: true }">
        @include('partials.nav')
        
        @include('partials.flash')

        <main class="container mx-auto px-4 py-8">
            @yield('content')
        </main>
    </div>
</body>
</html>

