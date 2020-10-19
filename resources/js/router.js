import Vue from "vue";
import VueRouter from "vue-router";

// ページコンポーネントのインポート
import Index from './pages/Index.vue';
// 認証系
import Login from './pages/Auths/Login.vue';
import Register from './pages/Auths/Register.vue';
import RegisterCompletion from './pages/Auths/RegisterCompletion.vue';
import PassResetMailSend from './pages/Auths/PassResetMailSend.vue';
import PassResetForm from './pages/Auths/PassResetForm.vue';
// Googleニュース
import NewsList from './pages/News/NewsList.vue';
// エラー系
import SystemError500 from './pages/errors/System.vue';


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
    path: '/registerCompletion',
    component: RegisterCompletion,
    beforeEnter(to, from, next) {
      // 未ログイン状態なら戻す
      if(store.getters['auth/loginCheck']) {
        next();
      }else{
        next('/login');
      }
    }
  },
  {
    path: '/password/reset',
    component: PassResetMailSend
  },
  {
    path: '/password/reset/:token',
    component: PassResetForm,
    props: true
  },
  
  {
    path: '/news',
    component: NewsList,
    props: true,
    // TODO ニュースリストは分岐させなくても良い？
    beforeEnter(to, from, next) {
      // 未ログイン状態ならログインチェックに戻す
      if(store.getters['auth/loginCheck']) {
        next();
      }else{
        next('/login');
      }
    }
  },
  {
    path: '/500',
    component: SystemError500
  }
]


const router = new VueRouter({
  mode: 'history',
  routes
})

// VueRouterインスタンスのエクスポート(app.jsでインポートする)
export default router;