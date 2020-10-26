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
          <button class="c-btn c-btn__main c-btn--primary">自動フォロー</button>
        </div>
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
            v-for="Accounts in fetchedAccounts"
            :key="Accounts.id"
            :account="Accounts"
        />
      </div>


    </div>

  </div>

</template>

<script>
import Account from './Account.vue';
import Loading from '../../components/Loading.vue';
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import Ribbonnav from '../Components/Ribbonnav.vue';
import {OK, DEFAULT_SEARCHWORD} from "../../util";
import { mapState } from 'vuex';

const PAGE_TITLE = '仮想通貨アカウント一覧';

export default {
  data() {
    return {
      isSearching: false, // 検索中か
      isNothingAccounts: false, // 検索した結果アカウントが見つからなかったか
      fetchedAccounts: [],
      searchData: {
        keywords: '仮想通貨'
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
  components: {
    Account,
    Loading,
    SiteLinknav,
    PageTitle,
    Ribbonnav
  },
  methods: {
    // TwitterControllerを呼び、APIを使って該当のアカウント一覧を取得する
    async fetch_TwitterAccounts() {

      // 検索中には呼び出せないようにする
      if(this.isSearching) {
        return false;
      }

      // 検索開始時点で、isSearchingをtrueに、isNothingAccountsをfalseにする
      this.isSearching = true;
      this.isNothingAccounts = false;

      // APIにアクセス
      const params = this.searchData;
      const response = await axios.get(`/api/twitter/index`, { params });

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
      // console.log(this.fetchedAccounts)

      // 見つけたアカウントの数が0の時、isNothingAccountsをtrueにする
      if(!this.fetchedAccounts.length) {
        this.isNothingNews = true;
      }
      // 検索終了、isSearchingをfalseに戻す
      this.isSearching = false;

      // ステータス番号を返す
      return response.status;
    },
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、Twitterアカウント一覧を取得
        await this.fetch_TwitterAccounts();
      },
      immediate: true
    }
  }

}
</script>

<style scoped>

</style>