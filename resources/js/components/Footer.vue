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
        <button class="c-btn" @click="check_limit_status">[開発用]<br>check_limit_status</button>
      </div>
    </div>
    <!-- Copyright -->
    <div class="p-footer__copyright item1">
      <span>Copyright © CryptoTrend 2020 All Rights Reserved.</span>
    </div>
  </div>
</template>

<script>
import {OK} from "../util";

export default {
  methods: {
    async check_limit_status() {
      const response = await axios.get('../api/twitter/check_limit_status');

      // エラー時
      if (response.status !== OK) {
        this.$store.commit('error/setErrorCode', response.status)
        return false
      }

      console.log(response.data.result.resources)
    }
  }
}
</script>

<style scoped>
.item1 {
  /*background: #dd69cd;*/
}
.item2 {
  /*background: #ffcd22;*/
}
.item3 {
  /*background: #82AAFF;*/
}
.p-footer__container {
  margin: 0 auto;
  max-width: 1200px;
  box-sizing: border-box;

  color: #09093e;
}

.p-footer__item {
  width: 25%;
  margin-right: 20px;
}
.p-footer__logo {
  width: 50%;
  height: 100px;
  border-radius: 15px;
  margin-right: 100px;
  background: #82AAFF;
}

.p-footer__main {
  /*-webkit-box-align: center;*/
  /*align-items: center;*/
  display: -webkit-box;
  display: flex;
  -webkit-box-pack: center;
  justify-content: center;

  padding-top: 40px;
  padding-bottom: 40px;
}
.p-footer__item--title {
  font-size: 1.2rem;
  margin-bottom: 15px;
}
.p-footer__menus li {
  padding-top: 10px;
}
.p-footer__menus li :hover {
  text-decoration: underline;
  cursor: pointer;
}

.p-footer__copyright {
  text-align: center;
  font-size: 1rem;
  padding: 25px 0;
}


</style>