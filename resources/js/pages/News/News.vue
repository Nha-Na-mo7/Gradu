<!--=======================================================-->
<!--ニュース一覧画面でいくつも描画されるニュースリンクへのコンポーネント-->
<!--=======================================================-->
<template>
<!--  <div class="p-news__item&#45;&#45;container">-->
  <div class="p-news__item" :class="{ 'p-news__new': isSub24hour }">
    <!--      &lt;!&ndash; 24H以内の記事に付与されるアイコン &ndash;&gt;-->
    <span v-if="isSub24hour" class="c-icon__new">NEW!!</span>
    <!-- 記事のタイトル -->
    <div class="p-news__item--title">
      <h2>
        <a
            class="p-news__item--title--link"
            :href="getEntryUrl"
            target="_blank"
            rel="noopener noreferrer"
        >{{ getEntryTitle }}</a
        >
      </h2>
    </div>
    <!-- 時刻とメディア -->
    <div class="p-news__item--data">
      <span class="p-news__item--time">{{ getPubDate | newsUpdate }}</span>
      <span class="p-news__item--media">{{ getEntrySource }}</span>
    </div>
  </div>
<!--  <div class="p-news__item&#45;&#45;container">-->
<!--    <div class="p-news__item&#45;&#45;data&#45;&#45;container">-->
<!--    </div>-->
<!--  </div>-->
</template>

<script>
import moment from "moment";

export default {
  props: {
    entry: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      currentPath: this.$route.path,
    };
  },
  computed: {
    // " - "で区切る
    splitToTitle() {
      const split_title = this.entry.title;
      return split_title.split(" - ");
    },
    // 提供メディアを除いたタイトルを返す。
    // タイトルの一番最後に" - "に続く形でメディアが続くため、そこだけを取り除いた文字列を返却
    getEntryTitle() {
      const split_title = this.splitToTitle;
      var title = "";
      for (let i = 0; i < split_title.length - 1; i++) {
        title += split_title[i];
      }
      return title;
    },
    getEntryUrl() {
      return this.entry.url;
    },
    getEntrySource() {
      return this.entry.source;
    },
    getPubDate() {
      // JSのUNIXタイムスタンプは13桁なので*1000する
      return this.entry.pubDate * 1000;
    },
    // 投稿された記事が、現在時刻から見て24時間以内の投稿記事かを判定する
    isSub24hour() {
      const now = Date.now(); //現在時刻
      const updated_timestamp = this.getPubDate; //記事の投稿時刻

      // JSのUNIXタイムスタンプはミリ秒計算13桁なので1000で割って計算。
      return (now - updated_timestamp) / 1000 < 60 * 60 * 24;
    },
  },
  filters: {
    newsUpdate: function (date) {
      return moment(date).format("YYYY/MM/DD HH:mm");
    },
  },
};
</script>

<style scoped>
.p-news__item {
  animation: fadeIn 1s;
}
@keyframes fadeIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}
</style>
