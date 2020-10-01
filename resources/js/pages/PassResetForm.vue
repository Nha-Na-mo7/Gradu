<!--==========-->
<!--パスリセットフォーム-->
<!--==========-->

<!--未実装-->

<template>
  <div class="l-container--authform">
    <h2 class="p-auth__title">STEP4 パスワードの再設定</h2>
    <p>ご登録いただいたメールアドレスと、新しく設定するパスワードを入力してください。</p>
    <p>新しいパスワードでそのままログインされます。</p>
    <form class="p-form" @submit.prevent="resetPassword">
      <!-- トークン -->
      <input type="hidden" name="token" value="">

      <label for="email">メールアドレス</label>
      <input type="email" class="p-form__item" id="email" value="" required autocomplete="email" autofocus v-model="resetPasswordForm.email">
      <label for="password">新しいパスワード (半角英数 8文字以上)</label>
      <input type="password" class="p-form__item" id="password" required autocomplete="new-password" v-model="resetPasswordForm.password">
      <label for="password_confirmation">パスワードの再入力</label>
      <input type="password" class="p-form__item" id="password_confirmation" required autocomplete="new-password" v-model="resetPasswordForm.password_confirmation">

      <button type="submit" class="c-btn c-btn__main c-btn--primary">パスワードを変更してログイン</button>
    </form>

  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  data() {
    return {
      resetPasswordForm: {
        email: '',
        password: '',
        password_confirmation: ''
      }
    }
  },
  methods: {
    async resetPassword() {
      // authStoreからregisterアクションを呼ぶ
      await this.$store.dispatch('auth/resetPassword', this.resetPasswordForm);

      // apiStatusがtrueなら遷移
      // if(this.apiStatus) {
      //  alert('送信しますた');
      // }
    },
    // // エラーメッセージをクリアする。ページ表示のタイミングで呼び出す。
    // clearError() {
    //   this.$store.commit('auth/setResetMailErrorMessages', null)
    // }
  },
  computed: {
    ...mapState({
      apiStatus: state => state.auth.apiStatus,
      // resetMailErrors: state => state.auth.resetMailErrorMessage
    })
  },
  // // ページが表示されるタイミングで、エラーメッセージをクリアする。
  // created() {
  //   this.clearError()
  // }

}
</script>

<style scoped>

</style>