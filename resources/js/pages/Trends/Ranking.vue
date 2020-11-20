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
      <div class="p-trends__item--time"><p>{{ price_max | add_JPY }}</p></div>
      <div class="p-trends__item--media"><p>{{ price_min | add_JPY }}</p></div>
    </div>
  </div>
</template>


<script>
import { BRAND_ICON_PATH, isArrayExists } from "../../util";
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
      transaction_price: []
    }
  },
  computed: {
    // アイコンのパス
    icon_path() {
      return this.brand.brand.icon;
    },
    // 24時間の最低取引価格
    price_min() {
      return this.transaction_price.price_min
    },
    // 24時間の最高取引価格
    price_max() {
      return this.transaction_price.price_max

    },
    // twitterの検索欄に通貨名が入った状態の検索ページURL
    search_url() {
      return TWITTER_SEARCH_URL + this.brand.brand.name
    },
  },
  methods: {
    async get_transaction_price() {
      const response = await axios.get(`/api/transaction/price`, { params:{ brand_id: this.brand.brand_id } });

      // console.log(response.data)

      if(response.data !== ''){
        this.transaction_price = response.data
      }
    }
  },
  filters: {
    // svgアイコンのパス
    icon_path_filter: function (icon_path)  {
      return BRAND_ICON_PATH + icon_path
    },
    // JPYを付与する。取得できていない場合は不明とする。
    add_JPY: function (price)  {
      if(price >= 0){
        return price + ' JPY'
      }else{
        return '不明'
      }
    },
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、トレンド一覧を取得
        await this.get_transaction_price();
      },
      immediate: true
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