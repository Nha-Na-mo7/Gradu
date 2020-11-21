<!--=============================-->
<!-- パスワードの更新用のコンポーネント-->
<!--=============================-->
<template>
  <div>
    <label for="old_password">現在のパスワード</label>
    <!-- エラー表示は要修正-->
    <ul v-if="errors_old_password">
      <li v-for="error in errors_old_password">
        <span>{{ error }}</span>
      </li>
    </ul>
    <input
        id="old_password"
        class="p-form__item"
        type="password"
        placeholder="現在のパスワードを入力してください"
        v-model="form_password.old_password"
    >

    <label for="password">新しいパスワード (半角英数字 8~50文字)</label>
    <!-- エラー表示は要修正-->
    <ul v-if="errors_password">
      <li v-for="error in errors_password">
        <span>{{ error }}</span>
      </li>
    </ul>
    <input
        id="password"
        class="p-form__item"
        type="password"
        placeholder="新しいパスワードを入力してください"
        v-model="form_password.password"
    >


    <label for="password_confirmation">新しいパスワード【再入力】</label>
    <!-- エラー表示は要修正-->
    <ul v-if="errors_password_confirmation">
      <li v-for="error in errors_password_confirmation">
        <span>{{ error }}</span>
      </li>
    </ul>
    <input
        id="password_confirmation"
        class="p-form__item"
        type="password"
        placeholder="新しいパスワードをもう一度入力してください"
        v-model="form_password.password_confirmation"
    >

    <button
        class="c-btn"
        @click="update_password"
    >
      パスワードを変更する
    </button>
  </div>

</template>

<script>
import { OK , UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js';

export default {
  data() {
    return {
      errors_old_password: '',
      errors_password: '',
      errors_password_confirmation: '',
      form_password: {
        old_password: '',
        password: '',
        password_confirmation: ''
      },
    }
  },
  computed: {

  },
  methods: {
    // パスワードの更新
    async update_password() {
      // 更新処理中は複数回起動できないようにする
      if(this.isUpdating){
        return false;
      }
      this.isUpdating = true;

      // 更新処理にアクセス
      const response = await axios
          .post(`/api/user/update/password`, this.form_password )
          .catch(error => error.response || error);

      console.log(response)

      // エラーチェック
      if(response.status === UNPROCESSABLE_ENTITY) {
        // バリデーションエラー。帰ってきたエラーメッセージを格納
        this.errors_old_password = response.data.errors.old_password;
        this.errors_password = response.data.errors.password;
        this.errors_password_confirmation = response.data.errors.password_confirmation;

        this.isUpdating = false;

        // 500エラーの時は更新失敗
      } else if(response.status === INTERNAL_SERVER_ERROR) {
        // TODO フラッシュメッセージ
        console.log('作成に失敗しました。')

        this.isUpdating = false;

      } else {
        console.log('パスワードの新規作成に成功しました。')

        this.isUpdating = false;
        // パスワード更新完了後はマイページに戻す
        window.location = "/mypage"
      }
      // ここでページにすぐさま反映させる。フラッシュメッセージで更新報告もする。
      // TODO フラッシュメッセージ

    },
  },
}
</script>

<style scoped>

</style>