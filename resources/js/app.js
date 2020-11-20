// Ajax通信用のAxiosライブラリの設定が記述されている、デフォルトのjsファイル
import './bootstrap.js';

// webpackでsassをコンパイルする用
import '../sass/app.scss';

import Vue from "vue";
// ルーティングの定義
import router from "./router.js";
// (ルート)ストア
import store from "./store/index.js";
// ルートコンポーネント
import App from "./App.vue";


// Vueインスタンスを作成
// この時ログインがされている場合、そのユーザー情報を取得してからインスタンスを作成する
const createApp = async () => {
  // 現在のログインユーザーを取得
  await store.dispatch('auth/currentUser')
  
  // ユーザーデータ取得後にVueインスタンスを作成
  new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App />'
  });
}

// 上記メソッドを呼び出し。
createApp();
