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
      <div class="c-modal__head"><span class="c-modal__head-title">自動フォロー</span></div>

      <div class="c-modal__info">
        <h2>自動フォロー</h2>
        <p>本ページに表示されているTwitterアカウントを自動でフォローする機能です。</p>
        <ul>
          <li>フォローは約30分に5人のペースで行われます。</li>
          <li>自動フォローをONにしている間は、こちらのページからのフォローは出来なくなります。</li>
          <li>自動フォローはいつでも解除することが可能です。</li>
        </ul>
      </div>

      <!-- 選択肢 -->
      <div class="c-modal__btn-area">
        <div class="">
          <button
              class="c-btn c-btn__follow c-btn__follow--destroy"
              @click="toggle_auto_following"
              v-if="is_auto_follow_flg"
          >自動フォロー中...</button>
          <button
              class="c-btn c-btn__follow"
              @click="toggle_auto_following"
              v-else
          >START AUTO-FOLLOW</button>
        </div>
        <button class="c-btn" @click="closeModal">閉じる</button>
      </div>

    </div>
  </div>

</template>
<script>
import { OK } from '../../util.js';

export default {
  props: {
    auto_flg: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    is_auto_follow_flg() {
      return this.auto_flg
    }
  },
  methods: {
    // 親コンポーネント側でモーダルを閉じる
    closeModal() {
      this.$emit('close');
    },
    // 親コンポーネントの自動フォローフラグを切り替える
    toggle_follow_flg_parent() {
      this.$emit('toggle_auto_follow_flg')
    },
    // 自動フォローのON/OFF切り替え
    async toggle_auto_following() {
      var result = false;
      const flg = this.is_auto_follow_flg;
      if( flg ) {
        result = confirm('自動フォローをOFFにします。よろしいですか？')
      } else {
        result = confirm('自動フォローをONにします。よろしいですか？')
      }
      // confirmではいが選択されたら切り替えを行う
      if(result) {
        const response = await axios
            .post(`/accounts/autofollowflg`, {'follow_flg': flg})
            .catch(error => error.response || error);

        // エラーハンドリング
        if(response.status === OK) {
          // フラッシュメッセージをセット
          this.$store.commit('message/setContentSuccess', {
            content: response.data.success
          })
          this.toggle_follow_flg_parent();
        }else{
          // フラッシュメッセージをセット
          this.$store.commit('message/setContentError', {
            content: 'エラーが発生しました。'
          })
        }
        // モーダルを閉じる
        this.closeModal();
      }
    },
  }
}
</script>

<style scoped>

</style>