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
      <!-- 通貨アイコン -->
      <img
          :src="currencyIconPath+brandiconpath"
          class="c-trends__item--icon"
      >
      <span class="">MONA</span>
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

export default {
  data() {
    return {
      //TODO 作成用の仮のもの、削除予定
      brandiconpath: 'mona.svg'
    }
  },
  computed: {
    currencyIconPath() {
      return CURRENCY_ICON_PATH;
    },
  },
}
</script>

<style scoped>
.c-trends__item--icon {
  height: 5rem;
  width: 5rem;
}
</style>