@extends('layouts.app')
@section('title', 'パスワードの再設定')

{{-- メールを送信する側 --}}

@section('content')
<div class="l-container__auth p-auth">
    <div class="p-auth__container">
        {{-- ここでメッセージ送信した通知が出る --}}
        @if (session('status'))
            <div class="p-form__send">
                <p class="p-form__send--msg" role="alert">
                    {{ session('status') }}
                </p>
                <p class="u-color--red">※ メールが届くまで、約5分ほどお時間がかかる場合があります。</p>
                <p>5分を過ぎてもメールが届かない場合、入力されたメールアドレスが間違っているか、迷惑メールフォルダに入っている可能性があります。</p>
            </div>
        @endif

        <h2 class="p-form__title">パスワードを忘れた場合</h2>
        <div class="p-form__description u-text--center">
            <p>アカウント作成時にご登録いただいたメールアドレスを入力してください。</p>
            <p>入力したメールアドレス宛に、パスワード変更ページのURLが記載されたメールを送信します。</p>
        </div>

        <div class="p-form p-form__auth">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <label
                        class="c-form__info"
                        for="email"
                >
                    ご登録されているメールアドレスを入力してください</label
                >
                @error('email')
                <div class="c-error">
                    {{ $message }}
                </div>
                @enderror
                <input
                        type="text"
                        class="c-form__input @error('email') c-error__input @enderror"
                        name="email"
                        value="{{ old('email') }}"
                />

                <div class="c-form__submit">
                    <button type="submit" class="c-btn c-btn__auth">
                        メールを送信する</button>
                </div>
            </form>

            <div class="p-auth__another">
                <div class="c-border">
                    <div class="c-border__dividingText"></div>
                </div>
                <a
                        class="c-btn"
                        href="{{ route('login') }}"
                >
                    ログイン画面へ戻る
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
