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
            @click="toggleAutoFollowing"
            v-if="isAutoFollowFlg"
          >
            自動フォローを解除する
          </button>
          <button
            class="c-btn c-btn__follow"
            @click="toggleAutoFollowing"
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
import { OK } from '../../util.js';

export default {
  props: {
    autoFlg: {
      type: Boolean,
      required: true,
    },
    testUserFlg: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    isAutoFollowFlg() {
      return this.autoFlg;
    },
    isTestUserFlg() {
      return this.testUserFlg;
    },
  },
  methods: {
    // 親コンポーネント側でモーダルを閉じる
    closeModal() {
      this.$emit('close');
    },
    // 親コンポーネントの自動フォローフラグを切り替える
    toggleFollowFlgParent() {
      this.$emit('toggleAutoFollowFlg');
    },
    // 自動フォローのON/OFF切り替え
    async toggleAutoFollowing() {
      var result = false;
      const flg = this.isAutoFollowFlg;
      if (flg) {
        result = confirm('自動フォローをOFFにします。よろしいですか？');
      } else {
        result = confirm('自動フォローをONにします。よろしいですか？');
      }
      // confirmではいが選択されたら切り替え処理に入る
      if (result) {
        // テストユーザーの場合は処理は行わない
        if(this.isTestUserFlg) {
          this.$store.commit('message/setContentError', {
            content: '【テストユーザーのため処理は行われません。】本登録をされている場合、連携しているTwitterアカウントでの自動フォローが開始されます。',
            timeout: 8000
          });
        }else{
          const response = await axios
              .post(`/accounts/autofollowflg`, { follow_flg: flg })
              .catch((error) => error.response || error);

          // エラーハンドリング
          if (response.status === OK) {
            // フラッシュメッセージをセット
            this.$store.commit('message/setContentSuccess', {
              content: response.data.success,
            });
            this.toggleFollowFlgParent();
          } else {
            // フラッシュメッセージをセット
            this.$store.commit('message/setContentError', {
              content: 'エラーが発生しました。',
            });
          }
        }
        // モーダルを閉じる
        this.closeModal();
      }
    },
  },
};
</script>

<style scoped></style>
