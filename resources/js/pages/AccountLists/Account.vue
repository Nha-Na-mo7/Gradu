<!--=======================================================-->
<!--アカウント一覧画面でいくつも描画される各twitterアカウントの情報-->
<!--=======================================================-->
<template>
  <div class="p-accounts__item">

    <div class="item-1">


      <!-- サムネイル表示エリア、縦に長く、他のコンテンツは設置しない -->
      <div class="item-2 item2-left">
        <div class="item-3 thumnale-area">
          <div class="item-3-image"></div>
          <!--<img src="" alt="サムネホイホイ">-->
        </div>
      </div>


      <div class="item-2 item2-right">

        <!-- プロフィールとフォロー関連のエリア -->
        <div class="item-3 item-3-right-top">

          <!-- プロフィールエリア -->
          <div class="item-4 item-4-left">
            <div class="item-5 item-5-top-prof">
              <div class="item-6 item-6-Account">
                <p>スーパーフェニミストヴィーガン=カルロスゴーン・ネオ麦茶</p>
              </div>
              <div class="item-6 item-6-username">
                <p>@oaijgoaja9494293</p>
              </div>
            </div>

            <div class="item-5 item-5-bottom-profile">
              <p>
                三浦鈴木 / 個人で製造業向け業務改善webアプリを開発中/ 自動車メカエンジニアからITエンジニアに転身 / 転職4回 /群馬から
                都内へ通勤 / 2児の父書類通過率90％以上の職務経歴書を販売してますこちらから↓田所浩二 / 個人で製造業向け業務改善web
                アプリを開発中/自動車メカエンジニアからITエンジニアに転身
              </p>
            </div>
          </div>

          <!-- フォローボタンとFF数のエリア -->
          <div class="item-4 item-4-right">

            <!-- フォローボタン -->
            <div class="item-5 item-5-top-btnarea">
              <button class="c-btn">フォロ</button>
            </div>

            <!-- FF数 -->
            <div class="item-5 item-5-bottom-FFArea">

              <div class="item-6 item-6-ff">
                <div class="item-7 item-7-top-counter"><p>777</p></div>
                <div class="item-7 item-7-bottom-title"><p>フォロー中</p></div>
              </div>
              <div class="item-6 item-6-ff">
                <div class="item-7 item-7-top-counter"><p>9,999</p></div>
                <div class="item-7 item-7-bottom-title"><p>フォロワー</p></div>
              </div>

            </div>

          </div>

        </div>

        <!-- 新着ツイート1件表示エリア -->
        <div class="item-3 item-3-right-bottom">
          <div class="item-4 item-4-top-title">
            <p>新着ツイート</p>
          </div>

          <!-- 取得したツイートと日付 -->
          <div class="item-4 item-4-bottom">
            <p>キリトかなーやっぱりww
              自分は思わないんだけど周りにキリトに似てるってよく言われるwww
              こないだDQNに絡まれた時も気が付いたら意識無くて周りに人が血だらけで倒れてたしなwww
              ちなみに彼女もアスナに似てる(聞いてないw)
              F外から失礼するゾ〜（謝罪）このツイート面白ｽｷﾞｨ！</p>
            <span>20/08/10 11:45:14</span>
          </div>

        </div>

      </div>


    </div>

  </div>
</template>

<script>

export default {
  // watch: {
  //   $route: {
  //     async handler() {
  //       // ページの読み込み直後にもニュース取得を行う
  //       // await this.getTime
  //     },
  //     immediate: true
  //   }
  // },
}
</script>

<style scoped>
.p-accounts__item {
  width: 100%;
  /*background: #f7fff1;*/
  color: #0b2e13;
  font-size: 15px;
  margin-bottom: 5px;
  border: 1px solid #000000;
}

.item-1 {
  height: 100%;
  box-sizing: border-box;
  padding: 10px 15px 25px 15px;
  border-radius: 4px;
  /*background: #deefe9;*/
  display: flex;
}
.item-2 {
  /*background: #c5ccd9;*/
  display: flex;
  flex-direction: column;
}
.item2-left {
  margin-right: 20px;
}
.item2-right {
  width: auto;
}
.item-3 {
  /*background: #bfabcb;*/
}
.item-3-right-top {
  display: flex;
  justify-content: space-between;
  margin-bottom: 30px;

}
.item-3-right-bottom {
  border-top: 1px solid #d8d8d8;
  padding-top: 15px;
}
.item-3-image {
  height: 80px;
  width: 80px;
  border-radius: 50%;
  border: 1px solid #000;
  /*background: #FFF;*/
}
.item-4 {
  display: flex;
  flex-direction: column;
  /*background: #d7d3b5;*/
}
.item-4-left {
  width: 80%;
}
.item-4-right {
  width: 20%;
}
.item-4-top-title {
  font-size: 20px;
  margin-bottom: 10px;
}
.item-4-bottom {
  /*background: #f5fff3;*/
  border: 1px solid #888;
  height: auto;
  width: 100%;
  padding: 10px 15px;
  font-size: 15px;
  line-height: 20px;
}
.item-5 {
  /*background: #d7c7b9;*/
}
.item-5-top-prof {
  margin-bottom: 5px;
}
.item-5-top-btnarea {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 15px;
}
.item-5-bottom-FFArea {
  display: flex;
  justify-content: space-between;
}
.item-5-bottom-profile {
  line-height: 22px;
  font-size: 15px;
}
.item-5-bottom-profile span {
  font-size: 10px;
  font-weight: bold;
  color: #b7b7b7;
}
.item-6 {
  /*background: #d2b0ca;*/
}
.item-6-Account {
  width: 100%;
  font-size: 24px;
  font-weight: bold;

  margin-bottom: 5px;
}
.item-6-username {
  width: 100%;
  font-size: 12px;
  color: #f63030;
}
.item-6-ff {
  width: 50%;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  text-align: center;
}
.item-7 {
  height: 50%;
  width: 100%;
  /*background: #eacaca;*/
}
.item-7-top-counter {
  font-size: 15px;
  font-weight: bold;
  margin-bottom: 10px;
}
.item-7-bottom-title {
  font-size: 5px;
  letter-spacing: .5px;
  color: #5d5d5d;
}
</style>