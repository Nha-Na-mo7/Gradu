<!--=======================================================-->
<!-- パスワードを新規作成するコンポーネント(Twitterで新規登録した人用)-->
<!--=======================================================-->
<template>
  <div class="p-setting">
    <div class="p-setting__container">
      <div class="p-form">

        <div class="p-form__decription">
          <p>※ 他のサービスと同じパスワードは使用しないでください</p>
        </div>

        <label
            class="p-form__info"
            for="password"
        >新しいパスワード(半角英数字 8~50文字)</label>

        <ul v-if="errors_password">
          <li class="c-error" v-for="error in errors_password">
            <span>{{ error }}</span>
          </li>
        </ul>
        <input
            id="password"
            class="p-form__item"
            type="password"
            v-model="form_password.password"
        >

        <label
            class="p-form__info"
            for="password_confirmation"
        >パスワード【再入力】</label>

        <ul v-if="errors_password_confirmation">
          <li v-for="error in errors_password_confirmation">
            <span>{{ error }}</span>
          </li>
        </ul>
        <input
            id="password_confirmation"
            class="p-form__item"
            type="password"
            v-model="form_password.password_confirmation"
        >

        <div class="u-text--center">
          <button
              class="c-btn"
              @click="create_password"
          >
            パスワードを登録する
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { UNPROCESSABLE_ENTITY , INTERNAL_SERVER_ERROR } from "../../util";

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
          .post(`/user/create/password`, this.form_password )
          .catch(error => error.response || error)

      // エラーチェック
      if(response.status === UNPROCESSABLE_ENTITY) {
        // バリデーションエラー。帰ってきたエラーメッセージを格納
        this.errors_password = response.data.errors.password;
        this.errors_password_confirmation = response.data.errors.password_confirmation;

        // 500エラーの時は更新失敗
      }else if(response.status === INTERNAL_SERVER_ERROR) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.errors
        })
        this.isUpdating = false;

      }else{
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success
        })
        this.isUpdating = false;

        // パスワード作成完了後はマイページに戻す
        this.$router.push('/mypage')
      }
    },
  },
}

</script>

<style scoped>

</style>