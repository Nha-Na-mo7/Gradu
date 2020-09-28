// ====================
// error Store(エラー情報)
// ====================

// ===============
// state
// ===============
const state = {
  errorCode: null
}


// ===============
// getter
// ===============
const getter = {}


// ===============
// mutations
// ===============
const mutations = {
  // errorCodeに、受け取ったエラー時のステータスコード番号を格納する
  setErrorCode(state, code) {
    state.errorCode = code;
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
  getter,
  mutations,
  actions
}