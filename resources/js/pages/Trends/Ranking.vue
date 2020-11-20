<!--===============================================-->
<!--ランキングページで表示される順位表一枚一枚のコンポーネント-->
<!--===============================================-->
<template>
<!--  スマホの場合は1画面に情報が収まりきらないので、タップorスワイプなどで情報を切り替えられるようにする-->

  <div class="p-trends__item">
    <!-- 順位パネル -->
    <div class="p-trends__item--rank">
    </div>

    <!-- 銘柄名 -->
    <div class="p-trends__item--brandname">
      <a
          :href="search_url"
          target="_blank"
          rel="noopener noreferrer"
      >
        <!-- 通貨アイコン -->
        <img
            :src="icon_path | icon_path_filter"
            class="c-trends__item--icon"
        >
        <!-- 通貨名(クリックするとtwitter検索ページにリンク) -->
        <span class="">{{ this.brand.brand.name }}</span>
        <span class="c-trends__item--realname">{{ this.brand.brand.realname }}</span>
      </a>
    </div>

    <!-- ツイート数 -->
    <div class="p-trends__item--tweetcount">
      <span class="">{{ this.brand.tweet_count }}</span>
    </div>


    <!-- 最高取引価格 & 最低取引価格 -->
    <div class="p-trends__item--data">
      <div class="p-trends__item--time"><p>1000000円</p></div>
      <div class="p-trends__item--media"><p>300円</p></div>
    </div>
  </div>
</template>




<script>
import {CURRENCY_ICON_PATH, isArrayExists} from "../../util";
import {mapState} from "vuex";

const TWITTER_SEARCH_URL = 'https://twitter.com/search?q=';

export default {
  props: {
    brand: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      //TODO 作成用の仮のもの、削除予定
      brandiconpath: 'mona.svg'
    }
  },
  computed: {
    // アイコンのパス
    icon_path() {
      return this.brand.brand.icon;
    },
    // 24時間の最低取引価格・取得できない場合は「不明」
    price_min() {
      return this.brand.brand.icon;
    },
    // 24時間の最高取引価格・取得できない場合は「不明」
    price_max() {
      return this.brand.brand.icon;
    },
    // twitterの検索欄に通貨名が入った状態の検索ページURL
    search_url() {
      return TWITTER_SEARCH_URL + this.brand.brand.name
    }
  },
  filters: {
    icon_path_filter: function (icon_path)  {
      return CURRENCY_ICON_PATH + icon_path
    }
  }
}
</script>

<style scoped>
.c-trends__item--icon {
  height: 5rem;
  width: 5rem;
}
.c-trends__item--realname {
  font-size: 10px;
}
</style>