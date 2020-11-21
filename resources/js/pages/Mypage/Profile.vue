<template>
<div class="l-container__content">
  <!-- サイトリンク -->
  <SiteLinknav :currentPageTitle='page_title'/>

  <!-- ページタイトル -->
  <PageTitle :title='page_title'/>

  <!-- ユーザーネームとメールアドレスの変更フォーム -->
  <div>
    <!-- ユーザーネーム -->
    <div>
      <!-- DBから現在のユーザーネームを取得し、入力された状態にしておく-->
      <label for="name">ユーザーネーム(20文字以内)</label>
      <!-- エラー表示は要修正-->
      <ul v-if="errors_name">
        <li v-for="error in errors_name">
          <span>{{ error }}</span>
        </li>
      </ul>
      <input
          id="name"
          class="p-form__item"
          type="text"
          placeholder="ユーザーネームを入力してください"
          v-model="form_name"
      >
      <button
          class="c-btn"
          @click="update_name"
      >
        変更を保存
      </button>
    </div>

    <!-- メールアドレス -->
    <div>
      <span>登録メールアドレス</span>
      <span>ERROR ドメインが存在しません</span>
      <!-- DBから現在のメールアドレスを取得し、入力された状態にしておく-->
      <input type="text">
      <button class="c-btn">変更する</button>

      <p>メールアドレスの変更後、確認メールを自動送信します。必ずメールを受け取れる状態で変更手続きを行ってください。</p>
    </div>

  </div>


</div>
</template>

<script>
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
import { OK , UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js';
const PAGE_TITLE = 'プロフィール編集';

export default {
  data() {
    return {
      // ユーザーネームのフォーム
      form_name: '',
      // メールアドレスのフォーム
      mail_form: {
        email: '',
      },
      errors_name: '',
      error_mail: '',
      isUpdating: false
    }
  },
  computed: {
    page_title() {
      return PAGE_TITLE
    }
  },
  methods: {
    // ユーザーネームの変更
    async update_name() {
      // 更新処理中は複数回起動できないようにする
      if(this.isUpdating){
        return false;
      }
      this.isUpdating = true;

      // 更新処理にアクセス
      const response = await axios
          .post(`/api/user/update/name`, { name : this.form_name })
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status === UNPROCESSABLE_ENTITY) {
        console.log(response.data.errors.name)
        // 帰ってきたエラーメッセージを格納
        this.errors_name = response.data.errors.name;
      // 500エラーの時は更新失敗、何もしない
      }else if(response.status === INTERNAL_SERVER_ERROR) {
        // TODO フラッシュメッセージ
        console.log('更新に失敗しました。')
      }else{
        console.log('名前の更新に成功しました。')
      }
      // ここでページにすぐさま反映させる。フラッシュメッセージで更新報告もする。
      // TODO フラッシュメッセージ
      this.isUpdating = false;
    },
    // メールアドレスの変更
    async update_mail() {
      // 更新処理中は複数回起動できないようにする
      if(!this.isUpdating){
        return false;
      }
      this.isUpdating = true;

      const response = await axios
          .post(`/api/user/update/mail`, this.mail_form )
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status !== OK) {
        // TODO フラッシュメッセージ
        console.log('更新に失敗しました。')
        this.isUpdating = false;
      }

      // ここでページにすぐさま反映させる。フラッシュメッセージで更新報告もする。
      // TODO フラッシュメッセージ
      console.log('メールアドレスあてにメールを送信しました。')
      this.isUpdating = false;
    },
  },
  components: {
    SiteLinknav,
    PageTitle
  }
}
</script>

<style scoped>

</style>