<!--=======================================================-->
<!--アカウント一覧画面でいくつも描画される各twitterアカウントの情報-->
<!--=======================================================-->

<!-- item2~7のクラス、及びscope内のスタイルは、本番に上げる前に外してください！ -->


<template>

  <div class="p-accounts__item">


    <!-- サムネイル表示エリア、縦に長く、他のコンテンツは設置しない -->
    <div class="item-2 item2-left p-accounts__left-side">
      <div class="item-3 p-accounts__icon--area">
        <a
            :href="twitter_account_url"
            target="_blank"
            rel="noopener noreferrer"
        >
        <img
            class="p-accounts__icon"
            :src="account.replaced_full_img"
            alt="picture">
        </a>
      </div>
    </div>


    <div class="item-2 p-accounts__right-side">

      <!-- プロフィールとフォロー関連のエリア -->
      <div class="item-3 p-accounts__data--area">

        <!-- プロフィールエリア -->
        <div class="item-4 p-accounts__profile--area">
          <div class="item-5 p-accounts__profile">
            <!-- アカウント名 -->
            <div class="item-6 p-accounts__profile--nickname">
              <p>
                <a
                    :href="twitter_account_url"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                  {{ account.name }}
                </a>
              </p>
            </div>
            <!-- Twitterユーザー名 -->
            <div class="item-6 p-accounts__profile--username">
              <span>
                <a
                    :href="twitter_account_url"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                  {{ account.screen_name |  add_AtSign_to_screen_name }}
                </a>
              </span>
            </div>
          </div>

          <!-- Twitterプロフィール -->
          <div class="item-5 p-accounts__profile--description">
            <p>{{ account.description }}</p>
          </div>
        </div>

        <!-- フォローボタンとFF数のエリア -->
        <div class="item-4 p-accounts__follow--area">

          <!-- フォローボタンエリア、そのユーザーがTwitterアカウントを連携していない場合非表示 -->
          <div class="item-5 p-accounts__follow-btn--area">
            <!-- フォローしていないアカウントを優先表示するので、フォローしているアカウントはページ更新すると出てこなくなる-->
            <button class="c-btn" v-if="isFollowing">フォロー中</button>
            <button class="c-btn" v-else>フォロー</button>
          </div>

          <!-- FF数 -->
          <div class="item-5 p-accounts__ff--area">

            <div class="item-6 p-accounts__ff--item">
              <div class="item-7 p-accounts__ff--count">
                <p>
                  <a
                    :href="twitter_following_url"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    {{ account.friends_count }}
                  </a>
                </p>
              </div>
              <div class="item-7 p-accounts__ff--title"><p>フォロー中</p></div>
            </div>
            <div class="item-6 p-accounts__ff--item">
              <div class="item-7 p-accounts__ff--count">
                <p>
                  <a
                      :href="twitter_followers_url"
                      target="_blank"
                      rel="noopener noreferrer"
                  >
                    {{ account.followers_count }}
                  </a>
                </p>
              </div>
              <div class="item-7 p-accounts__ff--title"><p>フォロワー</p></div>
            </div>

          </div>

        </div>

      </div>

      <!-- 新着ツイート1件表示エリア (鍵アカウントの場合は非表示)-->
      <div
          class="item-3 p-accounts__tweet--area"
          v-if="!twitter_protected"
      >
        <!-- 取得したツイートと日付 -->
        <div class="item-4 p-accounts__tweet--data">
          <p>{{ this.account_text }}</p>
          <span>
            <a
              :href="twitter_tweet_url"
              target="_blank"
              rel="noopener noreferrer"
            >{{ this.account_text_created_at | new_tweet_date }}</a>
          </span>
        </div>

      </div>

    </div>


  </div>
</template>

<script>

import moment from "moment";
const DEFAULT_TWITTER_URL = 'https://twitter.com/';

export default {
  props: {
    account: {
      type: Object,
      required: true
    }
  },
  computed: {
    isFollowing() {
      return this.account.following;
    },
    account_text() {
      return this.account.status.text;
    },
    account_text_created_at() {
      return this.account.status.created_at;
    },
    twitter_account_url() {
      return DEFAULT_TWITTER_URL + this.account.screen_name;
    },
    twitter_following_url() {
      return this.twitter_account_url + '/following';
    },
    twitter_followers_url() {
      return this.twitter_account_url + '/followers';
    },
    twitter_tweet_url() {
      return this.twitter_account_url + '/status/' + this.account.status.id_str;
    },
    twitter_protected() {
      return this.account.protected;
    },

  },
  filters: {
    new_tweet_date: function (date) {
      return moment(date).format('YYYY-MM-DD HH:mm:ss')
    },
    // ユーザー名にはレスポンスに"@"が付いていないので、付与する
    add_AtSign_to_screen_name: function (screen_name)  {
      return '@' + screen_name
    }
  }
}
</script>

<style scoped>
.p-accounts__item {
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

.item-2 {
  /*background: #c5ccd9;*/
}
.item-3 {
  /*background: #bfabcb;*/
}
.item-4 {
  /*background: #c4ceff;*/
}
.item-5 {
  /*background: #d2eed5;*/
}
.item-6 {
  /*background: #ffdaf7;*/
}
.item-7 {
  /*background: #b5fff0;*/
}

</style>