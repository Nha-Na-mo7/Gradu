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
    <div class="c-flash__column--left">
      <div class="c-flash__icon">
        <img :src="messageIcon" alt="message" />
      </div>
    </div>
    <div class="c-flash__text">
      <span>
        {{ message }}
      </span>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import { FLASH_ICON_PATH } from "../util";

export default {
  computed: {
    ...mapState({
      message: (state) => state.message.content,
      type: (state) => state.message.type,
    }),
    bgColor() {
      var type = "";
      switch (this.type) {
        case 0:
          type = "";
          break;
        case 1:
          type = "c-flash__success";
          break;
        case 2:
          type = "c-flash__error";
          break;
      }
      return type;
    },
    messageIcon() {
      var icon = FLASH_ICON_PATH + "ring.svg";
      switch (this.type) {
        case 0:
          break;
        case 1:
          icon = FLASH_ICON_PATH + "check.svg";
          break;
        case 2:
          icon = FLASH_ICON_PATH + "cross.svg";
          break;
      }
      return icon;
    },
  },
  methods: {
    hideFlash() {
      this.$store.commit("message/setContent", {
        content: "",
      });
    },
  },
};
</script>

<style scoped></style>
