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
        <!-- 絞り込みモーダル -->
        <div class="p-hedmodal">
          <button class="c-btn c-btn__main c-btn--primary">絞り込む</button>
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
</style>