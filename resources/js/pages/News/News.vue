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
      <h2 class=""><a :href="this.entry.url">{{ getTitle }}</a></h2>
    </div>
    <!-- 時刻とメディア -->
    <div class="p-news__item--data">
      <div class="p-news__item--time"><p>{{ entry.updated | newsUpdate }}</p></div>
      <div class="p-news__item--media"><p>{{ getMedia }}</p></div>
    </div>


  </div>
</template>

<script>
import moment from 'moment';

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
  computed: {
    splitTitle() {
      return this.entry.title.split(' - ');
    },
    getTitle() {
      const title = this.splitTitle;
      const media = title.pop();
      console.log(media)
      return title;
    },
    getMedia() {
      const title = this.splitTitle;
      const media = title.pop();
      console.log(media)
      return media;
    }
  },
  filters: {
    newsUpdate: function (date) {
      return moment(date).format('YYYY/MM/DD HH:mm')
    }
  }
}
</script>

<style scoped>

</style>