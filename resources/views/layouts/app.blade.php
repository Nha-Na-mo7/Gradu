<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ページのタイトル -->
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
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    {{-- flash message --}}
    {{-- メッセージが消える処理・タッチすると消える処理はjQueryなどで処理すること--}}
    {{-- https://qiita.com/usaginooheso/items/6a99e565f16de2f9ddf7 --}}
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
