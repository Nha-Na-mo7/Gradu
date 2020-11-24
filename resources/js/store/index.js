// ====================
// ルート用Store
// ====================
import Vue from 'vue';
import vuex from 'vuex';

import authenticate from './authenticate_store';

Vue.use(vuex);

const store = new vuex.Store({
  modules: {
    authenticate,
  }
});

export default store;
