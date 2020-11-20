<!--===============-->
<!--トレンドランキング-->
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

      <!-- ランキング -->
      <div class="p-trends__list">
        <!-- 検索中 -->
        <div v-if="isSearching" class="">
          <Loading />
        </div>
        <!-- ランキングパネル -->
<!--        <News-->
<!--            v-else-->
<!--            v-for="News in fetchedNews"-->
<!--            :key="News.id"-->
<!--            :entry="News"-->
<!--        />-->
        <Ranking />
        <Ranking />
        <Ranking />

      </div>

    </div>

  </div>

</template>

<script>
import Loading from '../../components/Loading.vue';
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import Ribbonnav from '../Components/Ribbonnav.vue';
import Ranking from './Ranking.vue';
import { OK } from "../../util";
import { mapState } from 'vuex';

const PAGE_TITLE = 'トレンド通貨・ツイート数ランキング';

// 必要情報
// ・月・日・週のツイート数(それぞれについて)
// 最大通貨
// 通貨情報

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
    // TODO リボンタグ用・このcomputed自体は削除予定
    today() {
      return new Date();
    }
  },
  components: {
    Loading,
    SiteLinknav,
    PageTitle,
    Ribbonnav,
    Ranking
  },
  methods: {
    async get_trend() {
      console.log('GET!')
    }
  }
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