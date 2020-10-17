// ====================
// Store News
// ====================
// Googleニュース取得ページで使用するストア

// ===============
// state
// ===============
const state = {
  // 実際にAPIを使ってのニュース検索ワード
  
  // デフォルトの検索ワード(仮想通貨とかアルトコインとか)
  searchBoxWords: '',
  // チェックボックスでチェックされたワードの配列
  checkedCurrencies: [],
  // DBから取得した通貨データを格納した配列
  fetchedBrands: []
  
}

// ===============
// getter
// ===============
const getter = {}

// ===============
// mutations
// ===============
const mutations = {}

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