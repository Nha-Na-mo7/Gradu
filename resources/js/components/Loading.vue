<!--ローディングはマスクをかけず、円がグルグルするだけに変更するかもしれない-->

<template>
  <div>
    <!-- 四角い表示画面-->
    <div class="c-loading">

      <!-- 円がグルグルしているエリア -->
      <div class="c-loading__circle-area">
        <span class="c-loading__circle">●</span>
      </div>

      <!-- メッセージのタイトル(例「検索中」) -->
      <div class="c-loading__message-area">
        <p class="c-loading__title">{{ this.title }}</p>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    }
  },
}
</script>

<style scoped>

</style>