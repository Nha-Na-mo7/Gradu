<template>
  <nav class="p-navbar">
    <RouterLink to="/">CryptoTrend(ロゴ)</RouterLink>
    <div class="p-navbar__menu">
      <div class="p-navbar__item">
        <RouterLink class="" to="/trends">トレンド通貨ランキング</RouterLink>
      </div>
      <div class="p-navbar__item">
        <RouterLink class="" to="/news">ニュース</RouterLink>
      </div>
      <div class="p-navbar__item">
        <RouterLink class="" to="/accounts">Twitterアカウント一覧</RouterLink>
      </div>
      <div class="p-navbar__item">
        <RouterLink class="" to="/">アカウント設定</RouterLink>
      </div>
      <div class="p-navbar__item" v-if="isLogin">
        <button class="" @click="logout">ログアウト</button>
      </div>
      <div class="p-navbar__item" v-else>
        <RouterLink class="" to="/login">ログイン</RouterLink>
      </div>
      <div class="p-navbar__item" v-if="!isLogin">
        <RouterLink class="" to="/register">新規登録</RouterLink>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  computed: {
    isLogin() {
      return this.$store.getters['auth/loginCheck'];
    }
  },
  methods: {
    async logout() {
      await this.$store.dispatch('auth/logout');

      this.$router.push('/login');
    }
  }
}
</script>

<style scoped>

</style>