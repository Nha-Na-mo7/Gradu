<template>
<div class="l-container__content">
  <!-- サイトリンク -->
  <SiteLinknav :currentPageTitle='page_title'/>

  <!-- ページタイトル -->
  <PageTitle :title='page_title'/>

  <!-- ユーザーネームとメールアドレスの変更フォーム -->
  <div v-if="!system_error">
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
      <!-- DBから現在のメールアドレスを取得し、入力された状態にしておく-->
      <label for="name">メールアドレス</label>
      <!-- エラー表示は要修正-->
      <ul v-if="errors_mail">
        <li v-for="error in errors_mail">
          <span>{{ error }}</span>
        </li>
      </ul>
      <input
          id="mail"
          class="p-form__item"
          type="text"
          placeholder="メールアドレスを入力してください"
          v-model="form_mail"
      >
      <button
          class="c-btn"
          @click="update_mail"
      >
        変更を保存
      </button>
    </div>

    <div>
      <p>メールアドレスの変更後、確認メールを自動送信します。必ずメールを受け取れる状態で変更手続きを行ってください。</p>
    </div>

  </div>
  <div
      v-else
  >
    <h2>ユーザー情報を読み込めませんでした。</h2>
    <p>しばらくしてからもう一度お試しください。</p>
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
      user: '',
      form_name: '',
      // メールアドレスのフォーム
      form_mail: '',
      system_error: '',
      errors_name: '',
      errors_mail: '',
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
          .get(`/api/user`)
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status === OK) {
        // フォーム用にデータを格納
        this.form_name = response.data.name
        this.form_mail = response.data.email
      }else{
        this.system_error = response.data.errors
      }
    },
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
        // バリデーションエラー。帰ってきたエラーメッセージを格納
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
          .post(`/api/user/update/mail`, { mail : this.form_mail })
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status === UNPROCESSABLE_ENTITY ) {
        this.errors_mail = response.data.errors.mail;
        this.isUpdating = false;
      }

      // ここでページにすぐさま反映させる。フラッシュメッセージで更新報告もする。
      // TODO フラッシュメッセージ
      console.log('メールアドレスあてにメールを送信しました。')
      this.isUpdating = false;
    },
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後にもニュース取得を行う
        await this.get_user();
      },
      immediate: true
    }
  },
  components: {
    SiteLinknav,
    PageTitle
  }
}
</script>

<style scoped>

</style>