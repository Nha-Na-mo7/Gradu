<!--=======================================================-->
<!--ニュース一覧画面でいくつも描画されるニュースリンクへのコンポーネント-->
<!--=======================================================-->
<template>
  <div class="p-news__item p-news__item--entry">
    <!-- 24H以内の記事に付与されるアイコン -->
    <span v-if="is24hour" class="c-icon">NEW!!</span>
    <!-- 写真 -->
    <div class="p-news__item--picture"></div>
    <!-- 記事のタイトル -->
    <div class="p-news__item--title">
      <h2 class="">
        <a
            :href="getUrl"
            target="_blank"
            rel="noopener noreferrer"
        >{{ getTitle }}</a>
      </h2>
    </div>
    <!-- 時刻とメディア -->
    <div class="p-news__item--data">
      <div class="p-news__item--time"><p>{{ getPubDate | newsUpdate }}</p></div>
      <div class="p-news__item--media"><p>{{ getSource }}</p></div>
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
    splitTitle() {
      const split_title = this.entry.title;
      return split_title.split(' - ');
    },
    // 提供メディアを除いたタイトルを返す。
    // タイトルの一番最後に" - "に続く形でメディアが続くため、そこだけを取り除いた文字列を返却
    getTitle() {
      const splitTitle = this.splitTitle
      var title = '';
      for (let i = 0; i < splitTitle.length - 1; i++){
        title += splitTitle[i];
      }
      return title;
    },
    getUrl() {
      return this.entry.url;
    },
    getSource() {
      return this.entry.source;
    },
    getPubDate() {
      // JSのUNIXタイムスタンプは13桁なので*1000する
      return this.entry.pubDate * 1000;
    },
    // 投稿された記事が、現在時刻から見て24時間以内の投稿記事かを判定する
    is24hour() {
      const now = Date.now(); //現在時刻
      const updatedTimeStamp = this.getPubDate; //記事の投稿時刻

      // JSのUNIXタイムスタンプはミリ秒計算13桁なので1000で割って計算。
      return (now - updatedTimeStamp) / 1000 < 60 * 60 * 24;
    }
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後にもニュース取得を行う
        await this.getTime
      },
      immediate: true
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