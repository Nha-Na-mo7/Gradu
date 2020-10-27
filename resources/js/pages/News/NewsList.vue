<!--===============-->
<!--ニュースの一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">

    <!-- サイトリンク -->
    <SiteLinknav :currentPageTitle='pageTitle'/>

    <!-- ページタイトル -->
    <PageTitle :title='pageTitle'/>

    <!--メインレイアウト-->
    <div class="p-news__container">

      <!-- ヘッドライン -->
      <div class="p-news__headline">
        <!-- 検索フォーム・コンポーネント検討 -->
        <form class="p-news__search">

          <!-- 検索虫眼鏡ボタン -->
          <div class="c-input__btn-area c-input__btn-area__search">
            <button class="c-input__btn-circle" @click.prevent="fetch_googleNews">🔎</button>
          </div>
          <!-- 検索欄 -->
          <div class="c-input__searcharea">
            <p>検索中のワード:<span>{{ searchData.keywords }}</span></p>
<!--            <input type="text" class="c-input" v-model="searchBoxWords" :placeholder="placeholder">-->
          </div>

        </form>

        <!-- 絞り込みモーダルボタン -->
        <div class="p-news__modal p-news__modal-show">
          <button class="c-btn c-btn__main c-btn--primary" @click="showModal">条件設定</button>
        </div>
      </div>

      <!-- 絞り込みモーダル -->
      <div class="c-modal__hide" v-if="modal">
        <SearchModal
          @closeModal="closeModal"
          @fetch_googleNews="fetch_googleNews"
        />
      </div>

      <!-- ニュース一覧 -->
      <div class="p-news__list">
        <!-- 検索中 -->
        <div v-if="isSearching" class="">
          <Loading />
        </div>
        <!-- ニュースコンポーネント、検索中は非表示 -->
        <News
            v-else
            v-for="News in fetchedNews"
            :key="News.id"
            :entry="News"
        />
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
import SearchModal from './SearchModal.vue';
import Loading from '../../components/Loading.vue';
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import { OK ,DEFAULT_SEARCHWORD } from "../../util";
import { mapState } from 'vuex';

const PAGE_TITLE = 'NEWS';

export default {

  data() {
    return {
      modal: false,
      isSearching: false,
      // 「検索した結果、記事が無かった」場合にtrueとなるフラグ。
      // ページ読み込み時にも「記事がありません」と表示するのは不自然なためこのようにしている。
      isNothingNews: false,
      isEditMode: false,

      fetchedNews: [],

      checkedSearchWords: [],
      searchBoxWords: DEFAULT_SEARCHWORD,
      searchData: {
        keywords: ''
      },
    }
  },
  computed: {
    pageTitle() {
      return PAGE_TITLE;
    },
    // 検索欄にワードが存在するか
    isExistSearchWord() {
      return this.searchBoxWords !== '';
    },
    // checkedCurrencyとsearchBoxWordsを組み合わせたワードを、searchData.keywordsに格納する
    margeSearchWords() {
      this.searchData.keywords = this.searchBoxWords + ' ' + this.checkedCurrencies.join(' ');
    },
    ...mapState({
      checkedCurrencies: state => state.news.checkedCurrencies,
    })

  },
  methods: {
    // モーダルを開く
    showModal(){
      this.modal = true;
    },
    // モーダルを閉じる
    closeModal(){
      this.modal = false;
    },
    // TODO 現状使っていないので最後まで必要なければ削除してください
    // 検索欄を空欄にする
    resetSearchWord() {
      this.searchBoxWords = '';
    },

    // GoogleNewsControllerを呼び、APIを使ってニュースを取得する
    async fetch_googleNews() {
      // 検索中には呼び出せないようにする
      if(this.isSearching) {
        return false;
      }
      // 検索開始、isSearchingをtrueに、isNothingNews、modalをfalseにする
      this.isSearching = true;
      this.isNothingNews = false;
      this.modal = false;

      // 検索ワードをマージさせる
      this.margeSearchWords;

      const params = this.searchData;
      const response = await axios.get(`/api/news/get`, { params });

      // エラー時
      if (response.status !== OK) {
        this.$store.commit('error/setErrorCode', response.status)
        return false
      }

      this.fetchedNews = response.data;

      // 記事数が0の時、isNothingNewsをtrueにする
      if(!this.fetchedNews.length) {
        this.isNothingNews = true;
      }

      // 検索終了、isSearchingをfalseに戻す
      this.isSearching = false;
      return response.status;
    },

  },
  components: {
    SiteLinknav,
    News,
    NothingNews,
    SearchModal,
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

</style>