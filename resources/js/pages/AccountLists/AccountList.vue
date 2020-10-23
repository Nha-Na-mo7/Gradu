<!--===============-->
<!--アカウント一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">

    <!-- サイトリンク -->
    <SiteLinknav :currentPageTitle='pageTitle'/>

    <!-- ページタイトル -->
    <PageTitle :title='pageTitle'/>

    <!--メインレイアウト-->
    <div class="p-accounts__container">

      <!-- リボンタグ -->
      <div class="c-ribbon">

        <p class="c-ribbon__content c-ribbon__content--left">Twitterアカウント一覧</p>
        <p class="c-ribbon__content c-ribbon__content--right">最終更新: <span class="c-ribbon__date">2020/12/31 09:00</span> JST</p>
      </div>

      <!-- ヘッドライン -->
      <div class="p-accounts__headline">

        <!-- 自動フォローボタンの位置確認 -->
        <div class="p-news__modal p-news__modal-show">
          <button class="c-btn c-btn__main c-btn--primary">自動フォロー</button>
        </div>
      </div>

      <!-- アカウントリスト -->
      <div class="p-accounts__list">
        <Account />
        <Account />
        <Account />
      </div>

    </div>

  </div>

</template>

<script>
import Account from './Account.vue';
import Loading from '../../components/Loading.vue';
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import { OK , DEFAULT_SEARCHWORD } from "../../util";
import { mapState } from 'vuex';

const PAGE_TITLE = '仮想通貨アカウント一覧';

export default {
  computed: {
    pageTitle(){
      return PAGE_TITLE;
    },
  },
  components: {
    Account,
    Loading,
    SiteLinknav,
    PageTitle
  },
  // watch: {
  //   $route: {
  //     async handler() {
  //       // ページの読み込み直後、Twitterアカウント一覧を取得
  //       await this.fetch_TwitterAccount();
  //     },
  //     immediate: true
  //   }
  // }

}
</script>

<style scoped>
.c-ribbon {
  height: 40px;
  width: 100%;
  color: #FFF;
  font-size: 20px;
  margin-bottom: 15px;
  background: #1d7ebe;
  border-radius: 4px;

  position: relative;
}
.c-ribbon__content {
  position: absolute;
  padding: 8px 20px;
}
.c-ribbon__content--left {
  left: 0;
}
.c-ribbon__content--right {
  right: 0;
}

.c-ribbon__date {
  font-weight: bold;
}
</style>