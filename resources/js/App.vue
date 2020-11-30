<!-- RouterView部分において各コンポーネントが切り替わります -->
<template>
  <div>
    <!-- フラッシュメッセージ -->
    <Message />
    <!-- 実際にコンポーネントが切り替わるエリア -->
    <RouterView />

  </div>
</template>

<script>
import Message from './layouts/Message.vue';

export default {
  components: {
    Message,
  },
}
</script>