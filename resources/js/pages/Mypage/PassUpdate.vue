<template>
  <div class="l-container__content">
    <!-- サイトリンク -->
    <SiteLinknav :currentPageTitle='page_title'/>

    <!-- ページタイトル -->
    <PageTitle :title='page_title'/>

    <!-- twitterログインなどでパスワードが初期登録されていない場合 -->
    <div>
      <h2>パスワードが未登録です。</h2>
      <p>Twitterでのログインは可能ですが、今後Twitterの連携を解除した場合、パスワードを設定していなければログインすることができなくなります。</p>

      <h2>パスワード</h2>
      <input type="password">
      <h2>パスワードの再入力</h2>
      <input type="password">
    </div>


    <!-- 既にパスワードが存在するとき -->
    <div>
      <h2>パスワードの変更</h2>
      <p>現在のパスワードと、変更後のパスワード・及び再入力を行ってください。</p>

      <h2>現在のパスワード</h2>
      <input type="password">

      <h2>新しいパスワード</h2>
      <input type="password">

      <h2>新しいパスワードの再入力</h2>
      <input type="password">

    </div>



  </div>
</template>

<script>
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
const PAGE_TITLE = 'パスワードの設定';

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