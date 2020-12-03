{{-- =========================== --}}
{{--  ユーザーが最初に訪れるページです --}}
{{-- =========================== --}}
@extends('layouts.app')

@section('title', '仮想通貨のトレンドがすぐにわかる')

{{-- descriptionはSPユーザーも想定して、90文字行かない程度が理想 --}}
@section('description', '注目されている仮想通貨がすぐにわかる！銘柄ごとにツイート数を集計し、可視化された最新のトレンド情報で仮想通貨を攻略しよう！仮想通貨のトレンド情報はCryptoTrend。')
@section('keywords', 'CryptoTrend, クリプトトレンド, 仮想通貨, 暗号通貨, ビットコイン, アルトコイン, BTC, イーサリアム, ETH, Twitter, Twitterアカウント, ツイッター, 自動フォロー, トレンド')

@section('content')
    {{-- ヒーローバナー --}}
    <section class="l-hero p-landing">
        <div class="p-landing__introduction">
            <h1 class="p-landing__introduction--title">
                仮想通貨のトレンドを
                <br />追うのに疲れていませんか？
            </h1>
            <p class="p-landing__introduction--subtitle">Crypto Trend で、最新のトレンド通貨を知ろう</p>
            @guest
                <div class="p-landing__introduction--btn">
                    <a href="{{ route('register') }}" class="">
                        <button class="c-btn c-btn__auth">無料で新規登録</button>
                    </a>
                </div>
            @endguest
        </div>
    </section>

    {{-- CryptoTrendとは？ --}}
    <section class="p-landing__section p-landing__information">
        <div class="p-landing__container">
            <div class="p-landing__section--info">
                <h2 class="p-landing__section--title">
                    CryptoTrendは<br /><br />
                    「仮想通貨」 に特化したトレンド情報サービスです
                </h2>
                <p class="p-landing__section--text">
                    <span>トレンドの移り変わりが激しい仮想通貨の情報を追いきれない...</span><br /><br />
                    <span>こんな悩みはCryptoTrendですぐに解決。</span><br />
                    <span>CryptoTrendは、最も人気のある仮想通貨の情報を、無料でリアルタイムに確認可能です。</span>
                </p>
            </div>
        </div>
    </section>

    {{-- 3大機能の紹介 --}}
    <section class="p-landing__section p-landing__contents u-bg-blue">
        <div class="p-landing__container">
            <div class="p-landing__section--info">
                <h2 class="p-landing__section--title">トレンドを確実に捉える3つの機能</h2>
                <p class="p-landing__section--text">
                    <span>CryptoTrendは、3つの機能で仮想通貨のトレンドを見逃しません。</span><br />
                </p>
            </div>
            <div class="p-landing__service">
                {{-- ツイート数 --}}
                <div class="p-landing__service--item">
                    <div class="">
                        <img class="p-landing__service--item--img" src="https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/figures/index_section_twitter350.jpeg" alt="仮想通貨トレンド" />
                    </div>
                    <div class="p-landing__service--item--info">
                        <h1 class="p-landing__service--item--info--title">通貨別ツイート数集計</h1>
                        <p class="p-landing__service--item--info--text">
                            通貨ごとにツイートを集計してランキングにしているため、
                            常にTwitterでのトレンド通貨をわかりやすくチェック出来ます。
                        </p>
                    </div>
                </div>

                {{-- 仮想通貨ニュース --}}
                <div class="p-landing__service--item">
                    <div class="">
                        <img class="p-landing__service--item--img" src="https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/figures/index_section_news.jpeg" alt="仮想通貨ニュース" />
                    </div>
                    <div class="p-landing__service--item--info">
                        <h1 class="p-landing__service--item--info--title">仮想通貨ニュース</h1>
                        <p class="p-landing__service--item--info--text">
                            仮想通貨に特化した最新のニュースが検索可能です。
                            トレンド通貨をすぐに調べられるので、情報を取りこぼすことがありません。
                        </p>
                    </div>
                </div>


                {{-- アカウント一覧 --}}
                <div class="p-landing__service--item">
                    <div class="">
                        <img class="p-landing__service--item--img" src="https://cryptotrendstrage.s3-ap-northeast-1.amazonaws.com/images/figures/index_section_follow.jpeg" alt="仮想通貨アカウント一覧" />
                    </div>
                    <div class="p-landing__service--item--info">
                        <h1 class="p-landing__service--item--info--title">自動フォロー</h1>
                        <p class="p-landing__service--item--info--text">
                            仮想通貨に関連したTwitterアカウントを自動でフォローすることが可能です。
                            面倒なTwitter運用も、放っておくだけで完了します。
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{--無料で今すぐはじめよう--}}
    <section class="p-landing__section p-landing__footer">
        <div class="p-landing__container">
            <div class="p-landing__section--info">
                <h2 class="p-landing__section--title">
                    さっそく始めよう
                </h2>
                <p class="p-landing__section--text u-mb-3l">
                    <span>登録はもちろん無料。仮想通貨の最先端を掴みましょう。</span>
                </p>
            </div>
            @guest
                <div class="">
                    <a href="{{ route('register') }}">
                        <button class="c-btn c-btn__auth">無料で新規登録</button>
                    </a>
                </div>
            @else
                <div class="">
                    <a href="{{ route('trend.index') }}">
                        <button class="c-btn c-btn__auth">トレンドをチェック</button>
                    </a>
                </div>
            @endguest
        </div>
    </section>
@endsection
