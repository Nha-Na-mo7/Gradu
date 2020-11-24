@extends('layouts.app')
@section('title', 'ログイン')

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

                <!-- email -->
                <div>
                    <label
                            class="p-form__item u__mt-xl"
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
                </div>

                <!-- password -->
                <div>
                    <label class="p-form__item u__mt-xl"
                           for="password">パスワード (半角英数字 8~50文字)</label>
                    @error('password')
                    <div class="c-error">
                        {{ $message }}
                    </div>
                    @enderror
                    <input
                            type="password"
                            class="c-form__input @error('password') c-error__input @enderror"
                            name="password"
                            value="{{ old('password') }}"
                    />
                </div>

                <!-- remember me -->
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
            <div>
                <div class="p-auth__dividingText">
                    <span class="p-auth__dividingText-spanborder">または</span>
                </div>

                <a
                        class="c-btn c-btn--primary c-btn__twitter--login"
                        title="Start for Twitter!"
                        @click.stop
                        href="{{ route('twitter.begin') }}"
                >Twitterでログイン
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
