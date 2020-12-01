<template>
  <div class="p-error__container">
    <div class="p-error">
      <div class="p-error__statusCode">
        <span class="p-error__statusCode--code">404</span>
        <span class="p-error__statusCode--text">Not Found</span>
      </div>
      <div class="p-error__description">
        <span>お探しのページは一時的にアクセスができない状況か、移動もしくは削除された可能性があります。 また、URL、ファイル名にタイプミスがないか再度ご確認ください。</span>
      </div>

      <a href="/" class="c-btn">トップページへ戻る</a>
    </div>
  </div>
</template>

<script>
export default {
name: "NotFound.vue"
}
</script>

<style scoped>

</style>