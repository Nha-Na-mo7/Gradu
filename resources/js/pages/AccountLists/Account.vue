<!--=======================================================-->
<!--アカウント一覧画面でいくつも描画される各twitterアカウントの情報-->
<!--=======================================================-->

<!-- item2~7のクラス、及びscope内のスタイルは、本番に上げる前に外してください！ -->


<template>

  <div class="p-accounts__item">


    <!-- サムネイル表示エリア、縦に長く、他のコンテンツは設置しない -->
    <div class="item-2 item2-left p-accounts__left-side">
      <div class="item-3 p-accounts__icon--area">
        <div class="p-accounts__icon"></div>
        <!--<img src="" alt="サムネホイホイ">-->
      </div>
    </div>


    <div class="item-2 p-accounts__right-side">

      <!-- プロフィールとフォロー関連のエリア -->
      <div class="item-3 p-accounts__data--area">

        <!-- プロフィールエリア -->
        <div class="item-4 p-accounts__profile--area">
          <div class="item-5 p-accounts__profile">
            <div class="item-6 p-accounts__profile--nickname">
<!--              <p>スーパーフェニミストヴィーガン=カルロスゴーン・ネオ麦茶</p>-->
              <p>草</p>
            </div>
            <div class="item-6 p-accounts__profile--username">
              <p>@oaijgoaja9494293</p>
            </div>
          </div>

          <div class="item-5 p-accounts__profile--description">
            <p>
<!--              三浦鈴木 / 個人で製造業向け業務改善webアプリを開発中/ 自動車メカエンジニアからITエンジニアに転身 / 転職4回 /群馬から-->
<!--              都内へ通勤 / 2児の父書類通過率90％以上の職務経歴書を販売してますこちらから↓田所浩二 / 個人で製造業向け業務改善web-->
<!--              アプリを開発中/自動車メカエンジニアからITエンジニアに転身-->
              三浦鈴木
            </p>
          </div>
        </div>

        <!-- フォローボタンとFF数のエリア -->
        <div class="item-4 p-accounts__follow--area">

          <!-- フォローボタン -->
          <div class="item-5 p-accounts__follow-btn--area">
            <button class="c-btn">フォロ</button>
          </div>

          <!-- FF数 -->
          <div class="item-5 p-accounts__ff--area">

            <div class="item-6 p-accounts__ff--item">
              <div class="item-7 p-accounts__ff--count"><p>777</p></div>
              <div class="item-7 p-accounts__ff--title"><p>フォロー中</p></div>
            </div>
            <div class="item-6 item-6-ff">
              <div class="item-7 p-accounts__ff--count"><p>9,999</p></div>
              <div class="item-7 p-accounts__ff--title"><p>フォロワー</p></div>
            </div>

          </div>

        </div>

      </div>

      <!-- 新着ツイート1件表示エリア -->
      <div class="item-3 p-accounts__tweet--area">
        <div class="item-4 p-accounts__tweet">
          <p>新着ツイート</p>
        </div>

        <!-- 取得したツイートと日付 -->
        <div class="item-4 p-accounts__tweet--data">
<!--          <p>キリトかなーやっぱりww-->
<!--自分は思わないんだけど周りにキリトに似てるってよく言われるwww-->
<!--こないだDQNに絡まれた時も気が付いたら意識無くて周りに人が血だらけで倒れてたしなwww-->
<!--ちなみに彼女もアスナに似てる(聞いてないw)-->

<!--F外から失礼するゾ〜（謝罪）このツイート面白ｽｷﾞｨ！-->
<!--          </p>-->
          <p>キリトかなーやっぱりww</p>
          <span>20/08/10 11:45:14</span>
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


.item-2 {
  /*background: #c5ccd9;*/
}
.item-3 {
  /*background: #bfabcb;*/
}
.item-4 {
  /*background: #d7d3b5;*/
}
.item-5 {
  /*background: #d7c7b9;*/
}
.item-6 {
  /*background: #d2b0ca;*/
}
.item-7 {
  /*background: #eacaca;*/
}

</style>