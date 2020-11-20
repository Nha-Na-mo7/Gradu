<!--===============================-->
<!--通貨絞り込み用のアコーディオンメニュー-->
<!--===============================-->
<template>
  <div>
    <!-- アコーディオンコンテンツ -->
    <div class="c-accordion">
      <button class="c-accordion__btn">絞り込む(アコーディオンオープン)</button>

      <div class="">
        <!--  通過での絞り込み -->
        <div class="">
          <label>
            <input
                type="button"
                name="brand_all"
                class="c-btn"
                value="リセット"
                @click=""
            >
          </label>
          <p class="">チェックした通貨に絞ってランキングを表示します。</p>

          <div class="">

            <!-- 通貨一覧 -->
            <div
                class=""
                v-for="brand in fetched_brands"
                :key="brand.id"
                @change=""
            >
              <label :for="brand.id - 1">
                <input type="checkbox"
                       name="brand"
                       :value="brand.name"
                       :id="brand.id - 1"
                       v-model="checked_id"
                       @change="isChecked"
                >
                <img
                    v-if="brand.icon"
                    :src="brand_icon_path+brand.icon"
                    :alt="brand.name"
                    class="c-checkbox__icon"
                >
                {{ brand.name }}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import { BRAND_ICON_PATH, isArrayExists} from "../../util";
import { mapState } from "vuex";

export default {
  data() {
    return {
      fetched_brands: [],
      checked_id:[]
    }
  },
  computed: {
    brand_icon_path() {
      return BRAND_ICON_PATH;
    },
  },
  methods: {
    // 全ての仮想通貨情報を取得する。選択肢に使用される
    async fetch_brand() {
      const response = await axios.get('/api/brand');
      this.fetched_brands = response.data;
    },
    // チェックされた時、チェックボックスの値全てを親コンポネに送る
    isChecked(brand) {
      this.$emit('checked', this.checked_id);
    },
    // チェックを全て外す
    reset_checkbox() {
      this.checked_id = [];
      this.$emit('reset');
    }
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、brandsテーブルから通過情報全てを取得する
        await this.fetch_brand();
      },
      immediate: true
    }
  }
}
</script>

<style scoped>

</style>