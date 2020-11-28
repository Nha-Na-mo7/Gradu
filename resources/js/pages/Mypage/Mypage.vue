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

    <div class="p-mypage">
      <div class="p-mypage__column">
        <!-- ユーザー名・メールアドレス -->
        <div class="p-documentbox">
          <div class="p-documentbox__header">
            <h2 class="p-documentbox__title">プロフィール</h2>
            <RouterLink to="/mypage/profile">設定する ></RouterLink>
          </div>
          <div class="p-documentbox__body">
            <h2 class="p-documentbox__item p-documentbox__item--info">ユーザーネーム</h2>
            <p class="p-documentbox__item">{{ this.auth_name }}</p>
          </div>
          <div class="p-documentbox__body">
            <h2 class="p-documentbox__item p-documentbox__item--info">登録メールアドレス</h2>
            <p class="p-documentbox__item">{{ this.auth_mail }}</p>
          </div>
        </div>

        <!-- パスワード -->
        <div class="p-documentbox">
          <div class="p-documentbox__header">
            <h2 class="p-documentbox__title">パスワード</h2>
            <RouterLink to="/mypage/password">設定する ></RouterLink>
          </div>
          <div class="p-documentbox__body" v-if="isExist_password">
            <!-- 実際の桁数に関係なく********とする -->
            <div class="p-documentbox__item">
              <p>********</p>
            </div>
          </div>
          <div class="p-documentbox__body" v-else>
            <div class="p-documentbox__item">
              <p>パスワードは設定されていません</p>
              <p>Twitterの連携を解除するには、パスワードの設定が必要です。</p>
            </div>
          </div>
        </div>
      </div>

      <div class="p-mypage__column">
        <!-- SNS連携 -->
        <div class="p-documentbox">
          <div class="p-documentbox__header">
            <h2 class="p-documentbox__title">SNS連携状態</h2>
          </div>

          <!-- 連携中の時 -->
          <div class="p-documentbox__body" v-if="isExist_twitter">
            <div class="p-documentbox__item">
              <h2>Twitter</h2>
              <span class="u-text--right">連携中</span>
            </div>
            <div>
              <p class="p-documentbox__item">Twitterアカウントでログインでき、仮想通貨アカウント一覧機能を利用することができます。</p>
              <div>
                <button class="c-btn" @click="twitter_un_linkage">解除する</button>
              </div>
            </div>
          </div>

          <!-- 連携していない時 -->

          <div class="p-documentbox__body" v-else>
            <h2 class="p-documentbox__item">Twitter</h2>
            <p>連携していません</p>
            <div>
              <p class="p-documentbox__item">仮想通貨アカウント一覧機能など、一部の機能がご利用できません。</p>
              <div>
                <button>
                  <a
                      class="c-btn c-btn__twitter"
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
        </div>

        <!-- 退会処理 -->
        <div class="p-documentbox">
          <div class="p-documentbox__header">
            <h2 class="p-documentbox__title">退会する</h2>
          </div>
          <div class="p-documentbox__body">
            <div class="p-documentbox__item">
              <p>退会処理を行うと、CryptoTrendのサービスがご利用いただけなくなります。</p>
            </div>
            <div class="p-documentbox__item">
              <button
                  class="c-btn"
                  @click="withdraw"
              >退会する</button>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>
</template>

<script>
import PageTitle from '../PageComponents/PageTitle.vue';
import Loading from '../../layouts/Loading.vue';

import { OK , UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js';
const PAGE_TITLE = 'マイページ';

export default {
  data() {
    return {
      loading: true,
      twitter: false,
      password: false,
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
        // フォーム用にデータを格納
        this.user = response.data
        this.twitter = (response.data.twitter_id !== null);
        this.password = (response.data.password !== null);
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

</style>