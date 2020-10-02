// ====================
// message Store
// ====================
// フラッシュメッセージ用のStoreです。

// ===============
// state
// ===============
const state = {
  content: ''
}

// ===============
// getter
// ===============
const getter = {}

// ===============
// mutations
// ===============
const mutations = {
  // メッセージをセットする。指定がなければ3秒で消える。
  setContent(state, {content, timeout = 3000}) {
    state.content = content;
    
    setTimeout(() => (state.content = ''), timeout);
  }
  
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
  // getter,
  mutations,
  // actions
}