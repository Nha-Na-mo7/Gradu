<!--=======================================================-->
<!-- パスワードを新規作成するコンポーネント(Twitterで新規登録した人用)-->
<!--=======================================================-->
<template>
  <div class="p-setting">
    <div class="p-setting__container">
      <div class="p-form">
        <div class="p-form__description">
          <p>※ 他のサービスと同じパスワードは使用しないでください</p>
        </div>

        <label class="c-form__info" for="password"
          >新しいパスワード(半角英数字 8~50文字)</label
        >

        <ul v-if="errorsPassword">
          <li class="c-error" v-for="error in errorsPassword">
            <span>{{ error }}</span>
          </li>
        </ul>
        <input
          id="password"
          class="c-form__input"
          type="password"
          v-model="formPassword.password"
        />

        <label class="c-form__info" for="password_confirmation"
          >パスワード【再入力】</label
        >

        <ul v-if="errorsPasswordConfirmation">
          <li v-for="error in errorsPasswordConfirmation">
            <span>{{ error }}</span>
          </li>
        </ul>
        <input
          id="password_confirmation"
          class="c-form__input"
          type="password"
          v-model="formPassword.password_confirmation"
        />

        <div class="c-form__submit u-text--center">
          <button class="c-btn" @click="createPassword">
            パスワードを登録する
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util';

export default {
  data() {
    return {
      isUpdating: false,
      errorsPassword: '',
      errorsPasswordConfirmation: '',
      formPassword: {
        password: '',
        password_confirmation: '',
      },
    };
  },
  computed: {},
  methods: {
    // パスワードの新規登録
    async createPassword() {
      // 更新処理中は複数回起動できないようにする
      if (this.isUpdating) {
        return false;
      }
      this.isUpdating = true;

      // 更新処理にアクセス
      const response = await axios
        .post(`/user/create/password`, this.formPassword)
        .catch((error) => error.response || error);

      // エラーチェック
      if (response.status === UNPROCESSABLE_ENTITY) {
        // バリデーションエラー。帰ってきたエラーメッセージを格納
        this.errorsPassword = response.data.errors.password;
        this.errorsPasswordConfirmation =
          response.data.errors.password_confirmation;

        // 500エラーの時は更新失敗
      } else if (response.status === INTERNAL_SERVER_ERROR) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.errors,
        });
        this.isUpdating = false;
      } else {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success,
        });
        this.isUpdating = false;

        // パスワード作成完了後はマイページに戻す
        this.$router.push('/mypage');
      }
    },
  },
};
</script>

<style scoped></style>
