<!--===============-->
<!--ニュースの一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">

    <!-- ページタイトル -->
    <PageTitle :title='page_title'/>

    <!--メインレイアウト-->
    <div class="p-news__container">

      <!-- サーチボックス -->
      <div class="p-news__searchBox--Inner">

        <div class="p-news__searchBox">

          <div class="p-news__searchBox--Item">
            <div class="p-news__searchBox--title"><p>検索条件</p></div>
            <div class="p-news__searchBox--content">

              <!-- 検索ワードを表示するエリア -->
              <div class="p-news__searchBox--content--searchWords">
                <span>{{ default_and_checked_brands }}</span>
              </div>

              <!-- 検索虫眼鏡ボタン -->
              <div class="c-input__btn-area c-input__btn-area__search">
                <button class="c-input__btn-circle" @click.prevent="fetch_googleNews">🔎</button>
              </div>


            </div>
          </div>
        </div>
      </div>

      <!-- 絞り込みアコーディオン -->
      <div class="c-accordion">
        <SearchCheckbox
            @checked="checked_brand"
            @reset="reset_brand"
        />
      </div>

      <!-- ニュース一覧 -->
      <div class="p-news__list">
        <!-- 検索中 -->
        <div v-if="isSearching" class="">
          <Loading />
        </div>
        <!-- ニュースコンポーネント、検索中は非表示 -->
        <div v-else>
          <News
              v-for="News in getNewsItems"
              :key="News.id"
              :entry="News"
          />
          <paginate
              v-model="currentPage"
              :page-count="getPageCount"
              :page-range="10"
              :margin-pages="1"
              :click-handler="clickCallback"
              :prev-text="'＜'"
              :next-text="'＞'"
              :hide-prev-next="true"
              :containerClass="'c-paginate__container'"
              :page-class="'c-paginate__item'"
              :page-link-class="'c-paginate__link'"
              :prev-class="'c-paginate__item c-paginate__item--prev'"
              :prev-link-class="'c-paginate__link'"
              :next-class="'c-paginate__item c-paginate__item--next'"
              :next-link-class="'c-paginate__link'"
              :active-class="'c-paginate__item--active'"
              list="" name="">
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
import SearchCheckbox from './SearchCheckbox.vue';
import Loading from '../../layouts/Loading.vue';
import PageTitle from '../PageComponents/PageTitle.vue';
import { DEFAULT_SEARCHWORD } from "../../util";

import Vue from "vue"
import Paginate from 'vuejs-paginate'
Vue.component('paginate', Paginate)

const PAGE_TITLE = 'NEWS';

export default {

  data() {
    return {
      accordion: false,
      isSearching: false,
      // 「検索した結果、記事が無かった」場合にtrueとなるフラグ。
      // ページ読み込み時にも「記事がありません」と表示するのは不自然なためこのようにしている。
      isNothingNews: false,
      fetchedNews: [],
      checked_brands: [],
      search_input_data: {
        keywords: ''
      },
      // ページネーション用
      parPage: 10,
      currentPage: 1
    }
  },
  computed: {
    page_title() {
      return PAGE_TITLE;
    },
    default_and_checked_brands() {
      return DEFAULT_SEARCHWORD + ' ' + this.checked_brands.join(' ');
    },
    // 「仮想通貨」とチェックされた通貨名の一覧を、search_input_data.keywordsに格納
    marge_words() {
      this.search_input_data.keywords = this.default_and_checked_brands;
    },
    // ======================
    // ページネーション用
    // ======================
    // ページネーション用にニュースリストを細分化する
    getNewsItems: function() {
      let current = this.currentPage * this.parPage;
      let start = current - this.parPage;
      return this.fetchedNews.slice(start, current);
    },
    // 総ページ数
    getPageCount: function() {
      return Math.ceil(this.fetchedNews.length / this.parPage);
    }
  },
  methods: {
    // ===================
    // 検索欄
    // ===================
    // 検索欄を空欄にする
    rese_searchword() {
      this.searchbox_words = '';
    },
    // チェックされた値を格納
    checked_brand(array) {
      this.reset_brand();
      this.checked_brands = array;
    },
    // チェックボックスがリセットされた時の処理
    reset_brand(){
      this.checked_brands = [];
    },

    // =====================
    // ニュース取得APIリクエスト
    // =====================
    // GoogleNewsControllerを呼び、APIを使ってニュースを取得する
    async fetch_googleNews() {
      // 検索中には呼び出せないようにする
      if(this.isSearching) {
        return false;
      }
      // 検索開始、isSearchingをtrueに、isNothingNewsをfalseにする
      this.isSearching = true;
      this.isNothingNews = false;

      // 検索ワードをマージさせる
      this.marge_words;

      // 作成した検索ワードを元にNewsAPIにリクエスト
      const params = this.search_input_data;
      const response = await axios.get(`/news/get`, { params });

      this.fetchedNews = response.data;

      // 記事数が0の時、isNothingNewsをtrueにする
      if(!this.fetchedNews.length) {
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
    }

  },
  components: {
    News,
    NothingNews,
    SearchCheckbox,
    Loading,
    PageTitle
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、ニュース取得
        await this.fetch_googleNews();
      },
      immediate: true
    }
  }
}
</script>

<style scoped>
.p-news__searchBox {
  border-bottom: solid 1px #DDD;
  margin-bottom: 30px;
  padding-bottom: 18px;

  width: inherit;
}

.p-news__searchBox--Inner {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  margin-left: auto;
  margin-right: auto;

  width: 100%;
  padding: 0 30px;

  font-size: 16px;
  line-height: 1.5;
}
.p-news__searchBox--Item {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  -webkit-justify-content: space-between;
  justify-content: space-between;

  margin-top: 15px;
}

.p-news__searchBox--title {
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  color: #AAA;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  width: 140px;

  padding: 0 10px;
}
.p-news__searchBox--content {
  width: 80%;
  display: flex;
  justify-content: space-between;
}
.p-news__searchBox--content--searchWords {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}
</style>