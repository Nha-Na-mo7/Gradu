<!--===============================-->
<!--通貨絞り込み用のチェックボックスエリアー-->
<!--===============================-->
<template>
  <!--  通貨での絞り込み -->
  <div class="p-trends__checkbox c-checkbox">
    <div class="p-trends__checkbox--description">
      <p>チェックした通貨に絞ってランキングを表示します。</p>
    </div>

    <div class="c-checkbox__space">
      <!-- 通貨一覧 -->
      <div
        class="c-checkbox__item"
        v-for="brand in fetchedBrandsData"
        :key="brand.id"
      >
        <label class="" :for="brand.id - 1">
          <input
            type="checkbox"
            name="brand"
            class="c-checkbox__item--check"
            :value="brand.name"
            :id="brand.id - 1"
            v-model="checkedId"
            @change="isChecked"
          />
          <img
            v-if="brand.icon"
            :src="brand.icon"
            :alt="brand.name"
            class="c-checkbox__icon"
          />
          {{ brand.name }}
        </label>
      </div>
    </div>

    <!-- ボタン -->
    <div class="p-trends__table--btn--inner">
      <button class="c-btn c-btn__trends" @click="resetCheckbox">
        絞り込みをリセット
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      fetchedBrandsData: [],
      checkedId: [],
    };
  },
  computed: {},
  methods: {
    // 全ての仮想通貨情報を取得する。選択肢に使用される
    async fetchBrand() {
      const response = await axios.get('/brand');
      this.fetchedBrandsData = response.data;
    },
    // チェックされた時、チェックボックスの値全てを親コンポネに送る
    isChecked(brand) {
      this.$emit('checked', this.checkedId);
    },
    // チェックを全て外す
    resetCheckbox() {
      this.checkedId = [];
      this.$emit('reset');
    },
  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、brandsテーブルから通過情報全てを取得する
        await this.fetchBrand();
      },
      immediate: true,
    },
  },
};
</script>

<style scoped></style>
