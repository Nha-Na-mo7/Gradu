@extends('layouts.app')
@section('title','新規登録')

@section('content')
<div class="l-container__auth p-auth">
    <div class="p-auth__container">
        <h2 class="p-form__title">アカウントの新規作成</h2>
        <div class="p-form__description u-text--center">
            <p>CryptoTrendは、Twitterと連携することでサービスを最大限に利用できます！</p>
        </div>
        <div class="">
            {{-- 実際のフォーム --}}
            <div class="p-form p-form__auth">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

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

                    <label
                            class="c-form__info"
                            for="password"
                    >パスワード (半角英数字 8~50文字)</label>
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

                    <label
                            class="c-form__info"
                            for="password-confirm"
                    >パスワードの再入力</label>
                    @error('password_confirmation')
                    <div class="c-error">
                        {{ $message }}
                    </div>
                    @enderror
                    <input
                            type="password"
                            id="password_confirm"
                            class="c-form__input @error('password_confirmation') c-error__input @enderror"
                            name="password_confirmation"
                    />

                    <div class="c-form__submit">
                        <button
                                class="c-btn c-btn__auth"
                                type="submit"
                        >新規登録
                        </button>
                    </div>
                </form>
            </div>
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
                >Twitterで新規登録
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
