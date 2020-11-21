<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->

    <script src="{{ asset('js/bundle.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Merriweather|Roboto:400">
    <link rel="stylesheet" href="https://unpkg.com/ionicons@4.2.2/dist/css/ionicons.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/sample.css') }}">
</head>
<body>
@if(session('flash_message'))
    <div class="flash_message">
        {{ session('flash_message') }}
    </div>
@endif

<div id="app">

</div>

</body>

</html>