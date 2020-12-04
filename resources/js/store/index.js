// ====================
// ルート用Store
// ====================
import Vue from 'vue';
import Vuex from 'vuex';

import authenticate from './authenticate';
import message from './message';

Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    authenticate,
    message,
  },
});

export default store;
