<!--===============-->
<!--トレンドランキング-->
<!--===============-->
<template>
  <div class="l-container__content">

    <!-- サイトリンク -->
    <SiteLinknav :currentPageTitle='page_title'/>

    <!-- ページタイトル -->
    <PageTitle :title='page_title'/>

    <!-- 切り替えタブ -->
    <ul class="tab">
      <li class="tab__item" @click="tab = 1" v-bind:class="{'tab__item--active': tab === 1}">過去1時間のトレンド</li>
      <li class="tab__item" @click="tab = 2" v-bind:class="{'tab__item--active': tab === 2}">過去1日のトレンド</li>
      <li class="tab__item" @click="tab = 3" v-bind:class="{'tab__item--active': tab === 3}">過去1週間のトレンド</li>
    </ul>

    <!-- 切り替えタブによってメインレイアウトを入れ替える-->
    <div class="p-panel">

      <!--メインレイアウト-->
      <div class="p-accounts__container" v-bind:class="content_bgcolor">
        <!-- リボンタグ -->
        <Ribbonnav
            :title='ribbon_page_title'
            :date='today'
        />

        <!-- ランキング -->
        <div class="p-trends__list">
          <!-- 検索中 -->
          <div v-if="isSearching" class="">
            <Loading />
          </div>
          ランキングパネル
          <!--        <Ranking-->
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
      isSearching: false,
      tab: 1
    }
  },
  computed: {
    page_title() {
      return PAGE_TITLE;
    },
    content_bgcolor() {
      let bgcolor = ''
      if(this.tab === 1){
        bgcolor = ''
      }else if(this.tab === 2){
        bgcolor = 'u-bg-green'
      }else{
        bgcolor = 'u-bg-purple'
      }
      return bgcolor;
    },
    ribbon_page_title(){
      let title = '';
      if(this.tab === 1){
        title = '過去1時間のトレンド'
      }else if(this.tab === 2){
        title = '過去1日でのトレンド'
      }else{
        title = '過去1週間でのトレンド'
      }
      return title;
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