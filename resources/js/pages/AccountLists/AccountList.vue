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
.p-accounts__container {
  background: #f5f6ff;
  box-shadow: #f5f6ff 0 0 20px;
  margin-top: 20px;
  padding: 15px 25px;
}
.p-accounts__list {
  margin: 20px 30px;
}

</style>