<!--==========-->
<!--ログイン画面-->
<!--==========-->
<template>
  <div class="l-container--authform p-auth">
    <h2 class="p-auth__title">ログイン</h2>
    <form class="p-form" @submit.prevent="login">

      <label for="email">メールアドレス</label>

<!--      エラー表示は要修正-->
      <div v-if="loginErrors">
        <span v-if="loginErrors.email">{{ loginErrors.email[0] }}</span>
      </div>
      <input type="text" class="p-form__item" id="email" v-model="loginForm.email">

      <label for="password">パスワード (半角英数字 8文字以上) </label>
      <div v-if="loginErrors">
        <span v-if="loginErrors.password">{{ loginErrors.password[0] }}</span>
      </div>
      <input type="password" class="p-form__item" id="password" v-model="loginForm.password">

      <div class="p-auth__checkbox">
        <input type="checkbox" id="remember" v-model="loginForm.remember">
        <label for="remember">ログイン状態を維持する</label>
      </div>

      <button type="submit" class="c-btn c-btn__main c-btn--primary">ログイン</button>
    </form>

    <RouterLink class="c-btn c-btn--primary" to="/password/reset">パスワードを忘れた方はこちら</RouterLink>

    <div class="p-auth__dividingText">
      <span class="p-auth__dividingText-spanborder">または</span>
    </div>
    <a
        class="c-btn c-btn--primary c-btn__twitter--login"
        title="Start for Twitter!"
        @click.stop
        :href="`/twitter/auth/begin`"
    >Twitterで始める</a>

    <h2>アカウントをまだ作成していない方は</h2>
    <RouterLink class="c-btn c-btn--primary" to="/register">新規アカウント登録</RouterLink>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  data() {
    return {
      loginForm: {
        email: '',
        password: '',
        remember: false
      }
    }
  },
  methods: {
    // ログイン
    async login() {
      await this.$store.dispatch('auth/login', this.loginForm);

      // apiStatusがtrue(ステータスコードが200)の時
      if(this.apiStatus) {

        // フラッシュメッセージテスト
        this.$store.commit('message/setContent', {
          content: 'ログインしました！'
        });

        // トップページへ遷移
        this.$router.push('/');
      }
    },
    // エラーメッセージをクリアする。ページ表示のタイミングで呼び出す。
    clearError() {
      this.$store.commit('auth/setLoginErrorMessages', null)
    }
  },
  computed: {
    ...mapState({
      apiStatus: state => state.auth.apiStatus,
      loginErrors: state => state.auth.loginErrorMessages
    })
  },
  // ページが表示されるタイミングで、エラーメッセージをクリアする。
  created() {
    this.clearError()
  }
}
</script>
