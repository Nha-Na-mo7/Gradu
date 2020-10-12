<!--===============-->
<!--ニュースの一覧画面-->
<!--===============-->
<template>
  <div class="l-container__content">
    <!-- サイトリンク -->
    <div class="c-site-linknav">
      <RouterLink class="c-site__link-nav__to-top" to="/">トップ</RouterLink>
      <span>></span>
      <RouterLink class="c-site__link-nav__to-content" to="/news">ニュース</RouterLink>
    </div>

    <!-- ページタイトル -->
    <div class="c-site__title">
      <span>NEWS</span>
    </div>

    <div>
      <button class="c-btn" @click="getGoogleNews">取得</button>
    </div>

    <!--メインレイアウト-->
    <div class="p-news__container">

      <!-- ヘッドライン -->
      <div class="p-news__headline">
        <!-- 検索 -->
        <div class="p-news__search">
          <input type="text" class="c-input" value="仮想通貨">
          <button type="submit" class="">🔎</button>
        </div>

        <!-- 絞り込みモーダルボタン -->
        <div class="c-modal__title">
          <button class="c-btn c-btn__main c-btn--primary" @click="showModal">条件設定</button>
        </div>
      </div>

      <!-- 絞り込みモーダル -->
      <div class="c-modal__hide" v-if="modal">
        <!-- モーダルカバー -->
        <!-- 画面がクリックでモーダルを閉じる。.selfを付与して子要素にクローズイベントが伝播しないようにする-->
        <div class="c-modal__cover" @click.self="closeModal"></div>
        <!-- モーダルコンテンツ -->
        <div class="c-modal">
          <div class="c-modal__head"><span class="c-modal__head-title">検索条件設定</span></div>

          <div class="c-modal__foot">
            <div class="c-modal__index">
              <p class="c-modal__index-title">記事の表示順</p>
              <!-- 降順・昇順ドロップダウン -->
              <div class="c-checkbox__space">
                <div class="c-checkbox__item"><input type="radio" name="CryptoSubject" value="kaso" checked>新着順</div>
                <div class="c-checkbox__item"><input type="radio" name="CryptoSubject" value="alto">古い順</div>
              </div>
            </div>
            <div class="c-modal__index">
              <p class="c-modal__index-title">通貨で絞り込む</p>
              <div class="c-checkbox__space">
                <div class="c-checkbox__item"><input type="checkbox" name="Crypto" value="kaso" checked>仮想通貨</div>
                <div class="c-checkbox__item"><input type="checkbox" name="Crypto" value="alto">アルトコイン</div>
              </div>
              <div class="c-checkbox__space">
                <!-- 通貨アイテムボックス、v-forで通貨テーブルからループさせて描画する -->
                <div class="c-checkbox__item">
                  <label for="0"><input type="checkbox" name="Crypto" value="0" id="0">BTC</label>
                </div>
                <div class="c-checkbox__item">
                  <label for="1"><input type="checkbox" name="Crypto" value="1" id="1">BTC</label>
                </div>
                <div class="c-checkbox__item">
                  <label for="2"><input type="checkbox" name="Crypto" value="2" id="2">BTC</label>
                </div>
              </div>
            </div>


          </div>
          <div class="c-modal__btn-area">
            <button class="c-btn" @click="closeModal">絞り込む</button>
            <button class="c-btn" @click="closeModal">リセット</button>
            <button class="c-btn" @click="closeModal">絞り込まずに閉じる</button>
            <button class="c-btn" @click="closeModal">設定を保存</button>
          </div>
        </div>
      </div>

      <!-- ニュース一覧 -->
      <div class="p-news__list">
        <News/>
      </div>


    </div>


  </div>

</template>

<script>
import News from './News.vue';
export default {
  data() {
    return {
      modal: false,
      searchData: {
        keywords: ''
      }

    }
  },
  methods: {
    // モーダルを開く
    showModal(){
      this.modal = true;
    },
    // モーダルを閉じる
    closeModal(){
      this.modal = false;
    },

    async getGoogleNews() {
      const params = this.searchData
      console.log(params)
      const response = await axios.get(`/api/news/get`, { params });

      console.log(response.data);

      return response.status;
    }
  },
  components: {News}

}
</script>

<style scoped>

</style>