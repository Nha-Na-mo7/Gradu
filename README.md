<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## テストユーザー
テストユーザー用メールアドレス: test@example.com
テストユーザー用パスワード : testtest


## 機能概要
- TwitterAPIによる「仮想通貨」に関する通貨名ごとのツイート数を集計し、ランキング形式で表示することで、トレンドとなっている仮想通貨名を知ることができるWEBサービス
- 他、仮想通貨関連を中心としたTwitterアカウントの一覧表示を行う
- 仮想通貨関連のニュースを一覧表示する

## 機能一覧
### 取扱通貨について
- 仮想通貨自体は膨大な銘柄が存在するため、このサービスで取り扱い対象とする仮想通貨は " CoinCheck "で取り扱う通貨の全てとする。
下記リンク先を参照。
https://coincheck.com/ja/exchange/closing_prices

### Twitterアカウント連携機能
1ユーザにつき、Twitterアカウントを1つ連携でき、以下の機能を利用できる。
- Twitterによる新規登録/ログイン
- 他の仮想通貨関連Twitterアカウントのフォロー

連携させたTwitterアカウントによるフォローを行うことができる。

### 仮想通貨・トレンド表示機能
- 取り扱っている仮想通貨名それぞれについて、Twitter上でのツイート数を取得・集計し、ツイート数の多い順にランキング形式で表示する。
検索条件は、「通貨の英単語略称_通貨のカタカナ名称」という条件としている(例: 「BTC ビットコイン」)
- 過去1時間、過去1日、過去1週間でランキング表示の条件を指定できる。
- ランキングにはツイート数の他、その通貨の24時間での最高取引価格 / 最安取引価格が表示される。
（※ CoinCheckAPIでは「ビットコイン」のみにしか対応していないため、ビットコインのみ表示されている状態です。取得できない通貨は「不明」と表示します。）
- 通貨名を絞って表示可能(複数選択)。

- 画面表示時にリアルタイムでツイート数を取得しに行くと表示に時間がかかり(1時間以内のツイート数だけでも5分はかかる)利便性に欠けるため、定期的にツイート数を取得し、DBに登録する。
- ランキング内のある銘柄名をクリックすると、TwitterのTweet検索画面に遷移する。検索欄にはクリックした通貨名が既に入力されている状態になっている。

### 仮想通貨アカウント一覧表示機能 (Twitter連携時のみ)
- 「仮想通貨」というキーワードをユーザー名またはプロフィールに記載しているユーザを一覧で表示し、画面上からフォローできる。
- ユーザ名、アカウント名、プロフィールのほか、フォロー数、フォロワー数、(集計時点での)最新ツイート一件を表示する。
- 1日1回、「仮想通貨」ユーザーを検索し、DBに登録することで一覧表示している。
- 表示は新規アカウントを優先して表示。

### 仮想通貨アカウント自動フォロー機能
- 仮想通貨アカウント一覧画面の「自動フォロー」を有効にすることで、一覧表示されているアカウントを全てフォローできる機能。
- フォローの間隔は 4ユーザー/15分 としている。これはTwitterAPIの制限が「15/15min」かつ「1000/1day」と定められているため、この上限を上回らない程度のペースに設定した。

### 仮想通貨関連ニュース表示機能
- GoogleニュースAPIを使って、「仮想通貨」というキーワードでニュースを検索し、仮想通貨関連のニュースを一覧表示する。
- 取扱通貨名に絞ってニュース検索をやり直すことも可能
- 通貨名によっては仮想通貨と関係のないニュースが取得されることを防ぐため、「仮想通貨」と先頭についた状態で検索をかけている。
(例:「ETC」は、そのままだと料金所のETCのニュースなどもかかってしまう)

### その他機能
- TOP(SEO対策済み)
- パスワードリマインダー

CoincheckAPI（仮想通貨取引所のAPI）
https://coincheck.com/ja/documents/exchange/api

### 開発環境
- LAMP環境
- Laravel6
- Vue.js
- CSS設計: FLOCSS設計
- CSSフレームワーク(Bootstrapなど)未使用
- レスポンシブデザイン
- webpack＋babel
- SPA

### 対応ブラウザ
- Android4.4~
- iOS10~
- GoogleChrome
- Safari
- Edge

- IE11未対応(Twitter自体がIEをサポートしていないため)
