<!--===============-->
<!--アカウント一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">

    <AutoFollowModal
        v-if="modal"
        :auto_flg="!!auto_follow_flg"
        @close="closeModal"
        @toggle_auto_follow_flg="toggle_auto_following"
    />

    <!-- ページタイトル -->
    <PageTitle :title='page_title'/>

    <!--メインレイアウト-->
    <div v-if="isExistTwitterAccount" class="p-accounts">

      <div>
        <!-- リボンタグ -->
        <Ribbonnav
            :title='page_title'
            :date='twitter_accounts_table_updated_at'
        />

        <!-- 自動フォロー欄 -->
        <div class="p-accounts__autofollow">
          <h2>自動フォローステータス</h2>

          <button @click="showModal" class="c-btn">おも０だる</button>

          <div class="">
            <div
                class="c-btn c-btn__follow"
                v-if="isAutoFollowFlg"
            >自動フォロー中...</div>
            <div
                class="c-btn c-btn__follow c-btn__follow--destroy"
                v-else
            >していません</div>
          </div>
        </div>

        <!-- アカウントリスト -->
        <div id="accounts" class="p-accounts__list">

          <!-- アカウントが見つからなかった場合 -->
          <div v-if="isNothingStatus">
            <NothingAccount />
          </div>

          <!-- 読み込み中 -->
          <div v-else-if="isLoading" class="">
            <Loading />
          </div>

          <!-- アカウントコンポーネント -->
          <div v-else>
            <div class="u-text--center">
              <div>
                <p>{{ this.getStartCount }} - {{ this.getEndCount }} / {{ this.accounts.length }}アカウント</p>
              </div>
              <paginate
                  v-model="currentPage"
                  :page-count="getPageCount"
                  :page-range="3"
                  :margin-pages="1"
                  :click-handler="clickCallback"
                  :prev-text="'＜'"
                  :next-text="'＞'"
                  :hide-prev-next="true"
                  :containerClass="'c-paginate'"
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
            <Account
                v-for="Accounts in getAccountsItems"
                :key="Accounts.id"
                :account="Accounts"
                :follow_flg="checkAlreadyFollow(Accounts.account_id)"
                :auto_follow_flg="!!isAutoFollowFlg"
            />
            <div class="u-text--center">
              <paginate
                  v-model="currentPage"
                  :page-count="getPageCount"
                  :page-range="3"
                  :margin-pages="1"
                  :click-handler="clickCallback"
                  :prev-text="'＜'"
                  :next-text="'＞'"
                  :hide-prev-next="true"
                  :containerClass="'c-paginate'"
                  :page-class="'c-paginate__item'"
                  :page-link-class="'c-paginate__link'"
                  :prev-class="'c-paginate__item c-paginate__item--prev'"
                  :prev-link-class="'c-paginate__link'"
                  :next-class="'c-paginate__item c-paginate__item--next'"
                  :next-link-class="'c-paginate__link'"
                  :active-class="'c-paginate__item--active'"
                  list="" name="">
              </paginate>
              <div>
                <p>{{ this.getStartCount }} - {{ this.getEndCount }} / {{ this.accounts.length }}アカウント</p>
              </div>
            </div>
          </div>
        </div>
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
import AutoFollowModal from './AutoFollowModal.vue';
import NeedLinkage from './NeedLinkage.vue';
import NothingAccount from './NothingAccount.vue';
import Loading from '../../layouts/Loading.vue';
import PageTitle from '../PageComponents/PageTitle.vue';
import Ribbonnav from '../PageComponents/Ribbonnav.vue';
import { OK } from "../../util";

import Vue from "vue"
import Paginate from 'vuejs-paginate'
Vue.component('paginate', Paginate)

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
      modal: false,
      isLoading: true, // 読み込み中か
      nothing_accounts: false, // 検索した結果アカウントが見つからなかったか
      UPDATED_AT_TABLES__TWITTER_ACCOUNTS_ID: 1,
      twitter_id: 1,
      auto_follow_flg: false,
      updated_at: '',
      follow_list: [],
      accounts: [],
      parPage: 10,
      currentPage: 1
    }
  },
  computed: {
    page_title(){
      return PAGE_TITLE;
    },
    // アカウント一覧の最終更新時刻
    twitter_accounts_table_updated_at() {
      return this.updated_at;
    },
    // アカウントが見つからなかったかを返すcomputed
    isNothingStatus() {
      return this.nothing_accounts;
    },
    // オートフォローがONがどうか
    isAutoFollowFlg() {
      return this.auto_follow_flg;
    },
    // ユーザーがTwitterアカウントと連携しているかどうか(!! 二重否定で確実にboolean型とする)
    isExistTwitterAccount() {
      return !!this.twitter_id;
    },
    // 引数に指定したアカウントのIDがユーザーのフォローリストに含まれているかの判定
    checkAlreadyFollow: function(){
      return function(id) {
        // フォローリストをループさせ、TwitterIDと一致していたらtrueを返す
        for (var i = 0, len = this.follow_list.length; i < len; i++) {
          if (id === this.follow_list[i]['follow_target_id']) {
            return true;
          }
        }
        return false;
      }
    },
    // ======================
    // ページネーション用
    // ======================
    // ページネーション用にアカウントリストを細分化する
    getAccountsItems: function() {
      let current = this.currentPage * this.parPage;
      let start = current - this.parPage;
      return this.accounts.slice(start, current);
    },
    // 総ページ数
    getPageCount: function() {
      return Math.ceil(this.accounts.length / this.parPage);
    },
    // 現在の表示開始箇所 (21-30件表示中 の21の部分)
    getStartCount: function (){
      return ((this.currentPage - 1) * this.parPage) + 1;
    },
    // 現在の表示終了箇所 (21-30件表示中 の30の部分)
    getEndCount: function (){
      let current = this.currentPage * this.parPage;
      let over_check = current > this.accounts.length
      if(over_check) {
        return this.accounts.length
      }else{
        return current;
      }
    },
    // アカウントリストの座標までスクロールするためのプロパティ
    getAccountsRect() {
      var $e = $('#accounts');
      return $e.offset().top - 60;
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
          .get(`/accounts/list`)
          .catch(error => error.response || error);

      // 通信成功時、各種アカウント取得結果を格納する
      if (response.status === OK){
        this.accounts = response.data

        // そのページにアカウントがないor通信が重いなどで読み込めないとき、nothing_accountsをtrueとする
        if(response.data.length === 0) {
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

    // ======================
    // ページネーション用
    // ======================
    clickCallback: function (pageNum) {
      this.currentPage = Number(pageNum);
    },

    scrollTop: function () {
      window.scrollTo({
        top: this.getAccountsRect,
      });
    },
    // =======================
    // モーダル関連
    // =======================
    showModal() {
      // 読み込み中ならモーダルを開かない
      if(this.isLoading) {
        return false;
      }
      this.modal = true;
    },
    closeModal() {
      this.modal = false;
    },
    // 自動フォローのON/OFF切り替え
    toggle_auto_following() {
      this.auto_follow_flg = !this.auto_follow_flg;
    },
  },
  components: {
    Account,
    AutoFollowModal,
    NeedLinkage,
    NothingAccount,
    Loading,
    PageTitle,
    Ribbonnav,
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
    },
    currentPage: function (newPage, oldPage) {
      this.scrollTop();
    }
  }

}
</script>

<style scoped>

</style>