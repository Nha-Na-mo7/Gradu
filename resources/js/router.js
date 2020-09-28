import Vue from "vue";
import VueRouter from "vue-router";

// ページコンポーネントのインポート
import Index from './pages/Index.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import PassReset from './pages/PassReset.vue';

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
    component: Login
  },
  {
    path: '/register',
    component: Register
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