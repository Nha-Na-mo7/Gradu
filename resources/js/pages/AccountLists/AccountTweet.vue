<!--================================================-->
<!--twitterコンポーネント内、新着ツイート欄用のコンポーネント-->
<!--================================================-->
<template>
  <!-- 取得したツイートと日付 -->
  <div
      class="item-4 p-accounts__tweet p-accounts__tweet--data"
      v-if="isExistTweet"
  >
    <!-- 実際のツイートテキスト -->
    <p class="p-accounts__tweet">{{ this.text }}</p>
    <!-- 日付・ここをクリックするとツイートのURLに飛ぶ -->
    <span class="p-accounts__tweet p-accounts__tweet--span p-accounts__tweet--date">
            <a
                :href="twitter_tweet_url"
                target="_blank"
                rel="noopener noreferrer"
            >{{ this.text_created_at | new_tweet_date }}</a>
          </span>
  </div>

  <!-- 新着ツイートが存在しないとき(鍵アカウントではないがまだ本人からのツイートがない場合など) -->
  <div
      class="item-4 p-accounts__tweet--data"
      v-else
  >
    <span class="p-accounts__tweet p-accounts__tweet--span p-accounts__tweet--nothing"> ~  このユーザーからのツイートはまだありません ~ </span>
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
    isExistTweet() {
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
  },
  filters: {
    new_tweet_date: function (date) {
      return moment(date).format('YYYY-MM-DD HH:mm:ss')
    },
  }
}
</script>

<style scoped>

</style>