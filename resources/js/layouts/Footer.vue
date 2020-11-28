<template>
  <div class="p-footer__container">
    <!-- ロゴとナビ -->
    <div class="p-footer__main item1">
      <div class="p-footer__logo item2">
        <a href="">ロゴマーク</a>
      </div>

      <!-- サービス -->
      <div class="p-footer__item item2">
        <h4 class="p-footer__item--title">サービス</h4>
        <ul class="p-footer__menus">
          <li><RouterLink class="" to="/trends">トレンド通貨ランキング</RouterLink></li>
          <li><RouterLink class="" to="/news">ニュース</RouterLink></li>
          <li><RouterLink class="" to="/accounts">Twitterアカウント一覧</RouterLink></li>
        </ul>
      </div>

      <!-- サポート -->
      <div class="p-footer__item item2">
        <h4 class="p-footer__item--title">サポート</h4>
        <ul class="p-footer__menus">
          <li><RouterLink class="" to="">利用規約</RouterLink></li>
          <li><RouterLink class="" to="">プライバシーポリシー</RouterLink></li>
          <li><RouterLink class="" to="">お問い合わせ</RouterLink></li>
        </ul>
      </div>
    </div>
    <!-- Copyright -->
    <div class="p-footer__copyright item1">
      <span>Copyright © CryptoTrend 2020 All Rights Reserved.</span>
    </div>
  </div>
</template>

<script>

export default {
  methods: {

  }
}
</script>

<style scoped>

</style>