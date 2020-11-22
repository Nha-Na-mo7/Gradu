<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    {{ __('平素CryptoTrendをご利用いただき誠にありがとうございます。') }}<br><br>
</p>
<p>
    {{ __('下記のURLにアクセスすることで、メールアドレスの更新手続きが完了いたします。') }}<br>
</p>
<p>
    {{ $actionText }}: <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
</p>

<p>
    <strong>{{ __('※ 有効期限は1時間以内です。') }}</strong>
    {{ __('有効期限が切れた場合、お手数ですがもう一度更新続きを行ってください。') }}<br><br>
</p>
<p>
    {{ __('※ このメールに心当たりがない場合、メールを破棄してください。') }}<br><br>
</p>
<p>
    {{ __('Copyright © CryptoTrend 2020 All Rights Reserved.') }}
</p>