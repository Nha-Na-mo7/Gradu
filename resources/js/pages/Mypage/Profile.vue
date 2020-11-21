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
      <span>ユーザーネーム</span>
      <span>ユーザーネームは2~16文字で入力してください</span>
      <!-- DBから現在のユーザーネームを取得し、入力された状態にしておく-->
      <input type="text">
      <button class="c-btn">変更を保存</button>
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
const PAGE_TITLE = 'プロフィール編集';

export default {
  computed: {
    page_title() {
      return PAGE_TITLE
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