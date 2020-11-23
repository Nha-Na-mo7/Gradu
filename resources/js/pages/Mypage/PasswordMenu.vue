<!--=======================================================-->
<!--パスワード変更画面のコンポーネント。登録があるかで新規・更新を分ける-->
<!--=======================================================-->
<template>
  <div class="l-container__content">

    <!-- ページタイトル -->
    <PageTitle :title='page_title'/>

    <!-- 読み込み中 -->
    <div v-if="isloading">
      <Loading />
    </div>

    <!-- パスワード変更フォーム -->
    <div v-else>
      <!-- パスワードが設定済みの時 -->
      <div v-if="isExist_password">
        <PasswordUpdate />
      </div>

      <!-- パスワードがまだ未登録の時 -->
      <div v-else>
        <PasswordCreate />
      </div>

    </div>

    <div>
      <p>※ 他のサービスと同じパスワードは使用しないでください</p>
    </div>

    <!-- 戻るボタン -->
    <RouterLink to="/mypage" class="c-btn">マイページへ戻る</RouterLink>

  </div>
</template>

<script>
import PageTitle from '../PageComponents/PageTitle.vue';
import Loading from '../../layouts/Loading.vue';
import PasswordCreate from './PasswordCreate.vue';
import PasswordUpdate from './PasswordUpdate.vue';
import { OK , INTERNAL_SERVER_ERROR } from '../../util.js';
const PAGE_TITLE = 'パスワード設定';

export default {
  data() {
    return {
      isloading: true,
      isExist_password: false,
    }
  },
  computed: {
    page_title() {
      return PAGE_TITLE
    },
  },
  methods: {
    // ログイン中のユーザーデータを取得する
    async get_user() {

      const response = await axios
          .get(`/api/user`)
          .catch(error => error.response || error);

      console.log(response)
      // エラーチェック
      if(response.status === OK) {
        // パスワードが既に設定されている場合、isExist_passwordをtrueとする
        if(response.data.password !== null){
          this.isExist_password = true
        }
        this.isloading = false;
      }else{
        this.system_error = response.data.errors
      }
    },
  },
  components: {
    PageTitle,
    Loading,
    PasswordCreate,
    PasswordUpdate
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後にユーザーの取得を行う
        await this.get_user();
      },
      immediate: true
    }
  },
}
</script>

<style scoped>

</style>