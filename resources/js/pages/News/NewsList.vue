<!--===============-->
<!--ニュースの一覧画面-->
<!--===============-->
<template>
  <div>
    <!-- サイトリンク -->
    <div class="c-site-linknav">
      <RouterLink class="c-totop" to="/">トップ</RouterLink>
      <span>></span>
      <RouterLink class="c-tonews" to="/news">ニュース</RouterLink>
    </div>

    <!-- ページタイトル -->
    <div class="c-topictitle">
      <h2>NEWS</h2>
    </div>

    <!--メインレイアウト-->
    <div class="p-container-news">

      <!-- ヘッドライン -->
      <div class="news-hedline">
        <!-- 検索 -->
        <div class="c-news__search">
          <input type="text" class="kari-input" value="仮想通貨">
          <button type="submit" class="btn">🔎</button>
        </div>

        <!-- 絞り込みモーダルボタン -->
        <div class="p-hedmodal">
          <button class="c-btn c-btn__main c-btn--primary" @click="showModal">条件設定</button>
        </div>
      </div>

      <!-- 絞り込みモーダル -->
      <div class="p-modal__hide" v-if="modal">
        <!-- モーダルカバー -->
        <!-- 画面がクリックでモーダルを閉じる。.selfを付与して子要素にクローズイベントが伝播しないようにする-->
        <div class="p-modal__cover" @click.self="closeModal"></div>
        <!-- モーダルコンテンツ -->
        <div class="p-modal">
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
      <div class="p-newses">
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
      modal: false
    }
  },
  methods: {
    showModal(){
      this.modal = true;
    },
    closeModal(){
      this.modal = false;
    },
  },
  components: {News}

}
</script>

<style scoped>
.p-container-news {
  margin: 20px 15px;
  border: 1px solid #000000;
  height: 800px;
  background: #f7fbff;
}
.c-site-linknav {
  margin-left: 20px;
  margin-bottom: 5px;
  font-weight: bold;
  text-shadow: #dedede 0 2px 2px;
}
.c-totop{
  color: #4FB4D7;
}
.c-totop:hover , .c-tonews:hover{
  color: #ffcd22;
}
.c-tonews {
  color: orange;
}
.c-topictitle{
  font-size: 30px;
  color: #4FB4D7;
  padding: 5px;
  font-weight: bold;
  border-bottom: 3px solid #4FB4D7;
}
.c-modal__head {
  text-align: center;
}
.c-modal__head-title{
  font-size: 30px;
  font-weight: bold;
}
.c-news__search {
  width: 70%;
  background: #fdfdfd;
  border-radius: 10px;
  border: 1px solid #000;
}
.kari-input {
  border-radius: 4px;
  height: 100%;
  padding: 0 10px;
  width: 90%;
  font-size: 20px;
}
.news-hedline {
  margin: 20px 30px;
  display: flex;
}
.p-hedmodal{
  width: 30%;
  border: 1px solid #000;
}

.p-modal {
  z-index: 5;
  box-sizing: border-box;
  position: fixed;
  background: #e9e9e9;
  border-radius: 4px;
  transition: .3s all;
  width: 60%;
  top: 10%;
  left: 20%;
  padding: 20px 25px;
}
.p-modal__hide {
   /*display: none;*/
 }

.p-modal__cover {
  position: absolute;
  /*display: none;*/
  transition: .3s all;
  width: 100%;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 4;
  opacity: 0.5;
  background: #030303;
}
.c-modal__index{
  margin-top: 40px;
}
.c-modal__index-title{
  font-size: 20px;
  padding-bottom: 5px;
  margin-bottom: 15px;
  border-bottom: 1px solid #000;
}

.c-checkbox__space {
  font-size: 20px;
  margin-bottom: 15px;
  display: flex;
  flex-wrap: wrap;
}
.c-checkbox__item {
  width: 25%;
  height: 30px;
  margin-bottom: 10px;
}


</style>