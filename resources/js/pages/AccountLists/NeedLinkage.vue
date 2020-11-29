<!--=================================================================================-->
<!--Twitter連携がされていないアカウントがアカウント一覧ページを見ようとした時に表示させるコンポーネント-->
<!--=================================================================================-->
<template>
  <div class="p-accounts__item c-nothing">
    <h2 class="">{{ title }}</h2>
    <p>{{ description }}</p>

    <div class="u-mt-xl">
      <button class="c-btn c-btn__twitter">
        <a
            title="Start for Twitter!"
            @click.stop
            :href="`/twitter/auth/begin`"
        >
          今すぐTwitterと連携する
        </a>
      </button>
    </div>
  </div>
</template>

<script>
const NOTHING_ACCOUNT_TITLE = '「アカウント一覧」のご利用にはTwitter連携が必要です。';
const NOTHING_ACCOUNT_DESCRIPTION = 'Twitterと連携することで、仮想通貨アカウントのフォロー・自動フォロー機能が利用可能になります。';

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