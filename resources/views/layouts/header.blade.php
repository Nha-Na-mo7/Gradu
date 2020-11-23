@section('header')
<header class="l-header">

    <!-- ロゴ -->
    <div class="p-header__logo-area">
        <a class="p-header lololink" href="{{ route('home') }}">
            <img class="p-header__logo" src="{{ asset('images/header_logo.png') }}" alt="CryptoTrend" />
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
                    <a class="p-header__item--link" href="{{ route('user.index') }}">マイページ</a>
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
</header>
@endsection
