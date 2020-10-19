<!--=======================================================-->
<!--ニュース一覧画面でいくつも描画されるニュースリンクへのコンポーネント-->
<!--=======================================================-->
<template>
  <div class="p-news__item">
    <!-- 24H以内の記事に付与されるアイコン -->
    <span v-if="is24hour" class="c-icon">NEW!!</span>
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
    getMedia() {
      const arr = this.splitTitle;
      return arr[arr.length - 1];
    },

    // 投稿された記事が、現在時刻から見て24時間以内の投稿記事かを判定する
    is24hour() {
      const now = Date.now(); //現在時刻
      const updatedTimeStamp = Date.parse(this.entry.updated); //記事の投稿時刻

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

</style>