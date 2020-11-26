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

</style>