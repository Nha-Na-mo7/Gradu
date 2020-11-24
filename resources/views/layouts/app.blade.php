<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
    <!-- ヘッダー -->
    <header class="l-header">
        <div class="p-navbar">
            <div class="p-header__logo-area">
                <a class="p-header lololink" href="{{ route('home.index') }}">
                    <img class="p-header__logo" src="" alt="CryptoTrend" />
                    {{--                <img class="p-header__logo" src="{{ asset('images/header_logo.png') }}" alt="CryptoTrend" />--}}
                </a>
            </div>

            <!-- SPサイト用メニュー -->
            <div id="" class="p-header__burger">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <!-- ナビバー -->
            <nav id="" class="p-header p-header__nav p-header__nav__sp   p-navbar__menu">
                <ul class="p-header__list">
                    @guest
                        <li class="p-header__item">
                            <a class="p-header__item--link" href="{{ route('login') }}">ログイン</a>
                        </li>
                        <li class="p-header__item">
                            <a class="p-header__item--link" href="{{ route('register') }}">新規登録</a>
                        </li>
                    @else
                        <li class="p-header__item">
                            <a class="p-header__item--link" href="{{ route('trend.index') }}">トレンドランキング</a>
                        </li>
                        <li class="p-header__item">
                            <a class="p-header__item--link" href="{{ route('accounts.index') }}">仮想通貨アカウント一覧</a>
                        </li>
                        <li class="p-header__item">
                            <a class="p-header__item--link" href="{{ route('news.index') }}">ニュース</a>
                        </li>
                        <li class="p-header__item">
                            <a class="p-header__item--link" href="{{ route('mypage.index') }}">マイページ</a>
                        </li>
                        <li class="p-header__item">
                            <!-- TODO ログアウト処理を用意すること-->
                            <a class="p-header__item--link"
                               href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();"
                            >ログアウト</a>
                            <form id="logout-form" method="post" action="{{ route('logout') }}" style="display:none;">
                                @csrf
                            </form>
                        </li>
                    @endguest
                </ul>
            </nav>
        </div>
    </header>

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
