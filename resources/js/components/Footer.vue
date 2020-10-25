<template>
  <div class="container">
    <div class="p-footer__main">
      <div class="p-footer__item">
        <a href="">ロゴマーク</a>
      </div>
      <div class="p-footer__item">
        <h4>サービス</h4>
        <ul class="p-footer__menus">
          <li><RouterLink class="" to="/trends">トレンド通貨ランキング</RouterLink></li>
          <li><RouterLink class="" to="/news">ニュース</RouterLink></li>
          <li><RouterLink class="" to="/accounts">Twitterアカウント一覧</RouterLink></li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
name: "Footer.vue"
}
</script>

<style scoped>

</style>