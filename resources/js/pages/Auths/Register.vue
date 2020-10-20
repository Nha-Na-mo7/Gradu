<!--==========-->
<!--新規登録画面-->
<!--==========-->
<template>
  <div class="l-container--authform p-auth">
    <h2 class="p-auth__title">アカウントを作成</h2>
    <p>CryptoTrendは、Twitterと連携することでサービスを最大限に利用することが可能です！</p>
    <form class="p-form" @submit.prevent="register" autocomplete="off">



      <label for="name">名前</label>
      <!--      エラー表示は要修正-->
      <div v-if="registerErrors">
        <span v-if="registerErrors.name">{{ registerErrors.name[0] }}</span>
      </div>
      <input type="text" class="p-form__item" id="name" v-model="registerForm.name">


      <label for="email">メールアドレス</label>
      <!--      エラー表示は要修正-->
      <div v-if="registerErrors">
        <span v-if="registerErrors.email">{{ registerErrors.email[0] }}</span>
      </div>
      <input type="text" class="p-form__item" id="email" v-model="registerForm.email">

      <label for="password">パスワード (半角英数字 8文字以上) </label>
      <!--      エラー表示は要修正-->
      <div v-if="registerErrors">
        <span v-if="registerErrors.password">{{ registerErrors.password[0] }}</span>
      </div>
      <input type="password" class="p-form__item" id="password" v-model="registerForm.password">

      <label for="password_confirmation">パスワードの再入力</label>
      <!--      エラー表示は要修正-->
      <div v-if="registerErrors">
        <span v-if="registerErrors.password_confirmation">{{ registerErrors.password_confirmation[0] }}</span>
      </div>
      <input type="password" class="p-form__item" id="password_confirmation" v-model="registerForm.password_confirmation">

      <button type="submit" class="c-btn c-btn__main c-btn--primary">新規登録</button>
    </form>

    <div class="p-auth__dividingText">
      <span class="p-auth__dividingText-spanborder">または</span>
    </div>

    <!-- @click.stopは現状@clickを持つ親コンポーネントで囲んでいない為無意味だが、どう転ぶか不明なので、不要になった段階で外してください-->
    <a
        class="c-btn c-btn--primary c-btn__twitter--login"
        title="Start for Twitter!"
        @click.stop
        :href="`/login/twitter`"
    >Twitterでログイン</a>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  data() {
    return {
      registerForm: {
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
      await this.$store.dispatch('auth/register', this.registerForm);

      // apiStatusがtrueなら登録完了画面へ遷移
      if(this.apiStatus) {
        this.$router.push('/registerCompletion');
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