<!--===============================================-->
<!--ランキングページで表示される順位表一枚一枚のコンポーネント-->
<!--===============================================-->
<template>
  <tr>
    <td>{{ this.rank + 1 }}</td>
    <td class="">
      <div>
        <a
          class="p-trends__table--link"
          :href="searchUrl"
          target="_blank"
          rel="noopener noreferrer"
        >
          <!-- 通貨アイコン -->
          <img
            :src="iconPath"
            class="p-trends__table--icon"
            :alt="this.brand.brand.name"
          />
          <!-- 通貨名(クリックするとtwitter検索ページにリンク) -->
          <span class="p-trends__item--name">{{ this.brand.brand.name }}</span>
          <span class="p-trends__item--realname">{{
            this.brand.brand.realname
          }}</span>
        </a>
      </div>
    </td>
    <td class="p-trends__table--count">{{ this.brand.tweet_count }}</td>
    <td class="u-text--right">{{ priceMax | addJPY }}</td>
    <td class="u-text--right">{{ priceMin | addJPY }}</td>
  </tr>
</template>

<script>
import { OK } from "../../util";

const TWITTER_SEARCH_URL = "https://twitter.com/search?q=";

export default {
  props: {
    brand: {
      type: Object,
      required: true,
    },
    rank: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      transactionPrice: [],
    };
  },
  computed: {
    // アイコンのパス
    iconPath() {
      return this.brand.brand.icon;
    },
    // 24時間の最低取引価格
    priceMin() {
      return this.transactionPrice.price_min;
    },
    // 24時間の最高取引価格
    priceMax() {
      return this.transactionPrice.price_max;
    },
    // twitterの検索欄に通貨名が入った状態の検索ページURL
    searchUrl() {
      return TWITTER_SEARCH_URL + this.brand.brand.name;
    },
  },
  methods: {
    // 24時間以内の取引価格の取得
    async getTransactionPrice() {
      const response = await axios.get(`/transaction/price`, {
        params: { brand_id: this.brand.brand_id },
      });

      // 通信成功時(取引価格だけ取得エラーするのは考えにくいが、もし起きた場合は「不明」と表示させる)
      if (response.status === OK && response.data !== "") {
        this.transactionPrice = response.data;
      }
    },
  },
  filters: {
    // JPYを付与する。取得できていない場合は不明とする。
    addJPY: function (price) {
      if (price >= 0) {
        return price + " JPY";
      } else {
        return "不明";
      }
    },
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、トレンド一覧を取得
        await this.getTransactionPrice();
      },
      immediate: true,
    },
  },
};
</script>

<style scoped></style>
