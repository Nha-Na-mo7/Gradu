<template>
  <div class="p-error__container">
    <div class="p-error">
      <div class="p-error__statusCode">
        <span class="p-error__statusCode--code">500</span>
        <span class="p-error__statusCode--text">Server Error</span>
      </div>
      <div class="p-error__description">
        <span
          >サーバーで問題が発生しました。お手数ですが、しばらく時間を置いてからやり直してください。</span
        >
      </div>

      <a href="/" class="c-btn">トップページへ戻る</a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'System',
};
</script>

<style scoped></style>
