// ====================
// authenticate Store
// ====================
// vue-routerで認証切れをチェックするためのストア

// ===============
// state
// ===============
const state = () => ({
  authenticated: false,
});

// ===============
// getter
// ===============

// ===============
// mutations
// ===============
const mutations = {
  set_authenticate(state) {
    state.authenticate = true;
  },
};

// ===============
// actions
// ===============
const actions = {
  // 認証を確認する
  async check_authenticate(context) {
    // 認証状態チェックAPIにリクエストする
    const response = await axios
      .get("/user/auth/check")
      // 通信失敗時にerror.responseが、成功時はレスポンスオブジェクトがそのまま入る
      .catch((error) => error.response || error);

    // 認証切れの時
    if (response.status === 401) {
      mutations.set_authenticate(state);
    }
  },
};

// ================
// export default
// ================
export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
