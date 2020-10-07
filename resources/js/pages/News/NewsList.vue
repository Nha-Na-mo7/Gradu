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
        <!-- 降順・昇順ドロップダウン -->
        <div class="p-selectbox">
          <select name="sort" class="c-selectbox">
            <option value="0">投稿が新しい順</option>
            <option value="1">投稿が古い順</option>
          </select>
        </div>
        <!-- 絞り込みモーダルボタン -->
        <div class="p-hedmodal">
          <button class="c-btn c-btn__main c-btn--primary" @click="showModalToggle">絞り込む</button>
        </div>
      </div>

      <!-- 絞り込みモーダル -->
      <div class="p-modal__hide" v-if="modal">
        <!-- モーダルカバー -->
        <!-- 画面がクリックでモーダルを閉じる。.selfを付与して子要素にクローズイベントが伝播しないようにする-->
        <div class="p-modal__cover" v-if="modal" @click.self="showModalToggle"></div>
        <!-- モーダルコンテンツ -->
        <div class="p-modal js-show-modal-target">
          <div class="c-modal__head"><span class="c-modal__head-title">法定健康診断基本コース</span></div>

          <div class="c-modal__foot">
            <p>およそ2時間の健診で、問診、尿検査、血液検査、心電図、胸部X線検査を行います。<br>
              健康診断書の送付は健診終了後１週間以内の発送となります。
              <br>
              ※健診前日以降、一切の飲食禁止となります。
            </p>
          </div>
          <div class="c-modal__btn-area">
            <button class="c-btn" @click="showModalToggle">閉じる</button>
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
      modal: true
    }
  },
  methods: {
    showModalToggle(){
      this.modal = !this.modal;
    }
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


.c-news__search {
  width: 50%;
  border: 1px dotted #000;
}
.kari-input {
  background: #FFF;
  border: 1px solid #000000;
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
.p-selectbox {
  width: 20%;
  border: 1px dotted #000;
  margin-right: 20px;
}
.c-selectbox {
  border: 1px solid #000;
  width: 90%;
  height: 100%;
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


</style>