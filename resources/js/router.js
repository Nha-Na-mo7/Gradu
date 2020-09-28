import Vue from "vue";
import VueRouter from "vue-router";

// ページコンポーネントのインポート
import Index from './pages/Index.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import PassReset from './pages/PassReset.vue';

// ストアのインポート
import store from './store';

// VueRouterプラグインの使用
Vue.use(VueRouter);



// パスとコンポーネントをマッピング
const routes = [
  {
    path: '/',
    component: Index
  },
  {
    path: '/login',
    component: Login,
    // URL直入力などでログイン済みのユーザーはアクセスできないページに無理やり行こうとした時、
    // ナビゲーションガードを使ってホームに遷移させる。
    beforeEnter(to, from, next) {
      // ログイン状態をチェックし、分岐させる
      if(store.getters['auth/loginCheck']) {
        next('/');
      }else{
        next();
      }
    }
  },
  {
    path: '/register',
    component: Register,
    beforeEnter(to, from, next) {
      // ログイン状態をチェックし、分岐させる
      if(store.getters['auth/loginCheck']) {
        next('/');
      }else{
        next();
      }
    }
  },
  {
    path: '/password/reset',
    component: PassReset
  },
]


const router = new VueRouter({
  mode: 'history',
  routes
})

// VueRouterインスタンスのエクスポート(app.jsでインポートする)
export default router;