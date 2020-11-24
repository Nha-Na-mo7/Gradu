<!-- RouterView部分において各コンポーネントが切り替わります -->
<template>
  <div class="l-content-wrapper">
    <!-- ヘッダー -->
<!--    <header id="header" class="l-header">-->
<!--      <Header />-->
<!--    </header>-->

    <!-- メイン -->
    <main>
      <div class="l-container">
        <!-- 実際にコンポーネントが切り替わるエリア -->
        <RouterView />
      </div>
    </main>

    <!-- フッター -->
    <footer id="footer" class="l-footer p-footer">
      <Footer />
    </footer>
  </div>
</template>

<script>
import { NOT_FOUND, UNAUTHORIZED, INTERNAL_SERVER_ERROR } from "./util.js";
// import Message from './layouts/Message.vue';
// import Header from './layouts/Header.vue';
import Footer from './layouts/footer.vue';

export default {
  components: {
    // Message,
    // Header,
    Footer
  },
  // computed: {
  //   // エラー時のステータスコード番号
  //   errorCode() {
  //     return this.$store.state.error.errorCode;
  //   }
  // },
  // watch: {
  //   // ストアのerrorCodeステートを監視
  //   errorCode: {
  //     async handler(val) {
  //
  //       switch (val){
  //         case UNAUTHORIZED:
  //           console.log('resources/js/App.vue : 認証切れです')
  //           // トークンリフレッシュ
  //           await axios.get('/api/refresh-token');
  //           // ストアのuserをクリアする
  //           this.$store.commit('auth/setUser', null);
  //           // ログイン画面へ遷移
  //           this.$router.push('/login').catch(err => {});
  //           break;
  //
  //         case INTERNAL_SERVER_ERROR:
  //           this.$router.push('/500');
  //           break;
  //
  //         case NOT_FOUND:
  //           this.$router.push('/404');
  //           break;
  //       }
  //     },
  //     // 最初の読み込みの段階からこのハンドラーは実行される
  //     immediate: true
  //   },
  //   // 何もなければErrorCodeにnullを代入
  //   $route() {
  //     this.$store.commit('error/setErrorCode', null);
  //   }
  // }
}

</script>