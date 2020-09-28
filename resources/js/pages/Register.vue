<!--==========-->
<!--新規登録画面-->
<!--==========-->
<template>
  <div class="l-container--authform p-auth">
    <h2 class="p-auth__title">アカウントを作成</h2>
    <form class="p-form" @submit.prevent="register" autocomplete="off">
      <label for="email">メールアドレス</label>
      <input type="text" class="p-form__item" id="email" v-model="registerForm.email">

      <label for="password">パスワード (半角英数字 8文字以上) </label>
      <input type="password" class="p-form__item" id="password" v-model="registerForm.password">

      <label for="password-confirmation">パスワードの再入力</label>
      <input type="password" class="p-form__item" id="password-confirmation" v-model="registerForm.password_confirmation">

      <button type="submit" class="c-btn c-btn__main c-btn--primary">新規登録</button>
    </form>

    <div class="p-auth__dividingText">
      <span class="p-auth__dividingText-spanborder">または</span>
    </div>
    <p>Twitterで新規登録</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      registerForm: {
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

      // トップページへ遷移 遷移先は要修正
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>

</style>