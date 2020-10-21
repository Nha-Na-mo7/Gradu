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

      <!-- ヘッドライン -->
      <div class="p-news__headline">
        <!-- 検索フォーム・コンポーネント検討 -->
        <form class="p-news__search">

          <!-- 検索虫眼鏡ボタン -->
          <div class="c-input__btn-area c-input__btn-area__search">
            <button class="c-input__btn-circle">🔎</button>
          </div>
          <!-- 検索欄 -->
          <div class="c-input__searcharea">
            <!--            <input type="text" class="c-input" v-model="searchBoxWords" :placeholder="placeholder">-->
          </div>

        </form>

        <!-- 絞り込みモーダルボタン -->
        <div class="p-news__modal p-news__modal-show">
          <button class="c-btn c-btn__main c-btn--primary">自動フォロー</button>
        </div>
      </div>

      </div>

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
.p-accounts__list {
  margin: 20px 30px;
}

</style>