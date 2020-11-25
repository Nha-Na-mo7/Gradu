<!--===============-->
<!--アカウント一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">

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

        <button class="c-btn" @click="test_search_account">twitterアカウント検索テストボタン</button>

        <!-- 自動フォローボタンの位置確認 -->
        <div class="p-news__modal p-news__modal-show">
          <button
              class="c-btn c-btn__main c-btn--primary"
              @click="auto_following"
              v-if="auto_follow_flg"
          >自動フォロー中...</button>
          <button
              class="c-btn c-btn__main c-btn--primary"
              @click="auto_following"
              v-else
          >START AUTO-FOLLOW</button>
        </div>
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
import Loading from '../../layouts/Loading.vue';
import PageTitle from '../PageComponents/PageTitle.vue';
import Ribbonnav from '../PageComponents/Ribbonnav.vue';
import Pagination from '../PageComponents/Pagination.vue';
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
      auto_follow_flg: false,
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
    },
  },
  methods: {
    // DBのアカウント一覧からアカウント情報を取得(ページネーション済)
    async fetchAccounts() {
      // 読み込み中ならこのメソッドは発火しない
      if(this.isLoading) {
        return false;
      }
      // 読み込みをtrueに
      this.isLoading = true;

      const response = await axios.get(`/accounts/index/?page=${this.p}`);
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
      const response = await axios.get(`/updated/at/table?id=${this.UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID}`);

      this.updated_at = response.data.updated_at;
    },
    // オートフォローを切り替える
    async auto_following() {

      const flg = this.auto_follow_flg;
      // getterに何も入っていない場合-1が帰ってくるため、その時は処理を行わない
      if(flg === -1) {
        return false
      }

      var result = false;
      if( flg ) {
        result = confirm('自動フォローをOFFにします。よろしいですか？')
      } else {
        result = confirm('自動フォローをONにします。よろしいですか？')
      }
      if(result) {
        const response = await axios.post(`/accounts/autofollowflg`, {'follow_flg': flg});
        this.auto_follow_flg = !flg;
      }else {
        return false
      }
    },

    async test_search_account() {
      console.log('test start ')
      await axios.get(`/twitter/testtest`);
    }
  },
  components: {
    Account,
    NothingAccount,
    Loading,
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