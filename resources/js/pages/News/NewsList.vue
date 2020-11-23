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
            <div class="p-news__searchBox--title"><p>フリーワード</p></div>
            <div class="p-news__searchBox--content">
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
            </div>
          </div>
          <div class="p-news__searchBox--Item">
            <div class="p-news__searchBox--title"><p>検索条件</p></div>
            <div class="p-news__searchBox--content">

              <div class="p-news__searchBox--content--searchWords">
                <span>{{ default_and_checkedSearchWords }}</span>
              </div>

              <!-- 絞り込みモーダルボタン -->
              <div class="p-news__modal p-news__modal-show">
                <button class="c-btn c-btn__main c-btn--primary" @click="showModal">設定</button>
              </div>

            </div>
          </div>


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
import Loading from '../../layouts/Loading.vue';
import PageTitle from '../PageComponents/PageTitle.vue';
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
      searchBoxWords: '',
      searchData: {
        keywords: ''
      },
    }
  },
  computed: {
    page_title() {
      return PAGE_TITLE;
    },
    placeholder() {
      return '追加したい検索ワードを入れることができます'
    },
    // 検索欄にワードが存在するか
    isExistSearchWord() {
      return this.searchBoxWords !== '';
    },
    default_and_checkedSearchWords() {
      return DEFAULT_SEARCHWORD + ' ' + this.checkedCurrencies.join(' ');
    },
    // checkedCurrencyとsearchBoxWordsを組み合わせたワードを、searchData.keywordsに格納する
    margeSearchWords() {
      this.searchData.keywords = this.default_and_checkedSearchWords + ' ' + this.searchBoxWords;
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

      console.log(response)

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
.p-news__searchBox {
  border-bottom: solid 1px #DDD;
  margin-bottom: 30px;
  padding-bottom: 18px;

  width: inherit;
}

.p-news__search {
  width: 100%;
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