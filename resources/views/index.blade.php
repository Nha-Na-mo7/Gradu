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
    <section class="p-section">
    </section>
    {{--CryptoTrendとは？--}}
    <section class="p-section">
    </section>
    {{--ツイート数を集計してトレンドが一眼でわかる--}}
    <section class="p-section p-section__three">
    </section>
    {{--仮想通貨ニュースをすぐに確認しよう--}}
    <section class="p-section p-section__three">
    </section>
    {{--気になる仮想通貨ツイッターユーザーをフォローしよう--}}
    <section class="p-section p-section__three">
    </section>
    {{--無料で今すぐはじめよう--}}
    <section class="p-section p-section__footer">
    </section>
@endsection
