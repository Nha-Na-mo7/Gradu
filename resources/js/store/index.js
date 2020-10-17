// ====================
// ルート用Store
// ====================
import Vue from 'vue';
import Vuex from 'vuex';

import auth from './auth';
import error from './error';
import message from './message';
import news from './news';

Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    auth,
    error,
    message,
    news
  }
});

export default store;
