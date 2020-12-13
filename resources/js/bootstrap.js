import { getCookieValue } from './util';

window._ = require('lodash');
window.$ = window.jQuery = require('jquery');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

// Ajaxリクエストであることを示すためのヘッダーを付与
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// X-XSRF-TOKENヘッダーを参照してCSRFトークンをチェックするための記述
window.axios.interceptors.request.use((config) => {
  // cookieからXSRF-TOKENを取り出し、ヘッダーに添付
  config.headers['X-XSRF-TOKEN'] = getCookieValue('XSRF-TOKEN');

  return config;
});
