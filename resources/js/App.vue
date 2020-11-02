<!-- RouterView部分において各コンポーネントが切り替わります -->
<template>
  <div class="l-content-wrapper">
    <!-- ヘッダー -->
    <header id="header" class="l-header">
      <Header />
    </header>

    <!-- メイン -->
    <main>
      <div class="l-container">
        <!-- フラッシュメッセージエリア -->
        <Message />
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
import Message from './components/Message.vue';
import Header from './components/Header.vue';
import Footer from './components/footer.vue';

export default {
  components: {
    Message,
    Header,
    Footer
  },
  computed: {
    // エラー時のステータスコード番号
    errorCode() {
      return this.$store.state.error.errorCode;
    }
  },
  watch: {
    // ストアのerrorCodeステートを監視
    errorCode: {
      async handler(val) {
        // TODO 500エラーが発生した時、500エラー用のコンポーネントに遷移させる、要確認
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/500');
        // 認証エラー
        } else if (val === UNAUTHORIZED) {
          console.log('認証切れです')
          // トークンリフレッシュ
          await axios.get('/api/refresh-token');
          // ストアのuserをクリアする
          this.$store.commit('auth/setUser', null);
          // ログイン画面へ遷移
          this.$router.push('/login');
        // 404エラー
        } else if (val === NOT_FOUND) {
          this.$router.push('/404');
        }
      },
      // 最初の読み込みの段階からこのハンドラーは実行される
      immediate: true
    },
    // 何もなければErrorCodeにnullを代入
    $route() {
      this.$store.commit('error/setErrorCode', null);
    }
  }
}

</script>