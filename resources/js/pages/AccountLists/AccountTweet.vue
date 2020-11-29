<!--================================================-->
<!--twitterコンポーネント内、新着ツイート欄用のコンポーネント-->
<!--================================================-->
<template>
  <!-- 取得したツイートと日付 -->
  <div
    class="p-accounts__tweet--data"
    v-if="exist_tweet"
  >
    <!-- 実際のツイートテキスト・画像 -->
    <div class="">
      <!-- テキスト -->
      <p class="">{{ this.text }}</p>
      <!-- 画像 -->
      <a
          v-if="exist_media"
          :href="media_url"
          target="_blank"
          rel="noopener noreferrer"
      >
        <img
            class=""
            :src="media_url | size_thumb"
            :alt="media_url">
      </a>
    </div>
    <div class="">
      <!-- 日付・ここをクリックするとツイートのURLに飛ぶ -->
      <span class="p-accounts__tweet--span p-accounts__tweet--date">
          <a
              :href="twitter_tweet_url"
              target="_blank"
              rel="noopener noreferrer"
          >{{ this.text_created_at | new_tweet_date }}</a>
      </span>
    </div>
  </div>

  <!-- 新着ツイートが存在しないとき(RTのみで、まだ本人からのツイートがない場合など) -->
  <div
      class="p-accounts__tweet--data"
      v-else
  >
    <span class="p-accounts__tweet--span p-accounts__tweet--nothing">
      ~  このユーザーからのツイートはまだありません ~
    </span>
  </div>
</template>

<script>
import moment from "moment";

export default {
  props: {
    tweet: {
      type: Object,
      required: false
    },
    account_url: {
      type: String,
      required: true
    }
  },
  computed: {
    exist_tweet() {
      return (this.tweet !== null && this.tweet.tweet_id_str !== null);
    },
    twitter_tweet_url() {
      return this.account_url + '/status/' + this.tweet.tweet_id_str;
    },
    text() {
      return this.tweet.tweet_text;
    },
    text_created_at() {
      return this.tweet.tweet_created_at;
    },
    exist_media() {
      return (this.tweet.media_url !== null);
    },
    media_url() {
      return this.tweet.media_url;
    },
  },
  filters: {
    new_tweet_date: function (date) {
      return moment(date).format('YYYY-MM-DD HH:mm:ss')
    },
    // 一覧表示する画像はTwitterのthumbサイズで表示。
    size_thumb: function (media) {
      return media + ':thumb';
    },
  }
}
</script>

<style scoped>

</style>