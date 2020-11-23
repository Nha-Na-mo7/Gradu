<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ページのタイトル -->
    <title>@yield('title')</title>

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
    <!-- ヘッダー -->
    @yield('header')

    <main class="l-container">
        @yield('content')
    </main>

    <!-- フラッシュメッセージ -->
    <!-- 呼び出すときは、flash_messageが存在しているかを確認する。 -->
    <!-- session() を使うことで、SESSIONの中身を取り出すことができる -->
    @if (session('flash_message'))
        <div class="" role="alert">
            {{ session('flash_message') }}
        </div>
    @endif

</body>
</html>
