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
      <li class="c-tab__item u-bg-blue" @click="tab = 0" v-bind:class="{'tab__item--active': tab === 0}">過去1時間のトレンド</li>
      <li class="c-tab__item u-bg-green" @click="tab = 1" v-bind:class="{'tab__item--active': tab === 1}">過去1日のトレンド</li>
      <li class="c-tab__item u-bg-purple" @click="tab = 2" v-bind:class="{'tab__item--active': tab === 2}">過去1週間のトレンド</li>
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
          <div
              class="p-trends__list--container"
              v-else>
          </div>
          <Ranking
              v-show="tab === 0"
              v-for="trend_brand in sort_tweet_count_desc(0)"
              :key="trend_brand.id"
              :brand="trend_brand"
          />
          <Ranking
              v-show="tab === 1"
              v-for="trend_brand in sort_tweet_count_desc(1)"
              :key="trend_brand.id"
              :brand="trend_brand"
          />
          <Ranking
              v-show="tab === 2"
              v-for="trend_brand in sort_tweet_count_desc(2)"
              :key="trend_brand.id"
              :brand="trend_brand"
          />

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
      trend_data_hour: [],
      trend_data_day: [],
      trend_data_week: [],
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
      if(this.tab === 0){
        bgcolor = ''
      }else if(this.tab === 1){
        bgcolor = 'u-bg-green'
      }else{
        bgcolor = 'u-bg-purple'
      }
      return bgcolor;
    },
    ribbon_page_title(){
      let title = '';
      if(this.tab === 0){
        title = '過去1時間のトレンド'
      }else if(this.tab === 1){
        title = '過去1日でのトレンド'
      }else{
        title = '過去1週間でのトレンド'
      }
      return title;
    },
    // ツイート数が多い順に並び替えたデータを返却する
    sort_tweet_count_desc: function(){
      return function(type) {
        let items;
        switch (type) {
          case 0:
            items = this.trend_data_hour
            break;
          case 1:
            items = this.trend_data_day
            break;
          case 2:
            items = this.trend_data_week
            break;
        }
        const sorted_item = items.slice().sort(function (a, b) {
          return b.tweet_count - a.tweet_count
        });
        // console.log(sorted_item)
        return sorted_item;
      }
    },
    // TODO 配列の最初にある更新時刻を最終更新時刻として表示する
    get_updated_at() {

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
    async fetch_trend(type) {
      // トレンド一覧を取得
      const response = await axios.get(`/api/tweet/count`, { params:{type: type} });

      // それぞれのトレンドデータに格納
      switch (type) {
        case 0:
          this.trend_data_hour = response.data;
          break;
        case 1:
          this.trend_data_day = response.data;
          break;
        case 2:
          this.trend_data_week = response.data;
          break;
      }
    },
    // 上記のfetch_trendを、時間・日・週の全てで取得する
    async all_fetch() {
      if(this.isLoading) {
        return false;
      }
      // 読み込みをtrueに
      this.isLoading = true;

      for (let type = 0;type <= 2;type++){
        this.fetch_trend(type);
      }

      // 読み込み中を解除
      this.isLoading = false;
    }
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、トレンド一覧を取得
        await this.all_fetch();
      },
      immediate: true
    }
  }

}

</script>

<style scoped>
.p-trends__list--container{
  transition: .5s;

}
</style>