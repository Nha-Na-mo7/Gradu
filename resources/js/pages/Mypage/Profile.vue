<!--======================================================-->
<!-- ユーザーネーム・メールアドレスを更新するフォームのコンポーネント-->
<!--======================================================-->
<template>
  <div class="l-container__content">

    <!-- ページタイトル -->
    <PageTitle :title='page_title'/>

    <!-- 読み込み中 -->
    <div v-if="isloading">
      <Loading />
    </div>

    <!-- ユーザーネームとメールアドレスの変更フォーム -->
    <div class="p-setting" v-else>
      <div class="p-setting__container">
        <!-- ユーザーネーム -->
        <div class="p-form">

          <label
              class="p-form__info"
              for="name"
          >ユーザーネーム( 20文字以内 )</label>

          <ul v-if="errors_name">
            <li class="c-error" v-for="error in errors_name">
              <span>{{ error }}</span>
            </li>
          </ul>
          <input
              id="name"
              class="p-form__item"
              type="text"
              v-model="form_name"
          >
          <div class="u-text--center">
            <button
                class="c-btn"
                @click="update_name"
            >
              変更を保存
            </button>
          </div>
        </div>

        <div class="c-border">
          <div class="c-border__dividingText">
            <span class="c-border__dividingText-spanborder"></span>
          </div>
        </div>

        <!-- メールアドレス -->
        <div>
          <!-- DBから現在のメールアドレスを取得し、入力された状態にしておく-->
          <div class="p-form__description u-mb-l">
            <p>メールアドレスを入力後、確認メールを自動送信します。</p>
            <p>必ずメールを受け取れる状態で変更手続きを行ってください。</p>
          </div>
          <label
              class="p-form__info"
              for="email">メールアドレス
          </label>
          <!-- エラー表示は要修正-->
          <ul v-if="errors_email">
            <li class="c-error" v-for="error in errors_email">
              <span>{{ error }}</span>
            </li>
          </ul>
          <input
              id="email"
              class="p-form__item"
              type="text"
              v-model="form_email"
          >
          <div class="u-text--center">
            <button
                class="c-btn"
                @click="update_name"
            >
              メールアドレスを変更
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- 戻るボタン -->
    <div class="u-text--center">
      <RouterLink to="/mypage" class="c-btn">マイページへ戻る</RouterLink>
    </div>

  </div>
</template>

<script>
import PageTitle from '../PageComponents/PageTitle.vue';
import Loading from '../../layouts/Loading.vue';
// import Vuex from ''

import { OK , UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js';
const PAGE_TITLE = 'プロフィール編集';

export default {
  data() {
    return {
      isloading: true,
      // ユーザーネームのフォーム
      user: '',
      form_name: '',
      // メールアドレスのフォーム
      form_email: '',
      system_error: [],
      errors_name: [],
      errors_email: [],
      isUpdating: false
    }
  },
  computed: {
    page_title() {
      return PAGE_TITLE
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
        this.form_name = response.data.name
        this.form_email = response.data.email
        this.isloading = false;
      }
    },

    // ユーザーネームの変更
    async update_name() {
      // 更新処理中は複数回起動できないようにする
      if(this.isUpdating){
        return false;
      }
      this.isUpdating = true;

      // 更新処理にアクセスする
      const response = await axios
          .post(`/user/update/name`, { name : this.form_name })
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status === UNPROCESSABLE_ENTITY) {
        // バリデーションエラー。帰ってきたエラーメッセージを格納
        this.errors_name = response.data.errors.name;
      // 500エラーの時は更新失敗
      }else if(response.status === INTERNAL_SERVER_ERROR) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.error
        })
      }else{
        // 更新成功したらエラーメッセージは空にする
        this.errors_name = [];

        // フラッシュメッセージをセット
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success
        });

      }
      this.isUpdating = false;
    },

    // メールアドレスの変更
    async update_email() {
      // 更新処理中は複数回起動できないようにする
      if(this.isUpdating){
        return false;
      }
      this.isUpdating = true;

      const response = await axios
          .post(`/user/update/email`, { email : this.form_email })
          .catch(error => error.response || error);

      // バリデーションエラー時
      if(response.status === UNPROCESSABLE_ENTITY ) {
        this.errors_email = response.data.errors.email;
        this.isUpdating = false;
      // 500エラー時
      }else if(response.status === INTERNAL_SERVER_ERROR){
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.error
        })
      }else{
        // 送信完了したらフラッシュメッセージを表示し、バリデーションエラーリストを空にする
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success
        })
        this.errors_email = [];
      }
      this.isUpdating = false;
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