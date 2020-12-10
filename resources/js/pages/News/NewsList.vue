<!--===============-->
<!--ニュースの一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">
    <!-- ページタイトル -->
    <PageTitle :title="page_title" />

    <!--メインレイアウト-->
    <div class="p-news">
      <!-- サーチボックス -->
      <div class="p-news__search--inner">
        <div class="p-news__search">
          <div class="p-news__search--item">
            <div class="p-news__search--info"><p>検索条件</p></div>
            <div class="p-news__search--content">
              <!-- 検索ワードを表示するエリア -->
              <div class="p-news__search--content--searchWords">
                <span>{{ defaultAndCheckedBrandsData }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 絞り込み -->
      <div class="p-news__checkbox">
        <NewsSearchCheckbox
          @checked="intoCheckedBrand"
          @reset="resetBrand"
          @search="searchGoogleNews"
        />
      </div>

      <!-- ニュース一覧 -->
      <div id="newslist" class="p-news__list">
        <!-- 検索中 -->
        <div v-if="isSearching" class="">
          <Loading />
        </div>
        <!-- ニュースコンポーネント、検索中は非表示 -->
        <div v-else>
          <News v-for="News in getNewsItems" :key="News.id" :entry="News" />
          <paginate
            v-model="currentPage"
            :page-count="getPageCount"
            :page-range="3"
            :margin-pages="1"
            :click-handler="clickCallback"
            :prev-text="'<'"
            :next-text="'>'"
            :hide-prev-next="true"
            :containerClass="'c-paginate'"
            :page-class="'c-paginate__item'"
            :page-link-class="'c-paginate__link'"
            :prev-class="'c-paginate__item c-paginate__item--prev'"
            :prev-link-class="'c-paginate__link'"
            :next-class="'c-paginate__item c-paginate__item--next'"
            :next-link-class="'c-paginate__link'"
            :active-class="'c-paginate__item--active'"
            list=""
            name=""
          >
          </paginate>
        </div>
      </div>

      <!-- 記事がない時 -->
      <div v-if="isNothingNews">
        <NothingNews />
      </div>
    </div>
  </div>
</template>

<script>
import News from './News.vue';
import NothingNews from './NothingNews.vue';
import NewsSearchCheckbox from './NewsSearchCheckbox.vue';
import Loading from '../../layouts/Loading.vue';
import PageTitle from '../PageComponents/PageTitle.vue';
import { DEFAULT_SEARCHWORD } from '../../util';

import Vue from 'vue';
import Paginate from 'vuejs-paginate';
Vue.component('paginate', Paginate);

const PAGE_TITLE = 'NEWS';

export default {
  data() {
    return {
      isSearching: false,
      // 「検索した結果、記事が無かった」場合にtrueとなるフラグ。
      // ページ読み込み時にも「記事がありません」と表示するのは不自然なためこのようにしている。
      isNothingNews: false,
      searchedNews: [],
      checkedBrandsData: [],
      searchInputData: {
        keywords: '',
      },
      // ページネーション用
      parPage: 10,
      currentPage: 1,
    };
  },
  computed: {
    page_title() {
      return PAGE_TITLE;
    },
    // 仮想通貨 + チェックされたワードを文字列として出力する
    defaultAndCheckedBrandsData() {
      return DEFAULT_SEARCHWORD + ' ' + this.checkedBrandsData.join(' ');
    },
    // 「仮想通貨」とチェックされた通貨名の一覧を、searchInputData.keywordsに格納
    wordsIntoSearchData() {
      this.searchInputData.keywords = this.defaultAndCheckedBrandsData;
    },
    // ======================
    // ページネーション用
    // ======================
    // ページネーション用にニュースリストを細分化する
    getNewsItems: function () {
      let current = this.currentPage * this.parPage;
      let start = current - this.parPage;
      return this.searchedNews.slice(start, current);
    },
    // 総ページ数
    getPageCount: function () {
      return Math.ceil(this.searchedNews.length / this.parPage);
    },
    // ニュースリストの座標までスクロールするためのプロパティ
    getNewsListRect() {
      var $e = $('#newslist');
      return $e.offset().top - 60;
    },
  },
  methods: {
    // ===================
    // 検索欄
    // ===================
    // チェックされた値を格納
    intoCheckedBrand(array) {
      this.resetBrand();
      this.checkedBrandsData = array;
    },
    // チェックボックスがリセットされた時の処理
    resetBrand() {
      this.checkedBrandsData = [];
    },

    // =====================
    // ニュース取得APIリクエスト
    // =====================
    // GoogleNewsControllerを呼び、APIを使ってニュースを取得する
    async searchGoogleNews() {
      // 検索中には呼び出せないようにする
      if (this.isSearching) {
        return false;
      }
      // 検索開始、isSearchingをtrueに、isNothingNewsをfalseにする
      this.isSearching = true;
      this.isNothingNews = false;

      // 現在ページを1に戻す(戻さないと新しく検索した場合にも、途中のページから表示されてしまう)
      this.currentPage = 1;

      // 検索ワードをsearchDataへ格納
      this.wordsIntoSearchData;

      // 作成した検索ワードを元にNewsAPIにリクエスト
      const params = this.searchInputData;
      const response = await axios.get(`/news/get`, { params });

      this.searchedNews = response.data;

      // 記事数が0の時、isNothingNewsをtrueにする
      if (!this.searchedNews.length) {
        this.isNothingNews = true;
      }

      // 検索終了、isSearchingをfalseに戻す
      this.isSearching = false;
      return response.status;
    },
    // ======================
    // ページネーション用
    // ======================
    clickCallback: function (pageNum) {
      this.currentPage = Number(pageNum);
    },

    scrollTop: function () {
      window.scrollTo({
        top: this.getNewsListRect,
      });
    },
  },
  components: {
    News,
    NothingNews,
    NewsSearchCheckbox,
    Loading,
    PageTitle,
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、ニュース取得
        await this.searchGoogleNews();
      },
      immediate: true,
    },
    currentPage: function (newPage, oldPage) {
      this.scrollTop();
    },
  },
};
</script>

<style scoped></style>
