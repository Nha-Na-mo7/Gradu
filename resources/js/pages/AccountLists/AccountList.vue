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
          :date='twitter_accounts_table_updated_at'
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
        <!-- TODO バッチ処理用のボタン・削除すること -->
        <button class="c-btn" @click="twitter_index">バッチ処理・ニュースをDBに格納</button>
      </div>

      <!-- アカウントリスト -->
      <div class="p-accounts__list">
        <!-- 読み込み中 -->
        <div v-if="isLoading" class="">
          <Loading />
        </div>

        <!-- アカウントが見つからなかった場合 -->
        <div v-else-if="isNothing">
          <NothingAccount />
        </div>

        <!-- アカウントコンポーネント -->
        <Account
            v-else
            v-for="Accounts in accounts"
            :key="Accounts.id"
            :account="Accounts"
        />
      </div>

    </div>

    <!-- ページネーション -->
    <Pagination
        :current-page="currentPage"
        :last-page="lastPage"
    />

  </div>

</template>

<script>
import Account from './Account.vue';
import NothingAccount from './NothingAccount.vue';
import Loading from '../../components/Loading.vue';
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import Ribbonnav from '../Components/Ribbonnav.vue';
import Pagination from '../Components/Pagination.vue';
import { OK } from "../../util";

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
      isLoading: false, // 読み込み中か
      isNothingAccounts: false, // 検索した結果アカウントが見つからなかったか
      UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID: 1,
      updated_at: '',
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
    twitter_accounts_table_updated_at() {
      return this.updated_at;
    },
    isNothing() {
      return this.isNothingAccounts;
    }
  },
  methods: {
    // TODO バッチ処理用。本来はこのコンポーネントに存在するものでは無い
    async twitter_index() {
      // APIにアクセス
      const response = await axios.get(`/api/twitter/index`);
    },


    // DBのアカウント一覧からアカウント情報を取得(ページネーション済)
    async fetchAccounts() {
      // 読み込み中ならこのメソッドは発火しない
      if(this.isLoading) {
        return false;
      }
      // 読み込みをtrueに
      this.isLoading = true;

      const response = await axios.get(`/api/accounts/index/?page=${this.p}`);

      // エラー時
      if (response.status !== OK) {
        this.$store.commit('error/setErrorCode', response.status)
        return false
      }

      console.log(response.data)

      this.accounts = response.data.data
      this.currentPage = response.data.current_page
      this.lastPage = response.data.last_page

      // そのページにアカウントがないor通信が思いなどで読み込めないとき
      if(response.data.data.length === 0) {
        this.isNothingAccounts = true;
      }

      // 読み込みをfalseに、isNothingAccountsをtrueに
      this.isLoading = false;
    },
    // DBからアカウント一覧のテーブル更新終了時刻を取得
    async fetchUpdatedAt() {
      const response = await axios.get(`/api/updated/at/table?id=${this.UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID}`);

      // エラー時
      if (response.status !== OK) {
        this.$store.commit('error/setErrorCode', response.status)
        return false
      }

      // console.log(response)
      this.updated_at = response.data.updated_at;
    },
    // オートフォローをオンにする
    auto_following() {
      alert('AUTO-FOLLOWING!');
    },
  },
  components: {
    Account,
    NothingAccount,
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
        await this.fetchUpdatedAt();
      },
      immediate: true
    }
  }

}
</script>

<style scoped>

</style>