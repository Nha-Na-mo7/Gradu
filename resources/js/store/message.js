// ====================
// message (フラッシュメッセージ)
// ====================

// ===============
// state
// ===============
const state = {
  content: ''
}


// ===============
// mutations
// ===============
const mutations = {
  // フラッシュメッセージをセットする。
  // 引数timeoutに指定した時間が経過したら消える。(指定がない場合5秒で消えることにする。)
  setContent(state, {content, timeout = 5000}) {
    state.content = content;
    
    setTimeout(() => (state.content = ''), timeout);
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