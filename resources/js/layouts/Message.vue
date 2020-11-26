<!--===============================-->
<!--フラッシュメッセージ用のコンポーネント-->
<!--===============================-->

<template>
<div class="c-flash" :class="bgColor" v-show="message">
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
      message: state => state.message.content,
      type: state => state.message.type
    }),
    bgColor() {
      var type = '';
      switch(this.type) {
        case 0:
          type = '';
          break;
        case 1:
          type = 'c-flash__success';
          break;
        case 2:
          type = 'c-flash__error';
          break;
      }
      return type;
    }
  }
}
</script>

<style scoped>
.c-flash {
  position: absolute;
  transition: all 3s;
  top: 15px;
  right: 10px;

  height: 60px;
  width: 480px;
  opacity: 0.7;

  border-radius: 4px;
  z-index: 5;

  font-size: 20px;
}
.c-flash__success {
  background: #b6e395;
}
.c-flash__error {
  background: #e39598;
}
</style>