<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- タイトル -->
    <title>@yield('title') | CryptoTrend</title>

    <!-- デスクリプション -->
    <meta name="description" itemprop="description" content="@yield('description')">
    <!-- キーワード -->
    <meta name="keyword" itemprop="keyword" content="@yield('keyword')">


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    {{-- flash message --}}
    @if(Session::has('system_message'))
        <div class="c-flash js-flash-system-message" role="alert">
            <p>{{ session('system_message') }}</p>
        </div>
    @endif

    <!-- header -->
    @include('layouts.header')

    <main class="l-container">
        @yield('content')
    </main>

    <footer id="footer" class="l-footer">
        @include('layouts.footer')
    </footer>

</body>
</html>
