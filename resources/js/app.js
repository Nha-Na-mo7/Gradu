// Ajax通信用のAxiosライブラリの設定が記述されている、デフォルトのjsファイル
import './bootstrap.js';

// webpackでsassをコンパイルする用
import '../sass/app.scss';

import Vue from "vue";
// ルーティングの定義
import router from "./router.js";
// ルートコンポーネント
import App from "./App.vue";


// Vueインスタンスを作成
const createApp = async () => {
  
  // ユーザーデータ取得後にVueインスタンスを作成
  new Vue({
    el: '#app',
    router,
    components: { App },
    template: '<App />'
  });
}

// 上記メソッドを呼び出し。
createApp();
