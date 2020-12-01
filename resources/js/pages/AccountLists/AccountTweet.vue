<!--================================================-->
<!--twitterコンポーネント内、新着ツイート欄用のコンポーネント-->
<!--================================================-->
<template>
  <!-- 取得したツイートと日付 -->
  <div class="p-accounts__tweet--data" v-if="existTweet">
    <!-- 実際のツイートテキスト・画像 -->
    <div class="">
      <!-- テキスト -->
      <p class="">{{ this.text }}</p>
      <!-- 画像 -->
      <a
        v-if="existMedia"
        :href="mediaUrl"
        target="_blank"
        rel="noopener noreferrer"
      >
        <img class="" :src="mediaUrl | sizeThumb" :alt="mediaUrl" />
      </a>
    </div>
    <div class="">
      <!-- 日付・ここをクリックするとツイートのURLに飛ぶ -->
      <span class="p-accounts__tweet--span p-accounts__tweet--date">
        <a
          :href="twitterTweetUrl"
          target="_blank"
          rel="noopener noreferrer"
          >{{ this.textCreatedAt | newTweetDate }}</a
        >
      </span>
    </div>
  </div>

  <!-- 新着ツイートが存在しないとき(RTのみで、まだ本人からのツイートがない場合など) -->
  <div class="p-accounts__tweet--data" v-else>
    <span class="p-accounts__tweet--span p-accounts__tweet--nothing">
      ~ このユーザーからのツイートはまだありません ~
    </span>
  </div>
</template>

<script>
import moment from "moment";

export default {
  props: {
    tweet: {
      type: Object,
      required: false,
    },
    account_url: {
      type: String,
      required: true,
    },
  },
  computed: {
    existTweet() {
      return this.tweet !== null && this.tweet.tweet_id_str !== null;
    },
    twitterTweetUrl() {
      return this.account_url + "/status/" + this.tweet.tweet_id_str;
    },
    text() {
      return this.tweet.tweet_text;
    },
    textCreatedAt() {
      return this.tweet.tweet_created_at;
    },
    existMedia() {
      return this.tweet.media_url !== null;
    },
    mediaUrl() {
      return this.tweet.media_url;
    },
  },
  filters: {
    newTweetDate: function (date) {
      return moment(date).format("YYYY-MM-DD HH:mm:ss");
    },
    // 一覧表示する画像はTwitterのthumbサイズで表示。
    sizeThumb: function (media) {
      return media + ":thumb";
    },
  },
};
</script>

<style scoped></style>
