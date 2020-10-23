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
      <Ribbonnav
          :title='pageTitle'
          :date='today'
      />

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

      <!-- 検索中 -->
      <div v-if="isSearching" class="">
        <Loading
            :title="searchingWord"
        />
      </div>

    </div>

  </div>

</template>

<script>
import Account from './Account.vue';
import Loading from '../../components/Loading.vue';
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import Ribbonnav from '../Components/Ribbonnav.vue';
import {OK, DEFAULT_SEARCHWORD, SEARCHING} from "../../util";
import { mapState } from 'vuex';

const PAGE_TITLE = '仮想通貨アカウント一覧';

export default {
  data() {
    return {
      isSearching: false
    }
  },
  computed: {
    pageTitle(){
      return PAGE_TITLE;
    },
    searchingWord() {
      return SEARCHING;
    },

    // TODO リボンタグ用・最終更新日を1日1回更新していれる、このcomputed自体は削除予定
    today() {
      return new Date();
    }
  },
  components: {
    Account,
    Loading,
    SiteLinknav,
    PageTitle,
    Ribbonnav
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