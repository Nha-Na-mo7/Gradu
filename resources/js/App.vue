<!-- RouterView部分において各コンポーネントが切り替わります -->
<template>
  <div>
    <!-- フラッシュメッセージ -->
    <FlashMessage />
    <!-- 実際にコンポーネントが切り替わるエリア -->
    <RouterView />
  </div>
</template>

<script>
import FlashMessage from './layouts/FlashMessage.vue';

export default {
  components: {
    FlashMessage,
  },
};
</script>
