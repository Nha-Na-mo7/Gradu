<!--=========================-->
<!--マイページ・アカウント設定画面-->
<!--=========================-->
<template>
<div class="l-container__content">

  <!-- ページタイトル -->
  <PageTitle :title='page_title'/>

  <!-- 読み込み中 -->
  <div v-if="isloading">
    <Loading />
  </div>

  <!-- メインレイアウト -->
  <div v-else class="p-container dummyflex">

    <!-- ユーザー名・メールアドレス -->
    <div class="dummybox">
      <RouterLink to="/mypage/profile">プロフィールの設定変更</RouterLink>
      <div>
        <h2>ユーザーネーム</h2>
        <p>{{ this.auth_name }}</p>
      </div>
      <div>
        <h2>登録メールアドレス</h2>
        <p>{{ this.auth_mail }}</p>
      </div>
    </div>

    <!-- パスワード -->
    <div class="dummybox">
      <RouterLink to="/mypage/password">パスワードの変更</RouterLink>
      <div v-if="isExist_password">
        <!-- 実際の桁数に関係なく********とする -->
        <p>********</p>
      </div>
      <div v-else>
        <p>パスワードは設定されていません</p>
        <p>Twitterの連携を解除するには、パスワードの設定が必要です。</p>
      </div>
    </div>

    <!-- SNS連携 -->
    <div class="dummybox">
      <div>
        <h2>SNS ログイン連携</h2>
        <h2>Twitter</h2>
        <!-- 連携中の時 -->
        <div v-if="isExist_twitter">
          <div>
            <p>連携中</p>
            <p>Twitterアカウントでログインでき、仮想通貨アカウント一覧機能を利用することができます。</p>

            <button class="c-btn" @click="twitter_un_linkage">解除する</button>
          </div>
          <div>
            <h2>Twitterアカウントの自動フォロー状態</h2>
            <p v-if="auto_follow_status">ON</p>
            <p v-else>OFF</p>
          </div>

        </div>

        <!-- 連携していない時 -->
        <div v-else>
          <p>連携していません</p>
          <p>仮想通貨アカウント一覧機能など、一部の機能がご利用できません。</p>
          <button>
            <a
              class="c-btn c-btn--primary c-btn__twitter--login"
              title="Start for Twitter!"
              @click.stop
              :href="`/twitter/auth/begin`"
            >
              連携する
            </a>
          </button>
        </div>
      </div>
    </div>

    <!-- 退会処理 -->
    <div class="p-mypage__footer">
      <button
          class="c-btn"
          @click="withdraw"
      >退会する</button>
    </div>
  </div>
</div>
</template>

<script>
import PageTitle from '../PageComponents/PageTitle.vue';
import Loading from '../../layouts/Loading.vue';

import { OK , UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js';
const PAGE_TITLE = 'マイページ(アカウント設定)';

export default {
  data() {
    return {
      loading: true,
      twitter: false,
      password: false,
      auto_follow: false,
      mail: '',
      name: ''
    }
  },
  computed: {
    page_title() {
      return PAGE_TITLE
    },
    isloading(){
      return this.loading
    },
    isExist_twitter(){
      return this.twitter
    },
    isExist_password(){
      return this.password
    },
    auto_follow_status(){
      return this.auto_follow
    },
    auth_mail(){
      return this.mail
    },
    auth_name(){
      return this.name
    },
  },
  methods: {
    // ログイン中のユーザーデータを取得する
    async get_user() {
      const response = await axios
          .get(`/user/info`)
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status === OK) {
        console.log(response)
        // フォーム用にデータを格納
        this.user = response.data
        this.twitter = (response.data.twitter_id !== null);
        this.password = (response.data.password !== null);
        this.auto_follow =  response.data.auto_follow_flg;
        this.mail =  response.data.email;
        this.name =  response.data.name;
        this.loading = false;
      }else{
        this.system_error = response.data.errors
        this.isloading = false;
      }
    },
    // 退会処理 PHP側でデータ削除して、フロント側で画面遷移させる。
    async withdraw() {
      if(confirm('【 CryptoTrendを退会しますか？ 】\n退会すると各種サービスのご利用ができなくなります。')){
        const response = await axios.post(`/withdraw`);
        if(response.status === OK){
          window.location = "/";
        }else{
          window.location = "/login";
        }
      }
    },
    // Twitter連携解除
    async twitter_un_linkage() {
      // 更新処理中は複数回起動できないようにする
      if(this.isUpdating){
        return false;
      }

      // パスワードが設定されていない場合、警告を出して連携解除できないようにする
      if(!this.isExist_password) {
        alert('【パスワードが設定されていません】\nパスワードが設定されていない状態でTwitter連携を解除すると、ログインができなくなります。\nパスワードを設定してから再度お試しください。');
        this.isUpdating = false;
        return false;
      }

      // はいが選択されたら解除処理を行う
      if(confirm('【 Twitterの連携を解除してもよろしいですか？ 】\nTwitterの連携を解除すると、一部の機能がご利用できなくなります。')){
        this.isUpdating = true;

        // 更新処理にアクセス
        const response = await axios
            .post(`/accounts/un_linkage`)
            .catch(error => error.response || error);

        // エラーチェック
        if(response.status === OK) {
          // フラッシュメッセージをセット
          this.$store.commit('message/setContentSuccess', {
            content: response.data.success
          })
          this.twitter = false;
        }else{
          // フラッシュメッセージをセット
          this.$store.commit('message/setContentError', {
            content: response.data.errors
          })
        }
        this.isUpdating = false;
      }
    },
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後にユーザー取得を行う
        await this.get_user();
      },
      immediate: true
    }
  },
  components: {
    PageTitle,
    Loading
  }
}
</script>

<style scoped>
.dummyflex {
  display: flex;
  flex-wrap: wrap;
  width: 100%;
  position: relative;
}
.dummybox {
  width: 45%;
  font-size: 15px;
  margin-right: 20px;
  margin-bottom: 30px;
  border: 1px solid #0a0a0a;
  background: #d8d8d8;
  min-height: 200px;
}
</style>