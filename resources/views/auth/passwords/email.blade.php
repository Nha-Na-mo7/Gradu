@extends('layouts.app')
@section('title', 'パスワードの再設定')

<!-- メールを送信する側 -->

@section('content')
<div class="l-container__auth">
    <div class="">
        <div>
            <h2 class="p-form__title">パスワードを忘れた場合</h2>
            <p>アカウント作成時にご登録いただいたメールアドレスを入力してください。</p>
            <p>入力したメールアドレス宛に、パスワード変更ページのURLが記載されたメールを送信します。</p>
        </div>

        <div class="">
            <!-- ここでメッセージ送信した通知が出る-->
            @if (session('status'))
                <div class="c-XXX c-XXXXXX" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <label
                        class="p-form__info"
                        for="email"
                >
                    ご登録のメールアドレスを入力してください</label
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

                <div class="">
                    <button type="submit" class="c-btn c-btn__auth">
                        メールを送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
