{{-- ================= --}}
{{-- 共通のヘッダー      --}}
{{-- ================= --}}
{{-- ロゴ  --}}
<header id="header" class="l-header">
    <div class="p-header">

        {{-- ロゴ --}}
        <a class="p-header__logo" href="{{ route('home.index') }}">
            <img class="p-header__logo--img" src="storage/images/logos/logo.svg" alt="CryptoTrend" />
        </a>

        {{-- SPサイト用メニュー  --}}
        <div class="p-header__trigger js-toggle-sp-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>

        {{-- ナビバー  --}}
        <nav class="p-header__nav p-header__nav__sp js-toggle-sp-nav">

            <ul class="p-header__menu">
                @guest
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('login') }}"><span>ログイン</span></a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('register') }}"><span>新規登録</span></a>
                    </li>
                @else
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('trend.index') }}"><span>トレンド</span></a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('accounts.index') }}"><span>アカウント一覧</span></a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('news.index') }}"><span>ニュース</span></a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link" href="{{ route('mypage.index') }}"><span>マイページ</span></a>
                    </li>
                    <li class="p-header__item">
                        <a class="p-header__item--link"
                           href="{{ route('logout') }}"
                           onclick="e.preventDefault();
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
