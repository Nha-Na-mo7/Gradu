<!--===============-->
<!--アカウント一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">

    <!-- ページタイトル -->
    <PageTitle :title='page_title'/>

    <!--メインレイアウト-->
    <div v-if="isExistTwitterAccount" class="p-accounts__container">

      <div>
        <!-- リボンタグ -->
        <Ribbonnav
            :title='page_title'
            :date='twitter_accounts_table_updated_at'
        />

        <!-- ヘッドライン -->
        <div class="p-accounts__headline">

          <!-- 自動フォローボタンの位置確認 -->
          <div class="p-news__modal p-news__modal-show">
            <button
                class="c-btn c-btn__main c-btn--primary"
                @click="toggle_auto_following"
                v-if="isAutoFollowFlg"
            >自動フォロー中...</button>
            <button
                class="c-btn c-btn__main c-btn--primary"
                @click="toggle_auto_following"
                v-else
            >START AUTO-FOLLOW</button>
          </div>
        </div>

        <!-- アカウントリスト -->
        <div class="p-accounts__list">

          <!-- アカウントが見つからなかった場合 -->
          <div v-if="isNothingStatus">
            <NothingAccount />
          </div>

          <!-- 読み込み中 -->
          <div v-else-if="isLoading" class="">
            <Loading />
          </div>

          <!-- アカウントコンポーネント -->
          <Account
              v-else
              v-for="Accounts in accounts"
              :key="Accounts.id"
              :account="Accounts"
              :follow_list="follow_list"
              :auto_follow_flg="!!isAutoFollowFlg"
          />
        </div>
        <!-- ページネーション -->
        <Pagination
            v-if="!isLoading && !isNothingStatus"
            :current-page="current_page"
            :last-page="last_page"
        />
      </div>
    </div>

    <!-- Twitter連携していない場合 -->
    <div v-else>
      <NeedLinkage />
    </div>
  </div>

</template>

<script>
import Account from './Account.vue';
import NeedLinkage from './NeedLinkage.vue';
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
      isLoading: true, // 読み込み中か
      nothing_accounts: false, // 検索した結果アカウントが見つからなかったか
      UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID: 1,
      twitter_id: 1,
      auto_follow_flg: false,
      updated_at: '',
      accounts: [],
      follow_list: [],
      current_page: 0,
      last_page: 0,
      total: 0, //総アカウント数
      from: 0, // 100件中21~40 の21の部分
      to: 0, // 100件中21~40 の40の部分
    }
  },
  computed: {
    page_title(){
      return PAGE_TITLE;
    },
    twitter_accounts_table_updated_at() {
      return this.updated_at;
    },
    isNothingStatus() {
      return this.nothing_accounts;
    },
    isAutoFollowFlg() {
      return this.auto_follow_flg;
    },
    isExistTwitterAccount() {
      return !!this.twitter_id;
    }
  },
  methods: {
    // ログイン中のユーザーデータを取得する
    async get_user() {
      const response = await axios
          .get(`/user/info`)
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status === OK) {
        // フォーム用にデータを格納
        this.twitter_id = response.data.twitter_id;
        this.auto_follow_flg =  response.data.auto_follow_flg ?? 0;
      }else{
        // 取得できなかった場合は、アカウント情報を表示させない
        this.nothing_accounts = true;
      }
      this.isLoading = false;
    },
    // ログイン中のユーザーのフォローリストを取得する
    async get_follow_list() {
      const response = await axios
          .get(`/accounts/followlist`)
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status === OK) {
        // フォーム用にデータを格納
        this.follow_list = response.data;
      }else{
        this.nothing_accounts = true;
      }
    },
    // DBのアカウント一覧からアカウント情報を取得(ページネーション済)
    async fetchAccounts() {
      // 読み込み中ならこのメソッドは発火しない
      if(this.isLoading) {
        return false;
      }
      // 読み込みをtrueに
      this.isLoading = true;

      const response = await axios
          .get(`/accounts/list/?page=${this.p}`)
          .catch(error => error.response || error);

      // 通信成功時、各種アカウント取得結果を格納する
      if (response.status === OK){
        this.accounts = response.data.data
        this.current_page = response.data.current_page
        this.last_page = response.data.last_page
        this.total = response.data.total
        this.from = response.data.from
        this.to = response.data.to

        // そのページにアカウントがないor通信が重いなどで読み込めないとき、nothing_accountsをtrueとする
        if(response.data.data.length === 0) {
          this.nothing_accounts = true;
        }
      }else{
        this.nothing_accounts = true;
      }
      this.isLoading = false;
    },
    // DBからアカウント一覧のテーブル更新終了時刻を取得
    async fetchUpdatedAt() {
      const response = await axios
          .get(`/updated/at/table?id=${this.UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID}`)
          .catch(error => error.response || error);

      if(response.status === OK){
        this.updated_at = response.data.updated_at;
      }
    },

    // 自動フォローのON/OFF切り替え
    async toggle_auto_following() {
      var result = false;
      const flg = this.auto_follow_flg;
      if( flg ) {
        result = confirm('自動フォローをOFFにします。よろしいですか？')
      } else {
        result = confirm('自動フォローをONにします。よろしいですか？')
      }
      // confirmではいが選択されたら切り替えを行う
      if(result) {
        const response = await axios
            .post(`/accounts/autofollowflg`, {'follow_flg': flg})
            .catch(error => error.response || error);

        // エラーハンドリング
        if(response.status === OK) {
          this.auto_follow_flg = !flg;
          // フラッシュメッセージをセット
          this.$store.commit('message/setContentSuccess', {
            content: response.data.success
          })
        }else{
          // フラッシュメッセージをセット
          this.$store.commit('message/setContentError', {
            content: 'エラーが発生しました。'
          })
        }
      }
    },
  },
  components: {
    Account,
    NeedLinkage,
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
        await this.get_user();
        await this.get_follow_list();
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