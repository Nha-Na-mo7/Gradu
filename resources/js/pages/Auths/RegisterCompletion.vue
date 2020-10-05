<!--=================-->
<!--新規登録完了後の画面-->
<!--=================-->
<template>
  <div class="l-container--authform">
    <h2 class="p-auth__title">ユーザー登録が完了しました！</h2>
    <div>
      <p>登録してくれてありがとう！</p>
      <RouterLink
          to="/"
          class="c-btn"
      >マイページ</RouterLink>
      <RouterLink
          to="/"
          class="c-btn"
      >Twitterトレンド分析</RouterLink>
      <RouterLink
          to="/"
          class="c-btn"
      >ニュース</RouterLink>
    </div>
  </div>
</template>

<script>
export default {
name: "RegisterCompletion"
}
</script>

<style scoped>

</style>