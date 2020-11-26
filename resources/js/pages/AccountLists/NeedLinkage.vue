<!--=================================================================================-->
<!--Twitter連携がされていないアカウントがアカウント一覧ページを見ようとした時に表示させるコンポーネント-->
<!--=================================================================================-->
<template>
  <div class="p-accounts__item c-nothing">
    <h2 class="">{{ title }}</h2>
    <p>{{ description }}</p>

    <div class="renkei">
      <p>今すぐTwitterアカウントと連携</p>
    </div>
  </div>
</template>

<script>
const NOTHING_ACCOUNT_TITLE = '「アカウント一覧」のご利用にはTwitter連携が必要です。';
const NOTHING_ACCOUNT_DESCRIPTION = 'Twitterと連携していただくことによって、仮想通貨アカウントのフォロー・自動フォロー機能が利用可能になります。';

export default {
  data() {
    return {
      title: NOTHING_ACCOUNT_TITLE,
      description: NOTHING_ACCOUNT_DESCRIPTION
    }
  }
}
</script>

<style scoped>
.renkei {
  text-align: center;
}
</style>