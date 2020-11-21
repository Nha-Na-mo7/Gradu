<!--=======================================================-->
<!-- パスワードを新規作成するコンポーネント(Twitterで新規登録した人用)-->
<!--=======================================================-->

<template>
  <div>
    <label for="password">新しいパスワード(半角英数字 8~50文字)</label>
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
        placeholder="パスワードを入力してください"
        v-model="form_password.password"
    >

    <label for="password_confirmation">パスワード【再入力】</label>
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
        placeholder="同じパスワードをもう一度入力してください"
        v-model="form_password.password_confirmation"
    >

    <button
        class="c-btn"
        @click="create_password"
    >
      パスワードを登録する
    </button>
  </div>
</template>

<script>
import { OK , UNPROCESSABLE_ENTITY , INTERNAL_SERVER_ERROR } from "../../util";

export default {
  data() {
    return {
      isUpdating: false,
      errors_password: '',
      errors_password_confirmation: '',
      form_password: {
        password: '',
        password_confirmation: ''
      },
    }
  },
  computed: {

  },
  methods: {
    // パスワードの新規登録
    async create_password() {
      // 更新処理中は複数回起動できないようにする
      if(this.isUpdating){
        return false;
      }
      this.isUpdating = true;

      // 更新処理にアクセス
      const response = await axios
          .post(`/api/user/create/password`, this.form_password )
          .catch(error => error.response || error)

      // エラーチェック
      if(response.status === UNPROCESSABLE_ENTITY) {
        // バリデーションエラー。帰ってきたエラーメッセージを格納
        this.errors_password = response.data.errors.password;
        this.errors_password_confirmation = response.data.errors.password_confirmation;
        // 500エラーの時は更新失敗
      }else if(response.status === INTERNAL_SERVER_ERROR) {
        // TODO フラッシュメッセージ
        console.log('作成に失敗しました。')
      }else{
        console.log('パスワードの新規作成に成功しました。')
        this.isUpdating = false;
        // パスワード作成完了後はマイページに戻す
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