<!--===============-->
<!--トレンドランキング-->
<!--===============-->
<template>
  <div class="l-container__content">
    <!-- ページタイトル -->
    <PageTitle :title="pageTitle" />

    <!-- 切り替えタブによってメインレイアウトを入れ替える-->
    <div class="p-panel">
      <!--メインレイアウト-->
      <div class="p-trends">
        <!-- リボンタグ -->
        <Ribbonnav :title="ribbonPageTitle" :date="get_updated_at" />

        <!-- 絞り込みエリア -->
        <div class="p-trends__modal p-trends__modal-show">
          <TrendCheckbox @checked="checkedBrand" @reset="resetBrand" />
        </div>

        <!-- 切り替えタブ -->
        <ul class="p-trends__tab c-tab">
          <li
            class="c-tab__item"
            @click="tab = 0"
            v-bind:class="{ 'c-tab__item--active': tab === 0 }"
          >
            過去1時間
          </li>
          <li
            class="c-tab__item"
            @click="tab = 1"
            v-bind:class="{ 'c-tab__item--active': tab === 1 }"
          >
            過去1日
          </li>
          <li
            class="c-tab__item"
            @click="tab = 2"
            v-bind:class="{ 'c-tab__item--active': tab === 2 }"
          >
            過去1週間
          </li>
        </ul>

        <!-- ランキングの右上にも更新時刻を書く -->
        <div class="p-trends__date">
          <p>最終更新:{{ this.get_updated_at }} JST</p>
        </div>

        <!-- スマホ表示の時のみ、注意事項が出る -->
        <div class="p-trends__sp">
          <p>※ 左右にスワイプして情報を確認できます。</p>
        </div>

        <!-- ランキング -->
        <div class="p-trends__list">
          <!-- 検索中 -->
          <div v-if="isLoadingStatus" class="">
            <Loading />
          </div>

          <div v-else-if="isNothingStatus">
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
                <TrendRankCard
                  v-show="tab === 0"
                  v-for="(trend_brand, index) in sortTweetCountDesc(0)"
                  :key="trend_brand.id"
                  :brand="trend_brand"
                  :rank="index"
                  :transaction="brandsTransactionPrice(trend_brand.brand_id)"
                />
                <TrendRankCard
                  v-show="tab === 1"
                  v-for="(trend_brand, index) in sortTweetCountDesc(1)"
                  :key="trend_brand.id"
                  :brand="trend_brand"
                  :rank="index"
                  :transaction="brandsTransactionPrice(trend_brand.brand_id)"
                />
                <TrendRankCard
                  v-show="tab === 2"
                  v-for="(trend_brand, index) in sortTweetCountDesc(2)"
                  :key="trend_brand.id"
                  :brand="trend_brand"
                  :rank="index"
                  :transaction="brandsTransactionPrice(trend_brand.brand_id)"
                />
              </table>
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
import TrendCheckbox from './TrendCheckbox.vue';
import PageTitle from '../PageComponents/PageTitle.vue';
import Ribbonnav from '../PageComponents/Ribbonnav.vue';
import TrendRankCard from './TrendRankCard.vue';
import { OK } from '../../util';

const PAGE_TITLE = 'トレンド通貨・ツイート数ランキング';

export default {
  data() {
    return {
      isLoading: false,
      isNothing: false,
      tab: 0,
      trendDataHour: [],
      trendDataDay: [],
      trendDataWeek: [],
      checkedBrands: [],
      transactions: [],
    };
  },
  computed: {
    pageTitle() {
      return PAGE_TITLE;
    },
    isLoadingStatus() {
      return this.isLoading;
    },
    isNothingStatus() {
      return this.isNothing;
    },
    ribbonPageTitle() {
      let title = '';
      if (this.tab === 0) {
        title = '過去1時間のトレンド';
      } else if (this.tab === 1) {
        title = '過去1日でのトレンド';
      } else {
        title = '過去1週間でのトレンド';
      }
      return title;
    },
    // それぞれに対応した配列を返す
    choiceTrendData: function () {
      return function (type) {
        let items;
        switch (type) {
          case 0:
            items = this.trendDataHour;
            break;
          case 1:
            items = this.trendDataDay;
            break;
          case 2:
            items = this.trendDataWeek;
            break;
        }
        return items;
      };
    },
    // 指定のidの銘柄に絞った配列を返却する(チェックされた通貨に絞る)
    refineBrands: function () {
      return function (array, checked) {
        // 何もチェックされていない時は絞り込まない
        if (checked.length === 0) {
          return array;
        } else {
          const result = array.filter((brand) => {
            // チェックボックスで選択された値と同じ名前の通貨だけを抽出する
            return checked.indexOf(brand.brand.name) !== -1;
          });
          return result;
        }
      };
    },
    // ツイート数が多い順に並び替えたデータを返却する
    sortTweetCountDesc: function () {
      return function (type) {
        const checkedbox = this.checkedBrands;
        let items = this.choiceTrendData(type);
        let refined = this.refineBrands(items, checkedbox);
        const sortedItem = refined.slice().sort(function (a, b) {
          return b.tweet_count - a.tweet_count;
        });
        return sortedItem;
      };
    },
    // 配列の最初にある更新時刻を最終更新時刻として表示する
    get_updated_at() {
      let items = this.choiceTrendData(this.tab);
      if (items.length === 0) {
        return '';
      }
      return items[0].updated_at;
    },
    // 取引価格オブジェクトを格納した配列から取り出す
    brandsTransactionPrice: function () {
      return function (brand_id) {
        // インデックスに合わせて-1する
        return this.transactions[brand_id - 1];
      };
    },
  },
  methods: {
    // 指定した時間帯のトレンドテーブルを取得する
    async fetchTrend(type) {
      // トレンド一覧を取得
      const response = await axios
        .get(`/tweet/count`, { params: { type: type } })
        .catch((error) => error.response || error);

      // 通信成功時
      if (response.status === OK) {
        // それぞれのトレンドデータに格納
        switch (type) {
          case 0:
            this.trendDataHour = response.data;
            break;
          case 1:
            this.trendDataDay = response.data;
            break;
          case 2:
            this.trendDataWeek = response.data;
            break;
        }
      } else {
        // 何らかの理由でエラーが出た場合は、トレンドが取得できなかった旨を表示する
        this.isNothing = true;
      }
    },
    // 上記のfetchTrendを、時間・日・週の全てで取得する
    async allFetch() {
      if (this.isLoading) {
        return false;
      }
      // 読み込みをtrueに
      this.isLoading = true;

      for (let type = 0; type <= 2; type++) {
        this.fetchTrend(type);
      }
      // 24時間以内の取引価格取得
      this.getTransactionPrice();

      // 読み込み中を解除
      this.isLoading = false;
    },
    // 24時間以内の取引価格の取得
    async getTransactionPrice() {
      const response = await axios.get(`/transaction/price`);

      // 通信成功時
      if (response.status === OK) {
        this.transactions = response.data;
      }
    },
    //チェックボックスでチェックされた値を格納
    checkedBrand(array) {
      this.resetBrand();
      this.checkedBrands = array;
    },
    // チェックボックスがリセットされた時の処理
    resetBrand() {
      this.checkedBrands = [];
    },
  },
  components: {
    Loading,
    PageTitle,
    Ribbonnav,
    TrendRankCard,
    NothingTrends,
    TrendCheckbox,
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、トレンド一覧を取得
        await this.allFetch();
      },
      immediate: true,
    },
  },
};
</script>

<style scoped></style>
