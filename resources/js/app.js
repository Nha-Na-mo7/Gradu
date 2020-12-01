// Ajax通信用のAxiosライブラリの設定が記述されている、デフォルトのjsファイル
import "./bootstrap.js";

// jQuery
import jQuery from "./jquery.js";

// webpackでsassをコンパイルする用
import "../sass/app.scss";

import Vue from "vue";
// ストアの定義
import store from "./store/index.js";
// ルーティングの定義
import router from "./router.js";
// ルーティングの定義
import paginate from "./paginate.js";
// ルートコンポーネント
import App from "./App.vue";

// Vueインスタンスを作成するメソッド
const createApp = async () => {
  // id="app"がHTMLに存在する時のみVueインスタンスを作成
  if (document.getElementById("app") != null) {
    new Vue({
      el: "#app",
      store,
      router,
      paginate,
      jQuery,
      components: { App },
      template: "<App />",
    });
  }
};

// 上記メソッドを呼び出し。
createApp();
