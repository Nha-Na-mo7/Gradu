@extends('layouts.app')
@section('title','新規登録')

@section('content')
<div class="l-container__auth p-auth">
    <div class="">
        <h2 class="p-auth__title">アカウントの新規作成</h2>
        <p>CryptoTrendは、Twitterと連携することでサービスを最大限に利用することが可能です！</p>
        <div class="">

            <div class="">
                <form method="POST" action="" autocomplete="off">
                    @csrf

                    <label
                            class="p-form__item"
                            for="name"
                    >ユーザーネーム ( 20文字以内 )</label>
                    @error('name')
                    <div class="c-error">
                        {{ $message }}
                    </div>
                    @enderror
                    <input
                            type="text"
                            class="c-form__input @error('name') c-error__input @enderror"
                            name="name"
                            value="{{ old('name') }}"
                    />

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

                    <label
                            class="p-form__item"
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
                            class="p-form__item"
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

                    <div class="">
                        <button
                                class="c-btn c-btn__auth"
                                type="submit"
                        >新規登録
                        </button>
                    </div>
                </form>
            </div>
            <div>
                <div class="p-auth__dividingText">
                    <span class="p-auth__dividingText-spanborder">または</span>
                </div>

                <a
                        class="c-btn c-btn--primary c-btn__twitter--login"
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
