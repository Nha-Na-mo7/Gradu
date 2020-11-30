// ====================
// message (フラッシュメッセージ)
// ====================

// ===============
// state
// ===============
const state = () => ({
  content: '',
  type: 0,
})

// ===============
// mutations
// ===============
const mutations = {
  // フラッシュメッセージをセットする。
  // 引数timeoutに指定した時間が経過したら消える。(通常は2秒、エラーは5秒)
  setContent(state, {content, timeout = 2000}) {
    state.content = content;
    state.type = 0;
    
    setTimeout(() => (state.content = ''), timeout);
  },
  setContentSuccess(state, {content, timeout = 2000}) {
    state.content = content;
    state.type = 1;
    
    setTimeout(() => (state.content = ''), timeout);
  },
  setContentError(state, {content, timeout = 5000}) {
    state.content = content;
    state.type = 2;
    
    setTimeout(() => (state.content = ''), timeout);
  },
}

// ================
// export default
// ================
export default {
  namespaced: true,
  state,
  mutations
}
