<!-- =========================-->
<!--  仮想通貨アカウント一覧ページ -->
<!-- =========================-->
@extends('layouts.app')

@section('title', 'トレンド通貨・ツイート数ランキング | CryptoTrend')

@section('content')

    {{-- Twitter連携が行われていないアカウントだった場合、アカウント一覧は表示させない --}}
    {{--    1,ユーザのtwitterアカウント判定 → 一覧ページ読み込みの流れとなり読み込みが遅くなる--}}
    {{--    2,Twitterアカウント連携を促すページも基本的には静的なもの--}}
    {{--    上記理由から、こちらのセッションで区分する--}}
    @if(Session::has('twitter_id'))
        <div id="app">

        </div>
    @else
        <div>
            <h2 style="font-size: 128px;">こちらのページを利用するには、Twitterの連携が必要です。</h2>
            <button class="c-btn">Twitter連携を行う。</button>
        </div>
    @endif
@endsection