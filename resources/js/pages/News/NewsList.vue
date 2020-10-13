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
    <div class="c-site__title">
      <span>NEWS</span>
    </div>

    <div>
      <button class="c-btn" @click="fetch_googleNews">取得</button>
    </div>

    <!--メインレイアウト-->
    <div class="p-news__container">

      <!-- ヘッドライン -->
      <div class="p-news__headline">
        <!-- 検索 -->
        <div class="p-news__search">
          <input type="text" class="c-input" v-model="searchData.keywords">
          <button type="submit" class="">🔎</button>
        </div>

        <!-- 絞り込みモーダルボタン -->
        <div class="c-modal__title">
          <button class="c-btn c-btn__main c-btn--primary" @click="showModal">条件設定</button>
        </div>
      </div>

      <!-- 絞り込みモーダル -->
      <div class="c-modal__hide" v-if="modal">

        <!-- モーダルカバー -->
        <!-- 画面がクリックでモーダルを閉じる。.selfを付与して子要素にクローズイベントが伝播しないようにする-->
        <div class="c-modal__cover" @click.self="closeModal"></div>
        <!-- モーダルコンテンツ -->
        <div class="c-modal">
          <div class="c-modal__head"><span class="c-modal__head-title">検索条件設定</span></div>

          <div class="c-modal__foot">
            <div class="c-modal__index">
              <p class="c-modal__index-title">記事の表示順</p>
              <!-- 降順・昇順ドロップダウン -->
              <div class="c-checkbox__space">
                <div class="c-checkbox__item"><input type="radio" name="CryptoSubject" value="kaso" checked>新着順</div>
                <div class="c-checkbox__item"><input type="radio" name="CryptoSubject" value="alto">古い順</div>
              </div>
            </div>
            <div class="c-modal__index">
              <p class="c-modal__index-title">通貨で絞り込む</p>
              <div class="c-checkbox__space">
                <div class="c-checkbox__item"><input type="checkbox" name="Crypto" value="kaso" checked>仮想通貨</div>
                <div class="c-checkbox__item"><input type="checkbox" name="Crypto" value="alto">アルトコイン</div>
              </div>
              <div class="c-checkbox__space">
                <!-- 通貨アイテムボックス、v-forで通貨テーブルからループさせて描画する -->
                <div class="c-checkbox__item">
                  <label for="0"><input type="checkbox" name="Crypto" value="0" id="0">BTC</label>
                </div>
                <div class="c-checkbox__item">
                  <label for="1"><input type="checkbox" name="Crypto" value="1" id="1">BTC</label>
                </div>
                <div class="c-checkbox__item">
                  <label for="2"><input type="checkbox" name="Crypto" value="2" id="2">BTC</label>
                </div>
              </div>
            </div>


          </div>
          <div class="c-modal__btn-area">
            <button class="c-btn" @click="fetch_googleNews">絞り込む</button>
            <button class="c-btn" @click="closeModal">リセット</button>
            <button class="c-btn" @click="closeModal">絞り込まずに閉じる</button>
            <button class="c-btn" @click="closeModal">設定を保存</button>
          </div>
        </div>
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
        <p>(記事が)ないです</p>
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
import Loading from '../../components/Loading.vue';
import { OK , SEARCHING, defaultSearchWord } from "../../util";


export default {

  data() {
    return {
      modal: false,
      searchData: {
        keywords: ''
      },
      isSearching: false,
      // 「検索した結果、記事が無かった」場合にtrueとなるフラグ。
      // ページ読み込み時にも「記事がありません」と表示するのは不自然なためこのようにしている。
      isNothingNews: false,
      fetchedNews: []
    }
  },
  computed: {
    searchingWord() {
      return SEARCHING;
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

    // GoogleNewsControllerを呼び、APIを使ってニュースを取得する
    async fetch_googleNews() {
      // 検索中には呼び出せないようにする
      if(this.isSearching) {
        return false;
      }
      // 検索開始、isSearchingをtrueに、isNothingNews、modalをfalseにする
      this.isSearching = true;
      this.isNothingNews = false;
      // this.modal = false;

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

    // DBからユーザーが保存した検索設定を取得し、searchData.keywordsに入れる。
    // 検索設定が保存されていない場合、'仮想通貨'とデフォルトで格納する。
    async fetch_setting_search() {
      // DBから取得してくる処理
      // const response = await axios.get(`/api/news/setting/get`, { params });

      // DBから取得した値が空だった場合の処理
      if(true) {
        this.searchData.keywords = defaultSearchWord;
      }
    },

    // 検索設定をDBに保存するメソッド
    // TODO この処理はPHP側でやるのかJS側でやるのか検討、おそらくはModelを作成してPHP側で処理させる
    save_setting_search() {
      // const response = await axios.post(`/api/news/setting/get`, { params });
    },

    // チェックボックスでチェックされた内容をsearchData.keywordsに入れる
    getCheckboxWord() {
      $('[name="Crypto"]').change(function(){
        $('[name="Crypto"]:checked').each(function(index, element){
          this.searchData.keywords.push($(element).val());
        });
      });
    }
  },
  components: {
    News,
    Loading
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