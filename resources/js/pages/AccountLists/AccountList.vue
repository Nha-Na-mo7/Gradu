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
    <Account />
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

</style>