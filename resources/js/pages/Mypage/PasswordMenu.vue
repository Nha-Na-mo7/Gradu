<!--=======================================================-->
<!--パスワード変更画面のコンポーネント。登録があるかで新規・更新を分ける-->
<!--=======================================================-->
<template>
  <div class="l-container__content">
    <!-- ページタイトル -->
    <PageTitle :title="pageTitle" />

    <!-- 読み込み中 -->
    <div v-if="isLoading">
      <Loading />
    </div>

    <!-- パスワード変更フォーム -->
    <div v-else>
      <!-- パスワードが設定済みの時 -->
      <div v-if="isExistPassword">
        <PasswordUpdate />
      </div>

      <!-- パスワードがまだ未登録の時 -->
      <div v-else>
        <PasswordCreate />
      </div>
    </div>

    <!-- 戻るボタン -->
    <div class="u-text--center">
      <RouterLink to="/mypage" class="c-btn">マイページへ戻る</RouterLink>
    </div>
  </div>
</template>

<script>
import PageTitle from "../PageComponents/PageTitle.vue";
import Loading from "../../layouts/Loading.vue";
import PasswordCreate from "./PasswordCreate.vue";
import PasswordUpdate from "./PasswordUpdate.vue";
import { OK, INTERNAL_SERVER_ERROR } from "../../util.js";
const PAGE_TITLE = "パスワード設定";

export default {
  data() {
    return {
      isLoading: true,
      isExistPassword: false,
    };
  },
  computed: {
    pageTitle() {
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
        // パスワードが既に設定されている場合、isExistPasswordをtrueとする
        if (response.data.password !== null) {
          this.isExistPassword = true;
        }
        this.isLoading = false;
      }
    },
  },
  components: {
    PageTitle,
    Loading,
    PasswordCreate,
    PasswordUpdate,
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後にユーザーの取得を行う
        await this.getUser();
      },
      immediate: true,
    },
  },
};
</script>

<style scoped></style>
