<!--===============================================-->
<!--ランキングページで表示される順位表一枚一枚のコンポーネント-->
<!--===============================================-->
<template>
  <!-- 順位/通貨(アイコン)/(通貨名)/ツイート数/最高取引価格/最低取引価格 -->
  <tr class="p-trends__table--row">
    <td class="p-trends__table--data p-trends__table--rank">{{ this.rank + 1 }}</td>
    <!-- 通貨アイコンは右スワイプ時に追従する -->
    <td class="p-trends__table--data p-trends__table--data--sticky">
      <div class="u-text--center">
        <!-- アイコンか通貨名をクリックするとTwitterの検索ページにリンク -->
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
        </a>
      </div>
    </td>
    <td class="p-trends__table--data">
      <div>
        <!-- アイコンか通貨名をクリックするとTwitterの検索ページにリンク -->
        <a
          class="p-trends__table--link"
          :href="searchUrl"
          target="_blank"
          rel="noopener noreferrer"
        >
          <!-- 通貨名 -->
          <span class="p-trends__item--name">{{ this.brand.brand.name }}</span><br />
          <span class="p-trends__item--realname">{{
            this.brand.brand.realname
          }}</span>
        </a>
      </div>
    </td>
    <td class="p-trends__table--data p-trends__table--count">{{ this.brand.tweet_count }}</td>
    <td class="p-trends__table--data u-text--right">{{ priceMax | addJPY }}</td>
    <td class="p-trends__table--data u-text--right">{{ priceMin | addJPY }}</td>
  </tr>
</template>

<script>
const TWITTER_SEARCH_URL = 'https://twitter.com/search?q=';

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
    transaction: {
      type: Object,
      required: false,
    },
  },
  computed: {
    // アイコンのパス
    iconPath() {
      return this.brand.brand.icon;
    },
    // 24時間の最低取引価格
    priceMin() {
      if (this.transaction != null) {
        return this.transaction.price_min;
      } else {
        return -1;
      }
    },
    // 24時間の最高取引価格
    priceMax() {
      if (this.transaction != null) {
        return this.transaction.price_max;
      } else {
        return -1;
      }
    },
    // twitterの検索欄に通貨名が入った状態の検索ページURL
    searchUrl() {
      return TWITTER_SEARCH_URL + this.brand.brand.name;
    },
  },
  filters: {
    // JPYを付与する。
    // 取得できない通貨の場合はcomputedが-1を返す。-1が帰ってきた時、「不明」と表示する。
    addJPY: function (price) {
      if (price >= 0) {
        return price + ' JPY';
      } else {
        return '不明';
      }
    },
  },
};
</script>
