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
    <ul class="c-tab">
      <li class="c-tab__item" @click="tab = 0" v-bind:class="{'tab__item--active': tab === 0}">過去1時間のトレンド</li>
      <li class="c-tab__item" @click="tab = 1" v-bind:class="{'tab__item--active': tab === 1}">過去1日のトレンド</li>
      <li class="c-tab__item" @click="tab = 2" v-bind:class="{'tab__item--active': tab === 2}">過去1週間のトレンド</li>
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
      tab: 0,
      trend_data: [],
      search_data: {
        type: 1
      },
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
    async fetch_trend() {
      // 読み込み中ならこのメソッドは発火しない
      if(this.isLoading) {
        return false;
      }
      // 読み込みをtrueに
      this.isLoading = true;

      // パラメータ設定
      const type = this.search_data
      // トレンド一覧を取得
      const response = await axios.get(`/api/tweet/count`, { type });

      console.log(response.data)
    }
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、トレンド一覧を取得
        await this.fetch_trend();
      },
      immediate: true
    }
  }

}

</script>

<style scoped>

</style>