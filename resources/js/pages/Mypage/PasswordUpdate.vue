<!--=============================-->
<!-- パスワードの更新用のコンポーネント-->
<!--=============================-->
<template>
  <div class="p-setting">
    <div class="p-setting__container">
      <div class="p-form">
        <div class="p-form__decription">
          <p>※ 他のサービスと同じパスワードは使用しないでください</p>
        </div>

        <label class="c-form__info" for="old_password">現在のパスワード</label>

        <ul v-if="errorsOldPassword">
          <li class="c-error" v-for="error in errorsOldPassword">
            <span>{{ error }}</span>
          </li>
        </ul>
        <input
          id="old_password"
          class="c-form__input"
          type="password"
          v-model="formPassword.old_password"
        />

        <label class="c-form__info" for="password"
          >新しいパスワード (半角英数字 8~50文字)</label
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
          >新しいパスワード【再入力】</label
        >

        <ul v-if="errorsPasswordConfirmation">
          <li class="c-error" v-for="error in errorsPasswordConfirmation">
            <span>{{ error }}</span>
          </li>
        </ul>
        <input
          id="password_confirmation"
          class="c-form__input"
          type="password"
          v-model="formPassword.password_confirmation"
        />

        <div class="u-text--center">
          <button class="c-btn" @click="updatePassword">
            パスワードを変更する
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js';

export default {
  data() {
    return {
      errorsOldPassword: '',
      errorsPassword: '',
      errorsPasswordConfirmation: '',
      formPassword: {
        old_password: '',
        password: '',
        password_confirmation: '',
      },
    };
  },
  computed: {},
  methods: {
    // パスワードの更新
    async updatePassword() {
      // 更新処理中は複数回起動できないようにする
      if (this.isUpdating) {
        return false;
      }
      this.isUpdating = true;

      // 更新処理にアクセス
      const response = await axios
        .post(`/user/update/password`, this.formPassword)
        .catch((error) => error.response || error);

      // エラーチェック
      if (response.status === UNPROCESSABLE_ENTITY) {
        // バリデーションエラー。帰ってきたエラーメッセージを格納
        this.errorsOldPassword = response.data.errors.old_password;
        this.errorsPassword = response.data.errors.password;
        this.errorsPasswordConfirmation =
          response.data.errors.password_confirmation;

        // 500エラーの時
      } else if (response.status === INTERNAL_SERVER_ERROR) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.errors,
        });
        // 成功時
      } else {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success,
        });
        // パスワード更新完了後はマイページに戻す
        this.$router.push('/mypage');
      }
      this.isUpdating = false;
    },
  },
};
</script>

<style scoped></style>
