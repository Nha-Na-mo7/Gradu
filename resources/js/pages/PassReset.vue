<!--==========-->
<!--パスリマインド-->
<!--==========-->

<!--未実装-->
<!--#メールの送信メソッド-->
<!--#送信後にフラッシュメッセージで送信した旨を通知する-->

<template>
  <div class="l-container--authform">
    <h2 class="p-auth__title">パスワードを忘れた場合</h2>
    <p>アカウント作成時にご登録いただいたメールアドレスを入力してください。</p>
    <p>入力したメールアドレス宛に、パスワード変更ページのURLが記載されたメールを送信します。</p>
    <form class="p-form" @submit.prevent="resetMailSubmit">
      <label for="email">メールアドレス</label>
      <input type="text" class="p-form__item" id="email" v-model="email">

      <button type="submit" class="c-btn c-btn__main c-btn--primary">送信する</button>
    </form>

  </div>
</template>

<script>
export default {
  data() {
    return {
      email: ''
    }
  },
  methods: {
    resetMailSubmit() {
      alert(this.email);
    }
  }
}
</script>

<style scoped>

</style>