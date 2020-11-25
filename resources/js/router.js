import Vue from "vue";
import VueRouter from "vue-router";

// ページコンポーネントのインポート
import Index from './pages/Index.vue';
// 認証系 過去作さん
// import Login from './pages/Auths/Login.vue';
// import Register from './pages/Auths/Register.vue';
// import RegisterCompletion from './pages/Auths/RegisterCompletion.vue';
// import PassResetMailSend from './pages/Auths/PassResetMailSend.vue';
// import PassResetForm from './pages/Auths/PassResetForm.vue';
// Googleニュース
import NewsList from './pages/News/NewsList.vue';
// Twitterアカウント一覧
import AccountList from './pages/AccountLists/AccountList.vue';
// 仮想通貨人気ツイートランキングページ
import TrendList from './pages/Trends/TrendList.vue';
// マイページ/アカウント設定
import Mypage from './pages/Mypage/Mypage.vue';
import Profile from './pages/Mypage/Profile.vue';
import PasswordMenu from './pages/Mypage/PasswordMenu.vue';

// エラー系
import SystemError500 from './pages/errors/System.vue';
import NotFound404 from './pages/errors/NotFound.vue';

// ストアのインポート
import auth from './store/authenticate_store.js';

// VueRouterプラグインの使用
Vue.use(VueRouter);


// vue-routerからvuexを参照するには直接インポートする
// 認証切れの場合に"/login"へ遷移させる
async function requireLogin(to, from, next){
  await auth.actions.check_authenticate();
  if(!auth.state.authenticate) {
    next(true);
  }else{
    document.location.reload();
  }
}


// パスとコンポーネントをマッピング
const routes = [
  {
    path: '/',
    component: Index
  },
  {
    beforeEnter: requireLogin,
    path: '/trends',
    component: TrendList,
    props: true,
  },
  {
    beforeEnter: requireLogin,
    path: '/accounts',
    component: AccountList,
    props: route => {
      const p = route.query.p
      // 整数でなかった場合、「1」として返す
      return { p: /^[1-9][0-9]*$/.test(p) ? p * 1 : 1 }
    },
  },
  {
    beforeEnter: requireLogin,
    path: '/news',
    component: NewsList,
    props: true,
  },
  {
    beforeEnter: requireLogin,
    path: '/mypage',
    component: Mypage,
    props: true,
  },
  {
    beforeEnter: requireLogin,
    path: '/mypage/profile',
    component: Profile,
    props: true,
  },
  {
    beforeEnter: requireLogin,
    path: '/mypage/password',
    component: PasswordMenu,
    props: true,
  },
  {
    path: '/500',
    component: SystemError500
  },
  {
    path: '*',
    component: NotFound404
  }
]


const router = new VueRouter({
  mode: 'history',
  scrollBehavior () {
    return { x: 0, y: 0 }
  },
  routes
  
})

// VueRouterインスタンスのエクスポート(app.jsでインポートする)
export default router;