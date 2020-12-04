<!--=============================================================-->
<!--何らかの理由でトレンドのデータ取得に失敗した場合に描画されるコンポーネント-->
<!--============================================================-->
<template>
  <div>
    <div class="p-news__item c-nothing">
      <!-- タイトル -->
      <h2 class="">{{ title }}</h2>
      <p>{{ description }}</p>
    </div>
  </div>
</template>

<script>
const NOTHING_TRENDS_TITLE = 'トレンド情報が取得できませんでした。';
const NOTHING_TRENDS_DESCRIPTION =
  'お手数ですが、しばらく経ってからもう一度お試しください。';

export default {
  data() {
    return {
      title: NOTHING_TRENDS_TITLE,
      description: NOTHING_TRENDS_DESCRIPTION,
    };
  },
};
</script>

<style scoped></style>
