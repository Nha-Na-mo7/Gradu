<!--==========-->
<!--新規登録画面-->
<!--==========-->
<template>
  <div class="l-container--authform p-auth">
    <h2 class="p-auth__title">アカウントを作成</h2>
    <p>CryptoTrendは、Twitterと連携することでサービスを最大限に利用することが可能です！</p>
    <form class="p-form" @submit.prevent="register" autocomplete="off">



      <label for="name">ユーザーネーム</label>
      <!--      エラー表示は要修正-->
      <div v-if="registerErrors">
        <span v-if="registerErrors.name">{{ registerErrors.name[0] }}</span>
      </div>
      <input type="text" class="p-form__item" id="name" v-model="register_form.name">


      <label for="email">メールアドレス</label>
      <!--      エラー表示は要修正-->
      <div v-if="registerErrors">
        <span v-if="registerErrors.email">{{ registerErrors.email[0] }}</span>
      </div>
      <input type="text" class="p-form__item" id="email" v-model="register_form.email">

      <label for="password">パスワード (半角英数字 8~50文字) </label>
      <!--      エラー表示は要修正-->
      <div v-if="registerErrors">
        <span v-if="registerErrors.password">{{ registerErrors.password[0] }}</span>
      </div>
      <input type="password" class="p-form__item" id="password" v-model="register_form.password">

      <label for="password_confirmation">パスワードの再入力</label>
      <!--      エラー表示は要修正-->
      <div v-if="registerErrors">
        <span v-if="registerErrors.password_confirmation">{{ registerErrors.password_confirmation[0] }}</span>
      </div>
      <input type="password" class="p-form__item" id="password_confirmation" v-model="register_form.password_confirmation">

      <button type="submit" class="c-btn c-btn__main c-btn--primary">新規登録</button>
    </form>

    <div class="p-auth__dividingText">
      <span class="p-auth__dividingText-spanborder">または</span>
    </div>

    <a
        class="c-btn c-btn--primary c-btn__twitter--login"
        title="Start for Twitter!"
        @click.stop
        :href="`/twitter/auth/begin`"
    >Twitterで新規登録</a>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  data() {
    return {
      register_form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      }
    }
  },
  methods: {
    async register() {
      // authStoreからregisterアクションを呼ぶ
      await this.$store.dispatch('auth/register', this.register_form);

      // apiStatusがtrueなら登録完了画面へ遷移
      if(this.apiStatus) {
        this.$router.push('/mypage');
      }
    },
    // エラーメッセージをクリアする。ページ表示のタイミングで呼び出す。
    clearError() {
      this.$store.commit('auth/setRegisterErrorMessages', null)
    }
  },
  computed: {
    ...mapState({
      apiStatus: state => state.auth.apiStatus,
      registerErrors: state => state.auth.registerErrorMessages
    })
  },
  // ページが表示されるタイミングで、エラーメッセージをクリアする。
  created() {
    this.clearError()
  }
}
</script>

<style scoped>

</style>