<!--===============-->
<!--ニュースの一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">

    <!-- サイトリンク -->
    <div class="c-site-linknav">
      <RouterLink class="c-site__link-nav__to-top" to="/">トップ</RouterLink>
      <span>></span>
      <RouterLink class="c-site__link-nav__to-content" to="/news">ニュース</RouterLink>
    </div>

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
            <input type="text" class="c-input" v-model="searchBoxWords" :placeholder="placeholder">
          </div>
          <!-- リセット用の✖️ボタン -->
          <div class="c-input__btn-area c-input__btn-area__reset" v-if="isExistSearchWord">
            <button class="c-input__btn-circle" @click="resetSearchWord">×</button>
          </div>
        </form>

        <!-- 絞り込みモーダルボタン -->
        <div class="c-modal__title">
          <button class="c-btn c-btn__main c-btn--primary" @click="showModal">条件設定</button>
        </div>
      </div>

      <!-- 絞り込みモーダル -->
      <div class="c-modal__hide" v-if="modal">
        <SearchModal
          @closeModal="closeModal"
          @fetch_googleNews="fetch_googleNews"
          @checkedWord="checkedSearchWordByModal"
        />
      </div>

      <!-- ニュース一覧 -->
      <div class="p-news__list">
        <News
            v-for="News in fetchedNews"
            :key="News.id"
            :entry="News"
        />
      </div>

      <!-- 記事がない時 -->
      <div v-if="isNothingNews">
        <NothingNews />
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
import News from './News.vue';
import NothingNews from './NothingNews.vue';
import SearchModal from './SearchModal.vue';
import Loading from '../../components/Loading.vue';
import PageTitle from '../Components/PageTitle.vue';
import { OK , SEARCHING, DEFAULT_SEARCHWORD } from "../../util";

const PAGE_TITLE = 'NEWS';
const PLACEHOLDER = '検索したいワードを追加することができます。';

export default {

  data() {
    return {
      pageTitle: PAGE_TITLE,
      placeholder: PLACEHOLDER,
      modal: false,
      isSearching: false,
      // 「検索した結果、記事が無かった」場合にtrueとなるフラグ。
      // ページ読み込み時にも「記事がありません」と表示するのは不自然なためこのようにしている。
      isNothingNews: false,
      isEditMode: false,
      fetchedNews: [],
      checkedSearchWords: ['プロ野球', 'ソフトバンク'],
      searchBoxWords: '',
      searchData: {
        keywords: ''
      },
    }
  },
  computed: {
    searchingWord() {
      return SEARCHING;
    },
    // 検索欄にワードが存在するか
    isExistSearchWord() {
      return this.searchBoxWords !== '';
    },
    // checkedSearchWordsとsearchBoxWordsを組み合わせたワードを、searchData.keywordsに格納する
    margeSearchWords() {
      this.searchData.keywords = this.checkedSearchWords.join(' ') + ' ' + this.searchBoxWords;
    }

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
    // 検索欄を空欄にする
    resetSearchWord() {
      this.searchBoxWords = '';
    },
    // モーダルから与えられたワードを検索欄にいれ、既に入っていた場合は消す。
    checkedSearchWordByModal(value) {
      // 配列を探す。
      if(true){
        this.checkedSearchWords = value;
      }
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

    // DBからユーザーが保存した検索設定を取得し、checkedSearchWordsに入れる。
    // 検索設定が保存されていない場合、'仮想通貨'とデフォルトで格納する。
    async fetch_setting_search() {
      // DBから取得してくる処理
      // const response = await axios.get(`/api/news/setting/get`, { params });

      // DBから取得した値が空だった場合の処理
      if(true) {
        this.searchBoxWords = DEFAULT_SEARCHWORD;
      }
    },

  },
  components: {
    News,
    NothingNews,
    SearchModal,
    Loading,
    PageTitle
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、DBから検索設定ワードを格納して、ニュース取得を行う
        await this.fetch_setting_search();
        await this.fetch_googleNews();
      },
      immediate: true
    }
  }

}
</script>

<style scoped>

</style>