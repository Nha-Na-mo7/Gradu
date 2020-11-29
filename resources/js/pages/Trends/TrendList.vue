<!--===============-->
<!--トレンドランキング-->
<!--===============-->
<template>
  <div class="l-container__content">

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
            :date='get_updated_at'
        />

        <!-- 絞り込みアコーディオンエリア -->
        <div class="p-news__modal p-news__modal-show">
          <button class="c-btn c-btn__main c-btn--primary" @click="show_accordion">設定</button>
          <SearchAccordion
            @checked="checked_brand"
            @reset="reset_brand"
          />
        </div>

        <!-- ランキング -->
        <div class="p-trends__list">
          <!-- 検索中 -->
          <div v-if="isLoading_status" class="">
            <Loading />
          </div>

          <div v-else-if="isNothing_status">
            <NothingTrends />
          </div>

          <div v-else class="p-trends__table__area">
            <div class="p-trends__table">
              <table class="p-trends__table--inner">
                <tr>
                  <th class="">順位</th>
                  <th>通貨</th>
                  <th>ツイート数</th>
                  <th>最高取引価格（24H）</th>
                  <th>最安取引価格（24H）</th>
                </tr>
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

              </table>
            </div>
          </div>
          </div>

        </div>

      </div>

    </div>

  </div>

</template>

<script>
import Loading from '../../layouts/Loading.vue';
import NothingTrends from './NothingTrends.vue';
import SearchAccordion from './SearchAccordion.vue';
import PageTitle from '../PageComponents/PageTitle.vue';
import Ribbonnav from '../PageComponents/Ribbonnav.vue';
import Ranking from './Ranking.vue';
import { OK } from "../../util";

const PAGE_TITLE = 'トレンド通貨・ツイート数ランキング';

export default {
  data() {
    return {
      accordion: false,
      isLoading: false,
      isNothing: false,
      tab: 0,
      trend_data_hour: [],
      trend_data_day: [],
      trend_data_week: [],
      checked_brands: [],
      search_data: {
        type: 1
      },
    }
  },
  computed: {
    page_title() {
      return PAGE_TITLE;
    },
    isLoading_status(){
      return this.isLoading
    },
    isNothing_status(){
      return this.isNothing
    },
    content_bgcolor() {
      let bgcolor = ''
      switch (this.tab){
        case 0:
          bgcolor = ''
          break;
        case 1:
          bgcolor = 'u-bg-green'
          break;
        case 2:
          bgcolor = 'u-bg-purple'
          break;
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
    // それぞれに対応した配列を返す
    choice_trend_data: function() {
      return function (type) {
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
        return items;
      }
    },
    // 指定のidの銘柄に絞った配列を返却する(チェックされた通貨に絞る)
    refine_brands: function(){
      return function(array, checked) {
        // 何もチェックされていない時は絞り込まない
        if(checked.length === 0) {
          return array;
        }else{
          const result = array.filter((brand) => {
            // チェックボックスで選択された値と同じ名前の通貨だけを抽出する
            return checked.indexOf(brand.brand.name) !== -1;
          })
          return result;
        }
      }
    },
    // ツイート数が多い順に並び替えたデータを返却する
    sort_tweet_count_desc: function(){
      return function(type) {
        const checkedbox = this.checked_brands;
        let items = this.choice_trend_data(type);
        let refined = this.refine_brands(items, checkedbox);
        const sorted_item = refined.slice().sort(function (a, b) {
          return b.tweet_count - a.tweet_count
        });
        return sorted_item;
      }
    },

    // 配列の最初にある更新時刻を最終更新時刻として表示する
    get_updated_at() {
      let items = this.choice_trend_data(this.tab)
      if(items.length === 0){
        return ''
      }
      return items[0].updated_at;
    },
  },
  methods: {
    // 指定した時間帯のトレンドテーブルを取得する
    async fetch_trend(type) {
      // トレンド一覧を取得
      const response = await axios
          .get(`/tweet/count`, { params:{type: type} })
          .catch(error => error.response || error);

      // 通信成功時
      if (response.status === OK) {
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
      }else{
        // 何らかの理由でエラーが出た場合は、トレンドが取得できなかった旨を表示する
        this.isNothing = true;
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
    },
    // アコーディオンを開く
    show_accordion(){
      this.accordion = true;
    },
    // アコーディオンを閉じる
    close_accordion(){
      this.accordion = false;
    },
    // アコーディオンでチェックされた値を格納
    checked_brand(array) {
      this.reset_brand();
      this.checked_brands = array;
    },
    // アコーディオンがリセットされた時の処理
    reset_brand(){
      this.checked_brands = [];
    }
  },
  components: {
    Loading,
    PageTitle,
    Ribbonnav,
    Ranking,
    NothingTrends,
    SearchAccordion
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