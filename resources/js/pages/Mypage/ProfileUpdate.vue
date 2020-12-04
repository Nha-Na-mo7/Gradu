<!--======================================================-->
<!-- ユーザーネーム・メールアドレスを更新するフォームのコンポーネント-->
<!--======================================================-->
<template>
  <div class="l-container__content">
    <!-- ページタイトル -->
    <PageTitle :title="page_title" />

    <!-- 読み込み中 -->
    <div v-if="isLoading">
      <Loading />
    </div>

    <!-- ユーザーネームとメールアドレスの変更フォーム -->
    <div class="p-setting" v-else>
      <div class="p-setting__container">
        <!-- ユーザーネーム -->
        <div class="p-form">
          <label class="c-form__info" for="name"
            >ユーザーネーム( 20文字以内 )</label
          >

          <ul v-if="errorsName">
            <li class="c-error" v-for="error in errorsName">
              <span>{{ error }}</span>
            </li>
          </ul>
          <input
            id="name"
            class="c-form__input"
            type="text"
            v-model="formName"
          />
          <div class="u-text--center">
            <button class="c-btn" @click="updateName">変更を保存</button>
          </div>
        </div>

        <div class="c-border">
          <div class="c-border__dividingText">
            <span class="c-border__dividingText-spanborder"></span>
          </div>
        </div>

        <!-- メールアドレス -->
        <div class="p-form">
          <!-- DBから現在のメールアドレスを取得し、入力された状態にしておく-->
          <div class="p-form__description u-mb-l">
            <p>メールアドレスを入力後、確認メールを自動送信します。</p>
            <p>必ずメールを受け取れる状態で変更手続きを行ってください。</p>
          </div>
          <label class="c-form__info" for="email">メールアドレス </label>
          <!-- エラー表示は要修正-->
          <ul v-if="errorsEmail">
            <li class="c-error" v-for="error in errorsEmail">
              <span>{{ error }}</span>
            </li>
          </ul>
          <input
            id="email"
            class="c-form__input"
            type="text"
            v-model="formEmail"
          />
          <div class="u-text--center">
            <button class="c-btn" @click="updateEmail">
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

import { OK, UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js';
const PAGE_TITLE = 'プロフィール編集';

export default {
  data() {
    return {
      isLoading: true,
      // ユーザーネームのフォーム
      user: '',
      formName: '',
      // メールアドレスのフォーム
      formEmail: '',
      systemError: [],
      errorsName: [],
      errorsEmail: [],
      isUpdating: false,
    };
  },
  computed: {
    page_title() {
      return PAGE_TITLE;
    },
  },
  methods: {
    // ログイン中のユーザーデータを取得する
    async getUser() {
      const response = await axios
        .get(`/user/info`)
        .catch((error) => error.response || error);

      // エラーチェック
      if (response.status === OK) {
        // フォーム用にデータを格納
        this.formName = response.data.name;
        this.formEmail = response.data.email;
        this.isLoading = false;
      }
    },

    // ユーザーネームの変更
    async updateName() {
      // 更新処理中は複数回起動できないようにする
      if (this.isUpdating) {
        return false;
      }
      this.isUpdating = true;

      // 更新処理にアクセスする
      const response = await axios
        .post(`/user/update/name`, { name: this.formName })
        .catch((error) => error.response || error);

      // エラーチェック
      if (response.status === UNPROCESSABLE_ENTITY) {
        // バリデーションエラー。帰ってきたエラーメッセージを格納
        this.errorsName = response.data.errors.name;
        // 500エラーの時は更新失敗
      } else if (response.status === INTERNAL_SERVER_ERROR) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.error,
        });
      } else {
        // 更新成功したらエラーメッセージは空にする
        this.errorsName = [];

        // フラッシュメッセージをセット
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success,
        });
      }
      this.isUpdating = false;
    },

    // メールアドレスの変更
    async updateEmail() {
      // 更新処理中は複数回起動できないようにする
      if (this.isUpdating) {
        return false;
      }
      this.isUpdating = true;

      const response = await axios
        .post(`/user/update/email`, { email: this.formEmail })
        .catch((error) => error.response || error);

      // バリデーションエラー時
      if (response.status === UNPROCESSABLE_ENTITY) {
        this.errorsEmail = response.data.errors.email;
        this.isUpdating = false;
        // 500エラー時
      } else if (response.status === INTERNAL_SERVER_ERROR) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.error,
        });
      } else {
        // 送信完了したらフラッシュメッセージを表示し、バリデーションエラーリストを空にする
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success,
        });
        this.errorsEmail = [];
      }
      this.isUpdating = false;
    },
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後にユーザー取得を行う
        await this.getUser();
      },
      immediate: true,
    },
  },
  components: {
    PageTitle,
    Loading,
  },
};
</script>

<style scoped></style>
