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
            :src="account.profile_image_url_https"
            :alt="account.profile_image_url_https">
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
                  {{ account_screen_name |  add_AtSign_to_screen_name }}
                </a>
              </span>
            </div>
          </div>

          <!-- Twitterプロフィール -->
          <div
              class="item-5 p-accounts__profile--description"
              v-if="isExist_profile_description"
          >
            <p>{{ account.description }}</p>
          </div>
        </div>

        <!-- フォローボタンとFF数のエリア -->
        <div class="item-4 p-accounts__follow--area">

          <!-- フォローボタンエリア、そのユーザーがTwitterアカウントを連携していない場合非表示 -->
          <div class="item-5 p-accounts__follow-btn--area">
            <!-- フォローしていないアカウントを優先表示するので、フォローしているアカウントはページ更新すると出てこなくなる-->
            <button
                v-if="isFollowing"
                class="c-btn"
                :class="{'c-btn__disabled': isAutoFollowing}"
                :disabled="isAutoFollowing"
                @click="destroy"
            >フォロー解除</button>
            <button
                v-else
                class="c-btn"
                :class="{'c-btn__disabled': isAutoFollowing}"
                :disabled="isAutoFollowing"
                @click="follow"
            >フォローする</button>
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

      <!-- 新着ツイート1件表示エリア -->
      <div
          class="item-3 p-accounts__tweet--area"
      >
        <AccountTweet
          :account_url="twitter_account_url"
          :tweet="this.account.new_tweet"
        />
      </div>

    </div>


  </div>
</template>

<script>
import { DEFAULT_TWITTER_URL, OK, INTERNAL_SERVER_ERROR, FORBIDDEN } from "../../util";
import AccountTweet from './AccountTweet.vue';

export default {
  props: {
    account: {
      type: Object,
      required: true
    },
    follow_list: {
      type: Array,
      required: true
    },
    auto_follow_flg: {
      type: Boolean,
      required: true
    },
  },
  data() {
    return {
      new_tweet: this.account.new_tweet,
      isFollowing: false,
    }
  },
  computed: {
    account_id() {
      return this.account.account_id
    },
    isAutoFollowing() {
      return this.auto_follow_flg
    },
    account_screen_name() {
      return this.account.screen_name
    },
    isExist_profile_description() {
      return this.account.description !== '';
    },
    twitter_account_url() {
      return DEFAULT_TWITTER_URL + this.account_screen_name;
    },
    twitter_following_url() {
      return this.twitter_account_url + '/following';
    },
    twitter_followers_url() {
      return this.twitter_account_url + '/followers';
    },
  },
  methods: {
    async follow() {
      if (this.isAutoFollowing) {
        console.log('自動フォローをONにしている間は、ボタンからのフォローはできません。')
        return false;
      }
      // フォロー用パラメータオブジェクトを作成
      const follow_param = {
        'user_id': this.account_id,
      }

      const response = await axios.post('../accounts/follow', follow_param).catch(error => error.response || error);

      // エラーハンドリング
      // 403エラーは、「API制限」「フォロー済みのアカウントをフォローしようとする」などで発生する。
      // ※ 二重フォローは、パフォーマンス上の理由で200を返却することもある。
      if(response.status === OK){
        // TODO フラッシュ フォローに成功しました！など
        console.log(response.data);
        this.isFollowing = true;
      }else if (response.status === FORBIDDEN) {
        // TODO フラッシュ レスポンスメッセージを表示。
        console.log(response.data.errors)
        // response.data.errors
      }else{
        // TODO フラッシュ ここにくるのは全部500
        console.log(response.data.errors)
      }
    },
    // フォロー解除
    async destroy() {
      if(this.isAutoFollowing) {
        console.log('自動フォローをONにしている間は、ボタンからのフォロー解除はできません。')
        return false;
      }
      // リムーブ用パラメータオブジェクトを作成
      const destroy_param = {
        'user_id': this.account_id,
      }
      // APIリクエスト
      const response = await axios.post('../accounts/destroy', destroy_param).catch(error => error.response || error);
      // エラーハンドリング(フォロー解除は200か500のみ)
      if(response.status === OK){
        // TODO フラッシュ フォロー解除しました！など
        console.log(response.data);
        this.isFollowing = false;
      }else{
        // TODO フラッシュ ここにくるのは全部500
        console.log(response.data.errors)
      }
    },
    // フォロー状態のチェック
    async isFollowing_check() {
      var check = false;

      // フォローリストをループさせ、TwitterIDと一致していたらtrueを返す
      for (var i = 0, len = this.follow_list.length; i < len; i++) {
        if (this.account_id === this.follow_list[i]['follow_target_id']) {
          check = true;
          break;
        }
      }
      // 一致するTwitterIDがある場合はisFollowingがtrueとなる
      if(check){
        this.isFollowing = true;
      }
    }
  },
  components: {
    AccountTweet
  },
  filters: {
    // ユーザー名にはレスポンスに"@"が付いていないので、付与する
    add_AtSign_to_screen_name: function (screen_name)  {
      return '@' + screen_name
    }
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、DBからTwitterアカウント一覧を取得
        await this.isFollowing_check();
      },
      immediate: true
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