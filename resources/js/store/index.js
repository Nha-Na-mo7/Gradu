// ====================
// ルート用Store
// ====================
import Vue from 'vue';
import vuex from 'vuex';

import auth from './auth';
import error from './error';
import message from './message';
import news from './news';

Vue.use(vuex);

const store = new vuex.Store({
  modules: {
    auth,
    error,
    message,
    news
  }
});

export default store;
