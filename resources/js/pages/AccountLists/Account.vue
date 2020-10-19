<!--=======================================================-->
<!--アカウント一覧画面でいくつも描画される各twitterアカウントの情報-->
<!--=======================================================-->
<template>
  <div class="p-accounts__item">
    <h2>アカウント名</h2>
    <ul>
      <li>@yaju8101919</li>
      <li>フォロー : 114</li>
      <li>フォロワー : 514</li>
    </ul>
    <div class="p-accounts__profile-area">
      <h3>プロフィール</h3>
      <p>F外から失礼するゾ〜（謝罪）このツイート面白ｽｷﾞｨ！自分RTいいっすか？淫夢知ってそうだしリストにぶちこんでやるぜ〜フォローしてください！オナシャス！なんでもしますから！（なんでもするとは言っていない）</p>
    </div>

    <div class="saishin-tweet">
      <h2>1999/08/10 11:45:14</h2>
      <p>キリトかなーやっぱりww
        自分は思わないんだけど周りにキリトに似てるってよく言われるwww
        こないだDQNに絡まれた時も気が付いたら意識無くて周りに人が血だらけで倒れてたしなwww
        ちなみに彼女もアスナに似てる(聞いてないw)</p>
    </div>

    <button class="c-btn">フォローする</button>
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
  height: 250px;
  background: #f7fff1;
  color: #0b2e13;
  font-size: 15px;
  margin-bottom: 5px;
  border: 1px solid #000000;
}
</style>