<!--=======================================================-->
<!--ニュース一覧画面でいくつも描画されるニュースリンクへのコンポーネント-->
<!--=======================================================-->
<template>
  <div class="news__item">
    <h2>ニュース</h2>
  </div>
</template>

<script>
export default {
name: "News.vue"
}
</script>

<style scoped>
.news__item{
  height: 80px;
  width: 90%;
  margin: 0 auto;
  border: 1px solid #000;
  background: #d4e9ee;
  color: #0b2e13;
  font-size: 20px;
}
</style>