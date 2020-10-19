<template>
  <div>
    <!-- モーダルカバー -->
    <!-- 画面がクリックでモーダルを閉じる。.selfを付与して子要素にクローズイベントが伝播しないようにする-->
    <div class="c-modal__cover" @click.self="closeModal"></div>


    <!-- モーダルコンテンツ -->
    <div class="c-modal">
      <div class="c-modal__head"><span class="c-modal__head-title">検索条件設定</span></div>

      <div class="c-modal__foot">

        <!--  通過での絞り込み -->
        <div class="c-modal__index">
          <label>
            <input
                type="checkbox"
                name="currency_all"
                @change="allCheckedSearchWord"
                :checked="allchecked"
            >
          </label>
          <p class="c-modal__index-title">通貨名<span class="c-modal__index-description">(仮想通貨と関係ないニュースを除外するため、検索ワードの前に「仮想通貨」が付与された状態で検索されます。)</span></p>

          <div class="c-checkbox__space">

            <!-- 通貨ボックス、brandsテーブルを参照している -->
            <div
                class="c-checkbox__item"
                v-for="currency in fetchedBrands"
                :key="currency.id"
                @change="checkedWord(currency.name)"
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
      isAllChecked: false,
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
    fetch_googleNews() {
      this.$emit('fetch_googleNews');
    },
    // 全ての仮想通貨情報を取得する。モーダルの選択肢を追加するときに使用される
    async fetch_brand() {
      const response = await axios.get('/api/brand');
      this.fetchedBrands = response.data;
    },

    // チェックボックスをクリックした時の操作
    checkedWord(currency_name) {
      this.$store.commit('news/setCheckedCurrencies', currency_name)
    },

    /* チェックボックスにチェックを入れるべきかを判定する。
     * dataのisAllCheckedが付いていればtrue。
     * そうでなけでばstoreのcheckedCurrenciesを確認して、値が存在すればtrueとする。
     */
    isChecked(currency_name) {
      if (this.isAllChecked) {
        return true
      } else {
        return isArrayExists(this.checkedCurrencies, currency_name)
      }
    },

    // 全選択をクリックした時の操作
    allCheckedSearchWord() {
      this.isAllChecked = !this.isAllChecked
      this.$store.commit('news/resetCheckedCurrencies');

      if (this.isAllChecked) {
        for (let i = 0; i < this.fetchedBrands.length; i++) {
          // TODO ベタがきはしないべき？
          this.$store.commit('news/setCheckedCurrencies', this.fetchedBrands[i].name);
        }
      }
    },

    // チェックボックスが全て埋まっている場合、全選択にもチェックがつく
    allchecked(){
      return this.checkedCurrencies.length !== this.fetchedBrands.length
    },

    // 検索設定をDBに保存
    // TODO この処理はPHP側でやるのかJS側でやるのか検討、おそらくはModelを作成してPHP側で処理させる
    save_setting_search() {
      // const response = await axios.post(`/api/news/setting/get`, { params });
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