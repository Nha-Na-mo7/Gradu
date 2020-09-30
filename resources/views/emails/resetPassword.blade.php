<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    平素CryptoTrendをご利用いただき誠にありがとうございます。<br><br>
    以下のリンクをクリックして、<strong>60分以内に</strong>パスワードの再設定を行ってください。<br>
    パスワードの変更が必要ない場合、こちらのメールは無視していただいて構いません。<br>
    <br>
    ※このメールに心当たりがない場合、メールを破棄してください。
</p>
<p>
    {{ $actionText }}: <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
</p>