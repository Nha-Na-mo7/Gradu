<!--=======================================================-->
<!--アカウント一覧画面でいくつも描画される各twitterアカウントの情報-->
<!--=======================================================-->

<!-- item2~7のクラス、及びscope内のスタイルは、本番に上げる前に外してください！ -->


<template>

  <div class="p-accounts__item">


    <!-- サムネイル表示エリア、縦に長く、他のコンテンツは設置しない -->
    <div class="item-2 item2-left p-accounts__left-side">
      <div class="item-3 p-accounts__icon--area">
        <img
            class="p-accounts__icon"
            :src="account.replaced_full_img"
            alt="picture">
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
              <p>{{ account.name }}</p>
            </div>
            <!-- Twitterユーザー名 -->
            <div class="item-6 p-accounts__profile--username">
              <p>{{ account.screen_name |  add_AtSign_to_screen_name }}</p>
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
              <div class="item-7 p-accounts__ff--count"><p>{{ account.friends_count }}</p></div>
              <div class="item-7 p-accounts__ff--title"><p>フォロー中</p></div>
            </div>
            <div class="item-6 p-accounts__ff--item">
              <div class="item-7 p-accounts__ff--count"><p>{{ account.followers_count }}</p></div>
              <div class="item-7 p-accounts__ff--title"><p>フォロワー</p></div>
            </div>

          </div>

        </div>

      </div>

      <!-- 新着ツイート1件表示エリア -->
      <div class="item-3 p-accounts__tweet--area">
        <div class="item-4 p-accounts__tweet">
          <p>新着ツイート</p>
        </div>

        <!-- 取得したツイートと日付 -->
        <div class="item-4 p-accounts__tweet--data">
          <p>{{ this.account_text }}</p>
          <span>{{ this.account_text_created_at | new_tweet_date }}</span>
        </div>

      </div>

    </div>


  </div>
</template>

<script>

import moment from "moment";

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