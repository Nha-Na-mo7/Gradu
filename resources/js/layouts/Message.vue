<!--===============================-->
<!--フラッシュメッセージ用のコンポーネント-->
<!--===============================-->

<template>
<div class="c-flash" v-show="message">
  <p>✔︎</p>
  <span>
    {{ message }}
  </span>
</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  computed: {
    ...mapState({
      message: state => state.message.content
    })
  }
}
</script>

<style scoped>
.c-flash {
  position: absolute;
  transition: all 3s;
  top: 15px;
  right: 10px;
  background: #b6e395;
  height: 60px;
  width: 480px;
  opacity: 0.7;

  border-radius: 4px;
  z-index: 5;

  font-size: 20px;
}
</style>