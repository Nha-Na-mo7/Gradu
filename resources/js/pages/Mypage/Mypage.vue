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
      <div>
        <h2>ユーザーネーム</h2>
        <p>aaaaaaaaaaaaaaaaaaaaaaaaa</p>
      </div>
      <div>
        <h2>メールアドレス</h2>
        <p>114514@gmail.com</p>
      </div>
    </div>

    <!-- ユーザー名・メールアドレス -->
    <div class="dummybox">
      <div>
        <h2>パスワード</h2>
        <p>********</p>
      </div>
    </div>

    <!-- SNS連携 -->
    <div class="dummybox">
      <div>
        <h2>SNS連携状態</h2>
        <p>Twitter</p>
        <button>連携する</button>
        <button>連携解除</button>
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
      <button class="c-btn">退会する</button>
    </div>


    <div class="p-auth__dividingText">
      <span class="p-auth__dividingText-spanborder">または</span>
    </div>
    <a
        class="c-btn c-btn--primary c-btn__twitter--login"
        title="Start for Twitter!"
        @click.stop
        :href="`/login/twitter`"
    >Twitter連携する</a>

  </div>

</div>
</template>

<script>
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
const PAGE_TITLE = 'マイページ(アカウント設定)';

export default {
  computed: {
    page_title() {
      return PAGE_TITLE
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