<template>
  <div>
    <!-- モーダルカバー -->
    <!-- 画面がクリックでモーダルを閉じる。.selfを付与して子要素にクローズイベントが伝播しないようにする-->
    <div class="c-modal__cover" @click.self="closeModal"></div>


    <!-- モーダルコンテンツ -->
    <div class="c-modal">
      <div class="c-modal__head"><span class="c-modal__head-title">絞り込み設定</span></div>

      <div class="c-modal__foot">

        <!--  通貨で絞り込み(最初から全ての通貨が選択されている状態で開始する) -->
        <div class="c-modal__index">
          <label>
            <input
                type="button"
                name="currency_all"
                class="c-btn"
                value="リセット"
                @click="resetSearchWord"
            >
          </label>
          <p class="c-modal__index-title">通貨名<span class="c-modal__index-description">ランキングに表示させる通貨をチェックした銘柄に絞り込むことができます。</span></p>

          <div class="c-checkbox__space">

            <!-- 通貨ボックス、brandsテーブルを参照している -->
            <div
                class="c-checkbox__item"
                v-for="currency in fetchedBrands"
                :key="currency.id"
                @change="setCheckedCurrency(currency.name)"
            >
              <label :for="currency.id - 1">
                <input type="checkbox"
                       name="currency"
                       :value="currency.name"
                       :id="currency.id - 1"
                       :checked="isChecked(currency.name)"
                >
                <img
                    v-if="currency.icon"
                    :src="currencyIconPath+currency.icon"
                    :alt="currency.name"
                    class="c-checkbox__icon"
                >
                {{ currency.name }}
              </label>
            </div>
          </div>
        </div>

      </div>

      <!-- 選択肢 -->
      <div class="c-modal__btn-area">
        <button class="c-btn" @click="fetch_googleNews">反映して絞り込む</button>
        <button class="c-btn" @click="closeModal">絞り込まずに閉じる</button>
      </div>

    </div>

  </div>
</template>

<script>
import {CURRENCY_ICON_PATH, isArrayExists} from "../../util";
import { mapState } from "vuex";

export default {
  data() {
    return {
      fetchedBrands: [],
      // オープン時にチェックされていた選択肢を格納、絞り込みせずに閉じる場合にここを参照する。
      checkedBoxWhenOpened: []
    }
  },
  computed: {
    currencyIconPath() {
      return CURRENCY_ICON_PATH;
    },
    ...mapState({
      checkedCurrencies: state => state.news.checkedCurrencies,
    })
  },
  methods: {
    // モーダルのオープン時に、チェック済みの選択肢を保存する
    keepCheckedBoxWhenOpened() {
      // checkedCurrenciesのストアをそのまま参照すると値が変わってしまうので、1つ1つ新しい配列として入れ直す
      for (let i = 0; i < this.checkedCurrencies.length; i++) {
        this.checkedBoxWhenOpened.push(this.checkedCurrencies[i]);
      }
    },

    // 親コンポーネント側でモーダルを閉じる
    closeModal() {
      // 検索せずにモーダルを閉じるだけなので、checkedBoxWhenOpenedの値を使って選択を元に戻す
      this.$store.commit('news/resetCheckedCurrencies');
      for (let i = 0; i < this.checkedBoxWhenOpened.length; i++) {
        this.$store.commit('news/setCheckedCurrencies', this.checkedBoxWhenOpened[i]);
      }
      this.$emit('closeModal');
    },

    // 親コンポーネントのGoogleニュース取得メソッドを発火させる
    fetch_googleNews() {
      this.$emit('fetch_googleNews');
    },

    // 全ての仮想通貨情報を取得する。モーダルの選択肢を追加するときに使用される
    async fetch_brand() {
      const response = await axios.get('/api/brand');
      this.fetchedBrands = response.data;
    },

    // チェックボックスをチェックした時、チェックされた通貨を保存するストアに値をセットする
    setCheckedCurrency(currency_name) {
      this.$store.commit('news/setCheckedCurrencies', currency_name)
    },

    // 既にチェックされているボックスかを判定する。モーダルのオープン時に使われる
    isChecked(currency_name) {
      return isArrayExists(this.checkedCurrencies, currency_name);
    },

    // リセットをクリックした時、チェック済みの値を管理する配列を空にする
    // TODO 全選択を付けるかは要検討
    resetSearchWord() {
      this.$store.commit('news/resetCheckedCurrencies');
    },

  },
  watch: {
    $route: {
      async handler() {
        // ページの読み込み直後、brandsテーブルから通過情報全てを取得する
        await this.fetch_brand();
        await this.keepCheckedBoxWhenOpened();
      },
      immediate: true
    }
  }

}
</script>

<style scoped>

</style>