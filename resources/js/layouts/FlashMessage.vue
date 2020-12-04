<!--===============================-->
<!--フラッシュメッセージ用のコンポーネント-->
<!--===============================-->
<template>
  <div
    class="c-flash js-flash-msg"
    :class="bgColor"
    v-show="message"
    @click="hideFlash"
  >
    <div class="c-flash__text">
      <span>
        {{ message }}
      </span>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  computed: {
    ...mapState({
      message: (state) => state.message.content,
      type: (state) => state.message.type,
    }),
    bgColor() {
      var type = '';
      switch (this.type) {
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
    },
  },
  methods: {
    hideFlash() {
      this.$store.commit('message/setContent', {
        content: '',
      });
    },
  },
};
</script>

<style scoped></style>
