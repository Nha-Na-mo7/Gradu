@extends('layouts.app')
@section('title', 'CryptoTrend | ログイン')

@section('content')
<div class="l-container--authform p-auth">
    <div class="">
        <h2 class="p-auth__title">ログイン</h2>
        <div class="">

            <!-- TODO エラーメッセージ用確認 -->
            @if(Session::has('error_message'))
                <div class="c-error__authflash">
                    <p>{{ session('error_message') }}</p>
                </div>
            @endif


            <form method="POST" action="{{ route('login') }}">
                @csrf



                <label
                        class="p-form__item"
                        for="email"
                >メールアドレス</label>
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

                <label class="p-form__item" for="password">パスワード (半角英数字 8~50文字)</label>
                <input
                        type="password"
                        class="c-form__input @error('password') c-error__input @enderror"
                        name="password"
                        value="{{ old('password') }}"
                />
                @error('password')
                <div class="c-error">
                    {{ $message }}
                </div>
                @enderror

                <div class="p-form__item p-form__item--check">
                    <label for="remember">
                        <input type="checkbox" name="remember" id="remember">
                        ログイン状態を維持する
                    </label>
                </div>

                <div class="">
                    <button
                            type="submit"
                            class="c-btn c-btn__auth"
                    >ログイン</button>
                </div>

                <div class="">
                    <a class="p-form__inquiry" href="{{ route('password.request') }}">
                        パスワードを忘れた方はこちら</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
