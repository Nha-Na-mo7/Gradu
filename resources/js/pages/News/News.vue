<!--=======================================================-->
<!--ニュース一覧画面でいくつも描画されるニュースリンクへのコンポーネント-->
<!--=======================================================-->
<template>
  <div class="p-news__item">
    <!-- 24H以内の記事に付与されるアイコン -->
    <span class="c-icon">NEW!!</span>
    <!-- 写真 -->
    <div class="p-news__item--picture"></div>
    <!-- 記事のタイトル -->
    <div class="p-news__item--title">
      <h2 class="">鳥貴族、錬金術終了へ | 新型コロナウイルスのキャンペーンを悪用</h2>
    </div>
    <!-- 時刻とメディア -->
    <div class="p-news__item--data">
      <div class="p-news__item--time">2020/18/39 99:99:59</div>
      <div class="p-news__item--media">いくいくファクトリーメディア</div>
    </div>


  </div>
</template>

<script>
export default {
name: "News.vue"
}
</script>

<style scoped>

</style>