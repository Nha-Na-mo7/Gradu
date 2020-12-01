{{-- =========================== --}}
{{--  ユーザーが最初に訪れるページです --}}
{{-- =========================== --}}
@extends('layouts.app')

@section('title', '仮想通貨のトレンドがすぐにわかる')

{{-- descriptionはSPユーザーも想定して、90文字行かない程度が理想 --}}
@section('description', '注目されている仮想通貨がすぐにわかる！銘柄ごとにツイート数を集計し、可視化された最新のトレンド情報で仮想通貨を攻略しよう！仮想通貨のトレンド情報はCryptoTrend。')
@section('keywords', 'CryptoTrend, クリプトトレンド, 仮想通貨, 暗号通貨, ビットコイン, アルトコイン, BTC, イーサリアム, ETH, Twitter, Twitterアカウント, ツイッター, 自動フォロー, トレンド')

@section('content')
    {{--ヒーローバナー--}}
    <section class="l-hero p-landing">
        <div class="p-landing__introduction">
            <h1 class="p-landing__introduction--title">
                仮想通貨のトレンドを
                <br />追うのに疲れていませんか？
            </h1>
            <p>Crypto Trend で、最新のトレンド通貨を知ろう</p>
            @guest
                <div class="">
                    <a href="{{ route('register') }}" class="">
                        <button class="c-btn c-btn__auth">無料で新規登録</button>
                    </a>
                    <div class="">
                        <a class="" href="{{ route('login') }}"><p>既にアカウントをお持ちの方</p></a>
                    </div>
                </div>
            @endguest
        </div>
    </section>

    {{--CryptoTrendとは？--}}
    <section class="p-landing__container p-landing__info">
        <div class="">
            <h1 class="">"仮想通貨"に特化した、トレンド情報サービスです</h1>
            <div>
                <p>トレンドの移り変わりが激しい仮想通貨の情報を追いきれない...</p>
                <p>こんな悩みはCryptoTrendですぐに解決。</p>
                <p>CryptoTrendは、最も人気のある仮想通貨をリアルタイムで確認可能です。</p>
            </div>
        </div>
    </section>

    {{--ツイート数を集計してトレンドが一眼でわかる--}}
    <section class="p-section p-section__three">
        <div class="">
            <div class="">
                {{-- <img src="" alt />--}}
            </div>
            <div class="">
                <h1 class="">最もツイートされている通貨をチェックしよう</h1>
                <p class="">
                    Twitterで今最もツイートされている通貨は何？
                    CoinCheckで取り扱う通貨1つ1つに対してのツイートが集計されているため、
                    常に最新のトレンドを漏らすことなくチェック出来ます。
                </p>
            </div>
        </div>
    </section>

    {{--仮想通貨ニュースをすぐに確認しよう--}}
    <section class="p-section p-section__three">
        <div class="">
            <div class="">
                {{--<img src="" alt />--}}
            </div>
            <div class="">
                <h1 class="">最新の仮想通貨ニュースをチェックしよう</h1>
                <p class="">
                    仮想通貨に特化した最新のGoogleニュースがすぐに検索可能です。
                    トレンド通貨をすぐに調べられるので、情報を取りこぼすことがありません。
                </p>
            </div>
        </div>
    </section>

    {{--気になる仮想通貨ツイッターユーザーをフォローしよう--}}
    <section class="p-section p-section__three">
        <div class="">
            <div class="">
                {{-- <img src="" alt />--}}
            </div>
            <div class="">
                <h1 class="">仮想通貨ユーザーをもれなくフォローしよう</h1>
                <p class="">
                    仮想通貨に関連したTwitterユーザーをフォローすることも出来ます。
                    さらに、自動フォロー機能を使えば、何もしなくても仮想通貨の関連アカウントを網羅できます。
                </p>
            </div>
        </div>
    </section>

    {{--無料で今すぐはじめよう--}}
    <section class="p-section p-section__footer">
        <h1 class="">
            さっそく始めよう
        </h1>
        <p>登録はもちろん無料。仮想通貨の最先端を掴みましょう。</p>
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
    </section>
@endsection
