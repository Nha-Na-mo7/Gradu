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
    <div v-else>
      <!-- ユーザーネーム -->
      <div>
        <!-- DBから現在のユーザーネームを取得し、入力された状態にしておく-->
        <label for="name">ユーザーネーム( 20文字以内 )</label>
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
        <label for="email">メールアドレス</label>
        <!-- エラー表示は要修正-->
        <ul v-if="errors_email">
          <li v-for="error in errors_email">
            <span>{{ error }}</span>
          </li>
        </ul>
        <input
            id="email"
            class="p-form__item"
            type="text"
            placeholder="メールアドレスを入力してください"
            v-model="form_email"
        >
        <button
            class="c-btn"
            @click="update_email"
        >
          変更を保存
        </button>
      </div>

      <div>
        <p>メールアドレスの変更後、確認メールを自動送信します。</p>
        <p>必ずメールを受け取れる状態で変更手続きを行ってください。</p>
      </div>

    </div>

    <!-- 戻るボタン -->
    <RouterLink to="/mypage" class="c-btn">マイページへ戻る</RouterLink>


  </div>
</template>

<script>
import PageTitle from '../PageComponents/PageTitle.vue';
import Loading from '../../layouts/Loading.vue';

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
          .get(`/user`)
          .catch(error => error.response || error);

      // エラーチェック
      if(response.status === OK) {
        // フォーム用にデータを格納
        this.form_name = response.data.name
        this.form_email = response.data.email
        this.isloading = false;
      }else{
        this.system_error = response.data.errors
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
      // 500エラーの時は更新失敗、何もしない
      }else if(response.status === INTERNAL_SERVER_ERROR) {
        // TODO フラッシュメッセージをいれる
        console.log('更新に失敗しました。')
      }else{
        // 更新成功したらエラーメッセージは空にする
        this.errors_name = [];
        console.log('名前の更新に成功しました。')
      }
      // ここでページにすぐさま反映させる。フラッシュメッセージで更新報告もする。
      // TODO フラッシュメッセージ
      this.isUpdating = false;
    },

    // メールアドレスの変更
    async update_email() {
      console.log('clicked!')
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
      }else if(response.status === INTERNAL_SERVER_ERROR){
        console.log('500error')
      }else{
        this.errors_email = [];
        // ここでページにすぐさま反映させる。フラッシュメッセージで更新報告もする。
        // TODO フラッシュメッセージ - メールを送信しました。
        console.log('メールアドレスあてにメールを送信しました。')
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