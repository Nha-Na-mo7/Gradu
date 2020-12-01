@extends('layouts.app')
@section('title', 'パスワードの再設定')

{{-- メールを送信する側 --}}

@section('content')
<div class="l-container__auth p-auth">
    <div class="p-auth__container">
        <div>
            <h2 class="p-form__title">パスワードを忘れた場合</h2>
            <div class="p-form__decription">
                <p>アカウント作成時にご登録いただいたメールアドレスを入力してください。</p>
                <p>入力したメールアドレス宛に、パスワード変更ページのURLが記載されたメールを送信します。</p>
            </div>
        </div>

        <div class="p-form">
            {{-- ここでメッセージ送信した通知が出る --}}
            @if (session('status'))
                <div class="p-form__send">
                    <p class="p-form__send--msg" role="alert">
                        {{ session('status') }}
                    </p>
                    <p>メールが届かない場合、入力されたメールアドレスが間違っているか、迷惑メールフォルダに入っている可能性がありますので確認してください。</p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <label
                        class="p-form__info"
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

                <div class="p-form__submit">
                    <button type="submit" class="c-btn c-btn__auth">
                        メールを送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
