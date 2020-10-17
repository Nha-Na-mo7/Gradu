// ====================
// Store News
// ====================
// Googleニュース取得ページで使用するストア
import { OK, CREATED, UNPROCESSABLE_ENTITY, isArrayExists } from '../util';

// ===============
// state
// ===============
const state = {
  // デフォルトの検索ワード(仮想通貨とかアルトコインとか)
  requireSearchWords: '',
  // チェックボックスでチェックされたワードの配列
  checkedCurrencies: [],
}

// ===============
// getter
// ===============
const getter = {
  checkedCurrencies: state => state.checkedCurrencies
}

// ===============
// mutations
// ===============
const mutations = {
  setCheckedCurrencies(state, currency_name) {
    // 既に配列内に同じ値があれば外す
    if(isArrayExists(state.checkedCurrencies, currency_name)) {
      state.checkedCurrencies = state.checkedCurrencies.filter(val => val !== currency_name);
    }else {
      // なければいれる
      state.checkedCurrencies.push(currency_name);
    }
  },
  // 配列をリセットする
  resetCheckedCurrencies(state) {
    state.checkedCurrencies = [];
  },
}

// ===============
// actions
// ===============
const actions = {}

// ================
// export default
// ================
export default {
  namespaced: true,
  state,
  getter,
  mutations,
  actions
}