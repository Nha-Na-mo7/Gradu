<!--==========================-->
<!--オートフォローの説明用のモーダル-->
<!--==========================-->
<template>
  <div>
    <!-- モーダルカバー -->
    <!-- 画面がクリックでモーダルを閉じる。.selfを付与して子要素にクローズイベントが伝播しないようにする-->
    <div class="c-modal__cover" @click.self="closeModal"></div>

    <!-- モーダルコンテンツ -->
    <div class="c-modal">
      <div class="c-modal__head">
        <h1 class="c-modal__head-title">自動フォロー</h1>
      </div>

      <div class="c-modal__main">
        <h2 class="c-modal__main--info">
          アカウント一覧ページの全Twitterアカウントを、自動でフォローします。
        </h2>
        <ul class="c-modal__main--description">
          <li class="c-modal__main--li">
            連携済みのTwitterアカウントでフォローされます。
          </li>
          <li class="c-modal__main--li">
            フォローは約30分に5人のペースで行われるため、反映には少し時間がかかります。
          </li>
          <li class="c-modal__main--li">
            自動フォローをONにしている間、アカウント一覧ページからはフォロー出来ません。
          </li>
          <li class="c-modal__main--li">
            自動フォローはいつでも解除することが可能です。
          </li>
        </ul>
      </div>

      <!-- 選択肢 -->
      <div class="c-modal__btn--inner">
        <div class="c-modal__btn">
          <button
            class="c-btn c-btn__follow c-btn__follow--destroy"
            @click="toggle_auto_following"
            v-if="is_auto_follow_flg"
          >
            自動フォローを解除する
          </button>
          <button
            class="c-btn c-btn__follow"
            @click="toggle_auto_following"
            v-else
          >
            自動フォローを開始する
          </button>
        </div>
        <button class="c-btn c-btn__modal" @click="closeModal">閉じる</button>
      </div>
    </div>
  </div>
</template>
<script>
import { OK } from "../../util.js";

export default {
  props: {
    auto_flg: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    is_auto_follow_flg() {
      return this.auto_flg;
    },
  },
  methods: {
    // 親コンポーネント側でモーダルを閉じる
    closeModal() {
      this.$emit("close");
    },
    // 親コンポーネントの自動フォローフラグを切り替える
    toggle_follow_flg_parent() {
      this.$emit("toggle_auto_follow_flg");
    },
    // 自動フォローのON/OFF切り替え
    async toggle_auto_following() {
      var result = false;
      const flg = this.is_auto_follow_flg;
      if (flg) {
        result = confirm("自動フォローをOFFにします。よろしいですか？");
      } else {
        result = confirm("自動フォローをONにします。よろしいですか？");
      }
      // confirmではいが選択されたら切り替えを行う
      if (result) {
        const response = await axios
          .post(`/accounts/autofollowflg`, { follow_flg: flg })
          .catch((error) => error.response || error);

        // エラーハンドリング
        if (response.status === OK) {
          // フラッシュメッセージをセット
          this.$store.commit("message/setContentSuccess", {
            content: response.data.success,
          });
          this.toggle_follow_flg_parent();
        } else {
          // フラッシュメッセージをセット
          this.$store.commit("message/setContentError", {
            content: "エラーが発生しました。",
          });
        }
        // モーダルを閉じる
        this.closeModal();
      }
    },
  },
};
</script>

<style scoped></style>
