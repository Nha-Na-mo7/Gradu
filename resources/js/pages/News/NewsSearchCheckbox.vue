<template>
  <div>
    <!-- コンテンツ(SP用に開閉できるようにする) -->
    <div class="c-checkbox">
      <div class="">
        <!--  通貨での絞り込み -->
        <div class="">
          <div class="p-news__search--description">
            <p class="">チェックした通貨を検索条件に指定できます。</p>
            <p class="">
              ※
              仮想通貨と関係のないニュースが表示される可能性を減らすため、「仮想通貨」は必ず検索ワードに入ります。
            </p>
          </div>

          <div class="c-checkbox__space">
            <!-- 通貨一覧 -->
            <div
              class="c-checkbox__item"
              v-for="brand in fetchedBrandsData"
              :key="brand.id"
            >
              <label :for="brand.id - 1">
                <input
                  type="checkbox"
                  name="brand"
                  :value="brand.name"
                  :id="brand.id - 1"
                  v-model="checkedBrandId"
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
        </div>

        <!-- 検索ボタン -->
        <div class="p-news__search--btn--inner">
          <button class="c-btn c-btn__news" @click="resetCheckbox">
            絞り込みをリセット
          </button>
          <button class="c-btn c-btn__news" @click="searchGoogleNews">
            検索する
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { OK } from '../../util';

export default {
  data() {
    return {
      fetchedBrandsData: [],
      checkedBrandId: [],
    };
  },
  methods: {
    // 全ての仮想通貨情報を取得する。選択肢に使用される
    async fetchBrandsTable() {
      const response = await axios
        .get('/brand')
        .catch((error) => error.response || error);

      // 検索を絞るためのチェックボックスであり、最悪通信失敗しても大きく影響しないので通信失敗時はdata更新しないだけに止める。
      if (response.status === OK) {
        this.fetchedBrandsData = response.data;
      }
    },
    // チェックされた時、チェックボックスの値全てを親コンポネに送る
    isChecked(brand) {
      this.$emit('checked', this.checkedBrandId);
    },
    // チェックを全て外す
    resetCheckbox() {
      this.checkedBrandId = [];
      this.$emit('reset');
    },
    // 検索を開始する
    searchGoogleNews() {
      this.$emit('search');
    },
  },
  watch: {
    $route: {
      async handler() {
        // 読み込み直後、brandsテーブルから通過情報全てを取得する
        await this.fetchBrandsTable();
      },
      immediate: true,
    },
  },
};
</script>

<style scoped></style>
