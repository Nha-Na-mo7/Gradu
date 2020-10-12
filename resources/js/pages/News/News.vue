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
      <h2 class="">{{ entry.title }}</h2>
    </div>
    <!-- 時刻とメディア -->
    <div class="p-news__item--data">
      <div class="p-news__item--time">{{ entry.updated }}</div>
      <div class="p-news__item--media">いくいくファクトリーメディア</div>
    </div>


  </div>
</template>

<script>
export default {
  props: {
    entry: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      currentPath: this.$route.path
    }
  },
  methods: {
    // async emitFetchReports() {
    //   this.$emit('reloadReports');
    // }
  },
  filters: {
    // moment: function (date) {
    //   return moment(date).format('YY/M/D HH:mm')
    // }
  }
}
</script>

<style scoped>

</style>