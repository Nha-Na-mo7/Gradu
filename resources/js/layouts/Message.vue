<!--===============================-->
<!--フラッシュメッセージ用のコンポーネント-->
<!--===============================-->

<template>
<div class="message" v-show="message">
  {{ message }}
</div>
</template>

<script>
import {mapState} from 'vuex';
export default {
  computed: {
    ...mapState({
      message: state => state.message.content
    })
  }
}
</script>

<style scoped>
.message {
  background: #98c379;
  height: 40px;
  width: 100%;
  opacity: 0.7;
  z-index: 44;
}
</style>