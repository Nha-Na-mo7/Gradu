// ====================
// Store Auth(認証用)
// ====================
import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util';




// ===============
// state
// ===============
const state = {
  //ログイン済みユーザーの保持
  user: null,
  //APIの呼び出しにエラーが起きていないか
  apiStatus: null,
  //エラーメッセージ
  loginErrorMessages: null,
  registerErrorMessages: null,
  resetMailErrorMessage: null,
  resetPasswordErrorMessages: null,
  resetToken: null
}




// ===============
// getter
// ===============
const getters = {
  // ユーザーがログイン済みか否か
  loginCheck: state => !! state.user,
  // ログインしているユーザーのID
  user_id: state => state.user ? state.user.id : '',
  // パスワードリセットトークン
  resetToken: state => state.resetToken ? state.resetToken : ''
}




// ===============
// mutations
// ===============
const mutations = {
  // userステートの値をセット
  setUser(state, userdata) {
    state.user = userdata
  },
  // 通信ステータス番号をセットする
  setApiStatus(state, status) {
    state.apiStatus = status
  },
  // ログイン時のエラーメッセージを格納する
  setLoginErrorMessages(state, messages) {
    state.loginErrorMessages = messages
  },
  // 新規登録時のエラーメッセージを格納する
  setRegisterErrorMessages(state, messages) {
    state.registerErrorMessages = messages
  },
  // リマインドメール送信先入力時のエラーメッセージを格納する(フォームが1つなので単数系)
  setResetMailErrorMessage(state, message) {
    state.resetMailErrorMessage = message
  },
  // パスワードの再設定時のエラーメッセージを格納する
  setResetPasswordErrorMessages(state, messages) {
    state.resetPasswordErrorMessages = messages
  },
  // パスワード再設定時のリセットトークンを格納する
  setResetToken(state, token){
    state.resetToken = token
  }
}



// ===============
// actions
// ===============
const actions = {
  
  // -------------
  // 会員登録
  // -------------
  async register (context, data) {
    
    // 始めにエラーステート欄を空にする
    
    context.commit('setApiStatus', null);
    // 会員登録APIに入力フォームのデータを送り、レスポンスを受け取る
    const response = await axios.post('/api/register', data)
        // 通信失敗時にerror.responseが、成功時はレスポンスオブジェクトがそのまま入る
        .catch(error => error.response || error);
    
    // 通信成功時
    if(response.status === CREATED) {
      // 受け取ったレスポンスを元に、apiStatus,userステートを更新
      context.commit('setApiStatus', true);
      context.commit('setUser', response.data);
      return false;
    }
    
    // 通信失敗時、errorストアを更新
    context.commit('setApiStatus', false);
    // バリデーションエラーの時
    if(response.status === UNPROCESSABLE_ENTITY) {
      // エラーメッセージをセット
      context.commit('setRegisterErrorMessages', response.data.errors);
    } else {
      // 別のストア(ここではerror.js)のmutationをcommitしたいので、第三引数に{root:true}を追記
      context.commit('error/setErrorCode', response.status, {root: true})
    }
  },
  
  // -------------
  // ログイン
  // -------------
  async login (context, data) {
    
    // 始めにエラーコード欄を空にする
    context.commit('setApiStatus', null);
    
    // ログインAPIに入力フォームのデータを送り、レスポンスを受け取る
    const response = await axios.post('/api/login', data)
        // 通信失敗時にerror.responseが、成功時はレスポンスオブジェクトがそのまま入る
        .catch(error => error.response || error);
    
    // 通信成功時
    if(response.status === OK) {
      // 受け取ったレスポンスを元に、apiStatus,userステートを更新
      context.commit('setApiStatus', true);
      context.commit('setUser', response.data);
      return false;
    }
    
    // 通信失敗時、errorストアを更新
    context.commit('setApiStatus', false);
    // バリデーションエラーの時
    if(response.status === UNPROCESSABLE_ENTITY) {
      // エラーメッセージをセット
      context.commit('setLoginErrorMessages', response.data.errors);
    } else {
      context.commit('error/setErrorCode', response.status, {root: true});
    }
  },
  
  // -------------
  // ログアウト
  // -------------
  async logout (context) {
    context.commit('setApiStatus', null);
    const response = await axios.post('/api/logout')
        // 通信失敗時にerror.responseが、成功時はレスポンスオブジェクトがそのまま入る
        .catch(error => error.response || error);
    
    if(response.status === OK) {
      context.commit('setApiStatus', true);
      context.commit('setUser', null);
      return false;
    }
    
    // 通信失敗時、errorストアを更新
    context.commit('setApiStatus', false);
    context.commit('error/setErrorCode', response.status, {root: true});
    
  },
  
  // --------------------------
  // パスワードリマインドメール送信
  // --------------------------
  async resetMail (context, data) {
    // 始めにエラーコード欄を空にする
    context.commit('setApiStatus', null);
    // リマインドAPIに入力フォームのデータを送り、レスポンスを受け取る
    const response = await axios.post('/api/password/email', data)
        // 通信失敗時にerror.responseが、成功時はレスポンスオブジェクトがそのまま入る
        .catch(error => error.response || error);
    
    // 通信成功時
    if(response.status === CREATED) {
      // 受け取ったレスポンスを元に、apiStatus,userステートを更新
      context.commit('setApiStatus', true);
      context.commit('setUser', response.data);
      return false;
    }
    
    // 通信失敗時、errorストアを更新
    context.commit('setApiStatus', false);
    // バリデーションエラーの時
    if(response.status === UNPROCESSABLE_ENTITY) {
      // エラーメッセージをセット
      context.commit('setResetMailErrorMessages', response.data.errors);
    } else {
      context.commit('error/setErrorCode', response.status, {root: true});
    }
  },
  // --------------------------
  // パスワード再設定フォーム
  // --------------------------
  async resetPassword (context, data) {
    // 始めにエラーコード欄を空にする
    context.commit('setApiStatus', null);
    // APIに入力フォームのデータを送り、レスポンスを受け取る。トークンの値もいれる。
    const response = await axios.post(`/api/password/reset/${data.token}`, data)
        // 通信失敗時にerror.responseが、成功時はレスポンスオブジェクトがそのまま入る
        .catch(error => error.response || error);
    
    // 通信成功時
    if(response.status === OK) {
      console.log(200)
      // 受け取ったレスポンスを元に、apiStatus,userステートを更新
      context.commit('setApiStatus', true);
      context.commit('setUser', response.data);
      return false;
    }
    
    // 通信失敗時、errorストアを更新
    context.commit('setApiStatus', false);
    // バリデーションエラーの時
    if(response.status === UNPROCESSABLE_ENTITY) {
      // エラーメッセージをセット
      context.commit('setResetPasswordErrorMessages', response.data.errors);
    } else {
      console.log(response.status)
      context.commit('error/setErrorCode', response.status, {root: true});
    }
  },
  
  // --------------------
  // 現在のユーザー情報を返却
  // --------------------
  async currentUser (context) {
    context.commit('setApiStatus', null);
    const response = await axios.get('/api/user');
    const currentUser = response.data || null;
    
    if(response.status === OK) {
      context.commit('setApiStatus', true);
      context.commit('setUser', currentUser);
      return false;
    }
    
    // 通信失敗時、errorストアを更新
    context.commit('setApiStatus', false);
    context.commit('error/setErrorCode', response.status, {root: true});
  }
}



// ================
// export default
// ================
export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
