<!--========================================-->
<!--アカウント一覧・見つからなかった時のコンポーネント-->
<!--=========================================-->
<template>
  <div class="p-accounts__item c-nothing">
    <h2 class="c-nothing__title">{{ title }}</h2>
    <p class="c-nothing__text">{{ description }}</p>
  </div>
</template>

<script>
const NOTHING_ACCOUNT_TITLE = 'ユーザーアカウントが見つかりませんでした。';
const NOTHING_ACCOUNT_DESCRIPTION =
  'しばらく経ってからもう一度お試しください。';

export default {
  data() {
    return {
      title: NOTHING_ACCOUNT_TITLE,
      description: NOTHING_ACCOUNT_DESCRIPTION,
    };
  },
};
</script>

<style scoped></style>
