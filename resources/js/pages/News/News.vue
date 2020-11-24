<!--=======================================================-->
<!--ニュース一覧画面でいくつも描画されるニュースリンクへのコンポーネント-->
<!--=======================================================-->
<template>
  <div class="p-news__item p-news__item--entry">
    <!-- 24H以内の記事に付与されるアイコン -->
    <span v-if="is_sub_24hour" class="c-icon">NEW!!</span>
    <!-- 記事のタイトル -->
    <div class="p-news__item--title">
      <h2 class="">
        <a
            :href="get_url"
            target="_blank"
            rel="noopener noreferrer"
        >{{ get_title }}</a>
      </h2>
    </div>
    <!-- 時刻とメディア -->
    <div class="p-news__item--data">
      <div class="p-news__item--time"><p>{{ get_pubDate | news_update }}</p></div>
      <div class="p-news__item--media"><p>{{ get_source }}</p></div>
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
      currentPath: this.$route.path,
    }
  },
  computed: {
    // " - "で区切る
    split_to_title() {
      const split_title = this.entry.title;
      return split_title.split(' - ');
    },
    // 提供メディアを除いたタイトルを返す。
    // タイトルの一番最後に" - "に続く形でメディアが続くため、そこだけを取り除いた文字列を返却
    get_title() {
      const split_title = this.split_to_title
      var title = '';
      for (let i = 0; i < split_title.length - 1; i++){
        title += split_title[i];
      }
      return title;
    },
    get_url() {
      return this.entry.url;
    },
    get_source() {
      return this.entry.source;
    },
    get_pubDate() {
      // JSのUNIXタイムスタンプは13桁なので*1000する
      return this.entry.pubDate * 1000;
    },
    // 投稿された記事が、現在時刻から見て24時間以内の投稿記事かを判定する
    is_sub_24hour() {
      const now = Date.now(); //現在時刻
      const updated_timestamp = this.get_pubDate; //記事の投稿時刻

      // JSのUNIXタイムスタンプはミリ秒計算13桁なので1000で割って計算。
      return (now - updated_timestamp) / 1000 < 60 * 60 * 24;
    }
  },
  filters: {
    news_update: function (date) {
      return moment(date).format('YYYY/MM/DD HH:mm')
    }
  }
}
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