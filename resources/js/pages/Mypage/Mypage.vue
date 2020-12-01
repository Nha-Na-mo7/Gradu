<!--=========================-->
<!--マイページ・アカウント設定画面-->
<!--=========================-->
<template>
  <div class="l-container__content">
    <!-- ページタイトル -->
    <PageTitle :title="pageTitle" />

    <!-- 読み込み中 -->
    <div v-if="isLoading">
      <Loading />
    </div>

    <!-- メインレイアウト -->
    <div v-else class="p-container dummyflex">
      <div class="p-mypage">
        <div class="p-mypage__column">
          <!-- ユーザー名・メールアドレス -->
          <div class="p-documentbox c-documentbox">
            <div class="c-documentbox__header">
              <h2 class="c-documentbox__title">プロフィール</h2>
              <RouterLink to="/mypage/profile">設定する ></RouterLink>
            </div>
            <div class="c-documentbox__body">
              <h2 class="c-documentbox__item c-documentbox__item--info">
                ユーザーネーム
              </h2>
              <p class="c-documentbox__item">{{ this.authName }}</p>
            </div>
            <div class="c-documentbox__body">
              <h2 class="c-documentbox__item c-documentbox__item--info">
                登録メールアドレス
              </h2>
              <p class="c-documentbox__item">{{ this.authMail }}</p>
            </div>
          </div>

          <!-- パスワード -->
          <div class="p-documentbox c-documentbox">
            <div class="c-documentbox__header">
              <h2 class="c-documentbox__title">パスワード</h2>
              <RouterLink to="/mypage/password">設定する ></RouterLink>
            </div>
            <div class="c-documentbox__body" v-if="isExistPassword">
              <!-- 実際の桁数に関係なく********とする -->
              <h2 class="c-documentbox__item c-documentbox__item--info">
                パスワード設定済
              </h2>
              <p class="c-documentbox__item">＊＊＊＊＊＊＊＊</p>
            </div>
            <div class="c-documentbox__body" v-else>
              <h2 class="c-documentbox__item c-documentbox__item--info">
                パスワードは設定されていません
              </h2>
              <p class="c-documentbox__item">
                Twitterの連携を解除するには、パスワードの設定が必要です。
              </p>
            </div>
          </div>
        </div>

        <div class="p-mypage__column">
          <!-- SNS連携 -->
          <div class="p-documentbox c-documentbox">
            <div class="c-documentbox__header">
              <h2 class="c-documentbox__title">Twitter連携状態</h2>
            </div>

            <!-- 連携中の時 -->
            <div class="c-documentbox__body" v-if="isExistTwitter">
              <div class="c-documentbox__item c-documentbox__item--info">
                <h2>Twitterと連携中</h2>
              </div>
              <div>
                <p class="c-documentbox__item">
                  仮想通貨アカウント一覧機能を利用することができます。
                </p>
                <div class="c-documentbox__footer">
                  <button
                    class="c-btn c-btn__twitter"
                    @click="twitterUnLinkage"
                  >
                    連携を解除する
                  </button>
                </div>
              </div>
            </div>

            <!-- 連携していない時 -->

            <div class="c-documentbox__body" v-else>
              <div class="c-documentbox__item c-documentbox__item--info">
                <h2>連携していません</h2>
              </div>
              <div>
                <p class="c-documentbox__item">
                  仮想通貨アカウント一覧機能がご利用できません。
                </p>
              </div>
              <div class="c-documentbox__footer">
                <button>
                  <a
                    class="c-btn c-btn__twitter"
                    title="Start for Twitter!"
                    @click.stop
                    :href="`/twitter/auth/begin`"
                  >
                    連携する
                  </a>
                </button>
              </div>
            </div>
          </div>

          <!-- 退会処理 -->
          <div class="p-documentbox c-documentbox">
            <div class="c-documentbox__header">
              <h2 class="c-documentbox__title">退会する</h2>
            </div>
            <div class="c-documentbox__body">
              <div class="c-documentbox__item">
                <p>
                  退会すると、CryptoTrendのサービスがご利用いただけなくなります。
                </p>
              </div>
              <div class="c-documentbox__footer">
                <button class="c-btn" @click="withdraw">退会する</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PageTitle from "../PageComponents/PageTitle.vue";
import Loading from "../../layouts/Loading.vue";

import { OK } from "../../util.js";
const PAGE_TITLE = "マイページ";

export default {
  data() {
    return {
      loading: true,
      twitter: false,
      password: false,
      mail: "",
      name: "",
    };
  },
  computed: {
    pageTitle() {
      return PAGE_TITLE;
    },
    isLoading() {
      return this.loading;
    },
    isExistTwitter() {
      return this.twitter;
    },
    isExistPassword() {
      return this.password;
    },
    authMail() {
      return this.mail;
    },
    authName() {
      return this.name;
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
        // フォーム用にデータを格納
        this.user = response.data;
        this.twitter = response.data.twitter_id !== null;
        this.password = response.data.password !== null;
        this.mail = response.data.email;
        this.name = response.data.name;
        this.loading = false;
      } else {
        this.system_error = response.data.errors;
        this.isLoading = false;
      }
    },
    // 退会処理 PHP側でデータ削除して、フロント側で画面遷移させる。
    async withdraw() {
      if (
        confirm(
          "【 CryptoTrendを退会しますか？ 】\n退会すると各種サービスのご利用ができなくなります。"
        )
      ) {
        const response = await axios.post(`/withdraw`);
        if (response.status === OK) {
          window.location = "/";
        } else {
          window.location = "/login";
        }
      }
    },
    // Twitter連携解除
    async twitterUnLinkage() {
      // 更新処理中は複数回起動できないようにする
      if (this.isUpdating) {
        return false;
      }

      // パスワードが設定されていない場合、警告を出して連携解除できないようにする
      if (!this.isExistPassword) {
        alert(
          "【パスワードが設定されていません】\nパスワードが設定されていない状態でTwitter連携を解除すると、ログインができなくなります。\nパスワードを設定してから再度お試しください。"
        );
        this.isUpdating = false;
        return false;
      }

      // はいが選択されたら解除処理を行う
      if (
        confirm(
          "【 Twitterの連携を解除してもよろしいですか？ 】\nTwitterの連携を解除すると、一部の機能がご利用できなくなります。"
        )
      ) {
        this.isUpdating = true;

        // 更新処理にアクセス
        const response = await axios
          .post(`/accounts/un_linkage`)
          .catch((error) => error.response || error);

        // エラーチェック
        if (response.status === OK) {
          // フラッシュメッセージをセット
          this.$store.commit("message/setContentSuccess", {
            content: response.data.success,
          });
          this.twitter = false;
        } else {
          // フラッシュメッセージをセット
          this.$store.commit("message/setContentError", {
            content: response.data.errors,
          });
        }
        this.isUpdating = false;
      }
    },
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後にユーザー取得を行う
        await this.getUser();
      },
      immediate: true,
    },
  },
  components: {
    PageTitle,
    Loading,
  },
};
</script>

<style scoped></style>
