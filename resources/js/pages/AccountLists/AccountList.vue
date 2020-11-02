<!--===============-->
<!--アカウント一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">

    <!-- サイトリンク -->
    <SiteLinknav :currentPageTitle='pageTitle'/>

    <!-- ページタイトル -->
    <PageTitle :title='pageTitle'/>

    <!--メインレイアウト-->
    <div class="p-accounts__container">

      <!-- リボンタグ -->
      <Ribbonnav
          :title='pageTitle'
          :date='today'
      />

      <!-- ヘッドライン -->
      <div class="p-accounts__headline">
        <!-- 自動フォローボタンの位置確認 -->
        <div class="p-news__modal p-news__modal-show">
          <button
              class="c-btn c-btn__main c-btn--primary"
              @click="auto_following"
          >自動フォロー</button>
        </div>
        <button class="c-btn" @click="twitter_index">バッチ処理・ニュースをDBに格納</button>
      </div>

      <!-- アカウントリスト -->
      <div class="p-accounts__list">
        <!-- 検索中 -->
        <div v-if="isSearching" class="">
          <Loading />
        </div>
        <!-- アカウント -->
        <Account
            v-else
            v-for="Accounts in accounts"
            :key="Accounts.id"
            :account="Accounts"
        />
      </div>


    </div>

    <Pagination
        :current-page="currentPage"
        :last-page="lastPage"
    />

  </div>

</template>

<script>
import Account from './Account.vue';
import Loading from '../../components/Loading.vue';
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import Ribbonnav from '../Components/Ribbonnav.vue';
import Pagination from '../Components/Pagination.vue';
import {OK, DEFAULT_SEARCHWORD} from "../../util";
import { mapState } from 'vuex';

const PAGE_TITLE = '仮想通貨アカウント一覧';

export default {
  props: {
    p: {
      type: Number,
      required: false,
      default: 1
    }
  },
  data() {
    return {
      isSearching: false, // 検索中か
      isNothingAccounts: false, // 検索した結果アカウントが見つからなかったか
      accounts: [],
      currentPage: 0,
      lastPage: 0,
      searchData: {
        keywords: '仮想通貨',
        page: this.p
      }
    }
  },
  computed: {
    pageTitle(){
      return PAGE_TITLE;
    },
    // TODO リボンタグ用・最終更新日を1日1回更新していれる、このcomputed自体は削除予定
    today() {
      return new Date();
    }
  },
  methods: {
    // TwitterControllerを呼び、APIを使って該当のアカウント一覧を取得する
    async fetch_TwitterAccountsOld() {

      // 検索中には呼び出せないようにする
      if(this.isSearching) {
        return false;
      }

      // 検索開始時点で、isSearchingをtrueに、isNothingAccountsをfalseにする
      this.isSearching = true;
      this.isNothingAccounts = false;

      // APIにアクセス
      const params = this.searchData;
      const response = await axios.get(`/api/twitter/index_old`, { params });

      // エラー時
      if (response.status !== OK) {
        this.$store.commit('error/setErrorCode', response.status);
        return false;
      }

      // レスポンスの結果を変数に格納
      // TwitterAPIは配列で返してくるので、オブジェクト形式に変更
      const res = {};
      for(let i = 0, l = response.data.result.length; i < l; i += 1) {
        const data = response.data.result[i];
        res[data.id] = data;
      }
      this.fetchedAccounts = res;
      console.log(this.fetchedAccounts)

      // 見つけたアカウントの数が0の時、isNothingAccountsをtrueにする
      if(!this.fetchedAccounts.length) {
        this.isNothingNews = true;
      }
      // 検索終了、isSearchingをfalseに戻す
      this.isSearching = false;

      console.log(response.data)

      this.currentPage = response.data.current_page
      this.lastPage = response.data.last_page

      // ステータス番号を返す
      return response.status;
    },
    // バッチ処理用。本来はこのコンポーネントに存在するものでは無い
    async twitter_index() {

      // APIにアクセス
      const response = await axios.get(`/api/twitter/index`);

      // エラー時
      if (response.status !== OK) {
        this.$store.commit('error/setErrorCode', response.status);
        return false;
      }

      alert('yes!' + response.status);

      // ステータス番号を返す
      return response.status;
    },


    // DBのアカウント一覧からアカウント情報を取得(ページネーション済)
    async fetchAccounts() {
      const response = await axios.get(`/api/accounts/index/?page=${this.p}`);

      // エラー時
      if (response.status !== OK) {
        this.$store.commit('error/setErrorCode', response.status)
        return false
      }

      console.log(response)

      this.accounts = response.data.data
      this.currentPage = response.data.current_page
      this.lastPage = response.data.last_page
    },
    // オートフォローをオンにする
    auto_following() {
      alert('AUTO-FOLLOWING!');
    }
  },
  components: {
    Account,
    Loading,
    SiteLinknav,
    PageTitle,
    Ribbonnav,
    Pagination
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、DBからTwitterアカウント一覧を取得
        await this.fetchAccounts();
        // await this.fetch_TwitterAccountsOld();
      },
      immediate: true
    }
  }

}
</script>

<style scoped>

</style>