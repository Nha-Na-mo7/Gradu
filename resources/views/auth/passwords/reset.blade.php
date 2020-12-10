<!-- トークンを元に変更完了させる側のbladeです -->

@extends('layouts.app')
@section('title', 'パスワードの再設定')

@section('content')
<div class="l-container__auth p-auth">
    <div class="p-auth__container">
        <div>
            <h2 class="p-form__title">パスワードの再設定</h2>
            <p class="p-form__description">新しく設定するパスワードを入力してください。</p>
        </div>

        <div class="p-form">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <label class="c-form__info" for="email"
                >メールアドレス</label>
                @error('email')
                <div class="c-error">
                    {{ $message }}
                </div>
                @enderror
                <input
                        id="email"
                        type="text"
                        class="c-form__input @error('email') c-error__input @enderror"
                        name="email"
                        value="{{ $email ?? old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                />

                <label
                        class="c-form__info"
                        for="password"
                >新しいパスワード(半角英数 8~50文字)</label>
                @error('password')
                <div class="c-error">
                    {{ $message }}
                </div>
                @enderror
                <input
                        id="password"
                        class="c-form__input @error('password') c-error__input @enderror"
                        type="password"
                        name="password"
                        required
                />

                <label class="c-form__info" for="password-confirm"
                >パスワードの再入力</label>
                @error('password_confirmation')
                <div class="c-error">
                    {{ $message }}
                </div>
                @enderror
                <input
                        id="password-confirm"
                        class="c-form__input c-from__input--reminder"
                        type="password"
                        name="password_confirmation"
                />
                <div class="c-form__submit">
                    <button class="c-btn c-btn__auth" type="submit">
                        パスワードを変更
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
