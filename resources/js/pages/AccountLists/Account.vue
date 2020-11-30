<!--=======================================================-->
<!--アカウント一覧画面でいくつも描画される各twitterアカウントの情報-->
<!--=======================================================-->

<template>

  <div class="p-accounts__item">

    <!-- アカウントアイコン表示エリア -->
    <div class="p-accounts__column p-accounts__column--left">
      <div class="p-accounts__icon--container">
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

    <!-- アイコン以外のコンテンツ -->
    <div class="p-accounts__column p-accounts__column--right">

      <!-- ユーザー名とフォローボタン -->
      <div class="p-accounts__data">
        <!-- アカウント名など -->
        <div class="p-accounts__name">
          <!-- アカウント名 -->
          <div class="p-accounts__name--nickname">
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
          <div class="p-accounts__name--username">
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

        <!-- フォローボタンエリア -->
        <div class="p-accounts__btn--area">
          <button
              v-if="follow_flg"
              class="c-btn c-btn__follow c-btn__follow--destroy"
              :class="{'c-btn__disabled': isAutoFollowing}"
              :disabled="isAutoFollowing"
              @click="destroy"
          >フォロー解除</button>
          <button
              v-else
              class="c-btn c-btn__follow"
              :class="{'c-btn__disabled': isAutoFollowing}"
              :disabled="isAutoFollowing"
              @click="follow"
          >フォローする</button>
        </div>
      </div>

      <!-- Twitterプロフィール -->
      <div
          class="p-accounts__description"
          v-if="isExist_profile_description"
      >
        <p>{{ account.description }}</p>
      </div>

      <!-- FF数 -->
      <div class="p-accounts__ff">

        <div class="p-accounts__ff--item">
          <div class="p-accounts__ff--title"><p>フォロー</p></div>
          <div class="p-accounts__ff--count">
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
        </div>

        <div class="p-accounts__ff--item">
          <div class="p-accounts__ff--title"><p>フォロワー</p></div>
          <div class="p-accounts__ff--count">
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
        </div>
      </div>

      <!-- 新着ツイート1件表示エリア -->
      <div
          class="p-accounts__tweet"
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
    follow_flg: {
      type: Boolean,
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
      firstFollowCheck: false
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
    check_follow_status: function () {
      return function() {
        if(this.firstFollowCheck) {
          return this.isFollowing
        }else{
          this.firstFollowCheck = true;
          return this.follow_flg
        }
      }
    }
  },
  methods: {
    async follow() {
      if (this.isAutoFollowing) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: '自動フォローをONにしている場合、ボタンからのフォローはできません。'
        })
        return false;
      }
      // フォロー用パラメータオブジェクトを作成
      const follow_param = {
        'user_id': this.account_id,
      }

      // APIにリクエスト
      const response = await axios
          .post('../accounts/follow', follow_param)
          .catch(error => error.response || error);

      // エラーハンドリング
      // 403エラーは、「API制限」「フォロー済みのアカウントをフォローしようとする」などで発生する。
      // ※ 二重フォローは、パフォーマンス上の理由で200を返却することもある。
      if(response.status === OK){
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success
        })
        this.follow_flg = true;
      }else if (response.status === FORBIDDEN) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.errors
        })
      }else{
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.errors
        })
      }
    },
    // フォロー解除
    async destroy() {
      if(this.isAutoFollowing) {
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: '自動フォローをONにしている場合、ボタンからフォロー解除はできません。'
        })
        return false;
      }
      // リムーブ用パラメータオブジェクトを作成
      const destroy_param = {
        'user_id': this.account_id,
      }
      // APIにリクエスト
      const response = await axios
          .post('../accounts/destroy', destroy_param)
          .catch(error => error.response || error);

      // エラーハンドリング(フォロー解除は200か500のみ)
      if(response.status === OK){
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentSuccess', {
          content: response.data.success
        })
        this.follow_flg = false;
      }else{
        // フラッシュメッセージをセット
        this.$store.commit('message/setContentError', {
          content: response.data.errors
        })
      }
    },
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
}
</script>

<style scoped>
.p-accounts__item {
}


</style>