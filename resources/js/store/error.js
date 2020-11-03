// ====================
// error Store(エラー情報)
// ====================

// ===============
// state
// ===============
const state = () => ({
  errorCode: null
})

// ===============
// mutations
// ===============
const mutations = {
  // errorCodeに、受け取ったエラー時のステータスコード番号を格納する
  setErrorCode(state, code) {
    state.errorCode = code;
  }
}


// ================
// export default
// ================
export default {
  namespaced: true,
  state,
  mutations
}