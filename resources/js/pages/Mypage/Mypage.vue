<template>
<div class="l-container__content">

  <!-- サイトリンク -->
  <SiteLinknav :currentPageTitle='page_title'/>

  <!-- ページタイトル -->
  <PageTitle :title='page_title'/>

  <!-- メインレイアウト -->
  <div class="p-container dummyflex">

    <!-- ユーザー名・メールアドレス -->
    <div class="dummybox">
      <RouterLink to="/mypage/profile">設定変更</RouterLink>
      <div>
        <h2>ユーザーネーム</h2>
        <p>aaaaaaaaaaaaaaaaaaaaaaaaa</p>
      </div>
      <div>
        <h2>メールアドレス</h2>
        <p>114514@gmail.com</p>
      </div>
    </div>

    <!-- パスワード -->
    <div class="dummybox">
      <RouterLink to="/mypage/password">設定変更</RouterLink>
      <div>
        <h2>パスワード</h2>
        <p>********</p>
      </div>
    </div>

    <!-- SNS連携 -->
    <div class="dummybox">
      <div>
        <h2>SNSログイン連携</h2>
        <h2>Twitter</h2>
        <!-- 連携中の時 -->
        <div>
          <p>連携中</p>
          <p>Twitterアカウントでログインでき、仮想通貨アカウント一覧機能を利用することができます。</p>
          <button>解除する</button>
        </div>

        <!-- 連携していない時 -->
        <div>
          <p>連携していません</p>
          <p>仮想通貨アカウント一覧機能など、一部の機能がご利用できません。</p>
          <button>
            <a
              class="c-btn c-btn--primary c-btn__twitter--login"
              title="Start for Twitter!"
              @click.stop
              :href="`/login/twitter`"
            >
              連携する
            </a>
          </button>
        </div>
      </div>
    </div>

    <!-- 自動フォロー状態 -->
    <div class="dummybox">
      <div>
        <h2>Twitterアカウントの自動フォロー</h2>
        <p>ON</p>
        <button>切り替える</button>
      </div>
    </div>

    <!-- 退会処理 -->
    <div class="p-mypage__footer">
      <button
          class="c-btn"
          @click="withdraw"
      >退会する</button>
    </div>


  </div>

</div>
</template>

<script>
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import { OK , INTERNAL_SERVER_ERROR } from '../../util.js';
const PAGE_TITLE = 'マイページ(アカウント設定)';


export default {
  computed: {
    page_title() {
      return PAGE_TITLE
    }
  },
  methods: {
    // 退会処理
    async withdraw() {
      if(confirm('【 CryptoTrendを退会しますか？ 】\n退会すると色々なサービスの利用ができなくなります。')){
        const response = await axios.post(`/api/withdraw`);

        if(response.status === OK){
          window.location = "/";
        }else{
          window.location = "/login";
        }
      }
    }
  },
  components: {
    SiteLinknav,
    PageTitle
  }
}
</script>

<style scoped>
.dummyflex {
  display: flex;
  flex-wrap: wrap;
  width: 100%;
  position: relative;
}
.dummybox {
  width: 45%;
  font-size: 15px;
  margin-right: 20px;
  margin-bottom: 30px;
  border: 1px solid #0a0a0a;
  background: #d8d8d8;
  min-height: 200px;
}
</style>