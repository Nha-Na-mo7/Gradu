{{-- ================= --}}
{{-- 共通のヘッダー      --}}
{{-- ================= --}}
{{-- ロゴ  --}}
<header id="header" class="l-header">
    <div class="p-header">

        {{-- ロゴ --}}
        <a class="p-header__logo" href="{{ route('home.index') }}">
            <img class="p-header__logo" src="" alt="CryptoTrend" />
            {{--                <img class="p-header__logo" src="{{ asset('images/header_logo.png') }}" alt="CryptoTrend" />--}}
        </a>

        {{-- SPサイト用メニュー  --}}
        <div id="" class="p-header__trigger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        {{-- ナビバー  --}}
        <nav id="" class="p-header__nav p-header__nav__sp">

            <ul class="p-header__menu">
                @guest
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('login') }}">ログイン</a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('register') }}">新規登録</a>
                    </li>
                @else
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('trend.index') }}">トレンド</a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('accounts.index') }}">アカウント一覧</a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('news.index') }}">ニュース</a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('mypage.index') }}">マイページ</a>
                    </li>
                    <li class="p-header__item">
                        {{-- TODO ログアウト処理を用意すること --}}
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
