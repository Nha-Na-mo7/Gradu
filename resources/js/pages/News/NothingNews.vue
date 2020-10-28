<!--============================================-->
<!--ニュースが存在しなかった場合に描画されるコンポーネント-->
<!--============================================-->
<template>
  <div>
    <div class="p-news__item p-news__item--nothing">
      <!-- 記事のタイトル -->
      <h2 class="">{{ title }}</h2>
      <p>{{ description }}</p>
    </div>
  </div>
</template>

<script>

const NOTHING_NEWS_TITLE = '記事が見つかりませんでした。';
const NOTHING_NEWS_DESCRIPTION = '検索ワードを変えるか、しばらく経ってからもう一度お試しください。';

export default {
  data() {
    return {
      title: NOTHING_NEWS_TITLE,
      description: NOTHING_NEWS_DESCRIPTION
    }
  }
}
</script>

<style scoped>

</style>