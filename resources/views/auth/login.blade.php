@extends('layouts.app')
@section('title', 'ログイン')

@section('content')
<div class="l-container__auth p-auth">
    <div class="p-auth__container">
        <h2 class="p-form__title">ログイン</h2>
        <div class="p-form p-form__auth">

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- email -->
                <label
                        class="c-form__info"
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
                        value="{{ $email ?? old('email') }}"
                />

                <!-- password -->
                <label class="c-form__info"
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

                <!-- remember me -->
                <div class="c-form__info c-form__check">
                    <label for="remember">
                        <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                class="c-form__check">
                        ログイン状態を維持する
                    </label>
                </div>

                <div class="c-form__submit">
                    <button
                            type="submit"
                            class="c-btn c-btn__auth"
                    >ログイン</button>
                </div>

                <div class="c-form__submit">
                    <a class="c-btn" href="{{ route('password.request') }}">
                        パスワードを忘れた方はこちら</a>
                </div>
            </form>

            <div class="p-auth__another">
                <div class="c-border">
                    <div class="c-border__dividingText">
                        <span class="c-border__dividingText-spanborder">または</span>
                    </div>
                </div>


                <a
                        class="c-btn c-btn__twitter"
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
