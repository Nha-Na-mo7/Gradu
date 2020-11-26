// Ajax通信用のAxiosライブラリの設定が記述されている、デフォルトのjsファイル
import './bootstrap.js';
// jQuery
// import './jquery.js';

// webpackでsassをコンパイルする用
import '../sass/app.scss';

import Vue from "vue";
// ルーティングの定義
import store from "./store/index.js";
// ルーティングの定義
import router from "./router.js";
// ルートコンポーネント
import App from "./App.vue";


// Vueインスタンスを作成
// #appがない時はインスタンスを作らないようにできるか？

const createApp = async () => {
  
  // ユーザーデータ取得後にVueインスタンスを作成
  new Vue({
    el: '#app',
    store,
    router,
    components: { App },
    template: '<App />'
  });
}

// 上記メソッドを呼び出し。
createApp();
