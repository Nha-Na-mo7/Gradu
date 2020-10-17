// ==============
// utility
// ==============

/**
 * ./bootstrap.js内で、クッキーからトークンを取り出して、ヘッダーに添付するために使用。
 * (bootstrap.js内で、Axiosライブラリの設定が記述されている)
 * クッキーの値を取得する。主にXSRF-TOKENの値を返すために使用。
 * @param {String} searchKey 検索するキー
 * @returns {String} キーに対応する値
 */
export function getCookieValue(searchKey) {
  if (typeof searchKey === 'undefined') {
    return '';
  }
  
  let val = '';
  
  // name=12345;token=67890;key=abcde;XSRF-TOKEN=hogehoge
  // cookieを";"でsplitし、それをさらに"="でsplit。
  document.cookie.split(';').forEach(cookie => {
    const [key, value] = cookie.split('=');
    if (key === searchKey) {
      return val = value;
    }
  })
  
  return val;
}

// 配列内に同じ値が存在するかをチェックする
export function isArrayExists(array, value) {
  // 配列の最後までループ、値があればtrueを、なければfalseを返す
  for (var i = 0, len = array.length; i < len; i++) {
    if (value === array[i]) {
      return true;
    }
  }
  return false;
}


export const OK = 200
export const CREATED = 201
export const NOT_FOUND = 404
export const UNAUTHORIZED = 419 //認証切れ
export const UNPROCESSABLE_ENTITY = 422 //バリデーションエラー
export const INTERNAL_SERVER_ERROR = 500

export const LOADING = '読み込み中'
export const SEARCHING = '検索中'

export const DEFAULT_SEARCHWORD = '仮想通貨';

// 通貨アイコンのパス。storage/images/currency_svg/xxxxxx.svg
export const CURRENCY_ICON_PATH = 'storage/images/currency_svg/';

