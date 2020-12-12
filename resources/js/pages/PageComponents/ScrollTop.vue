<!--===================================-->
<!--ページのトップにスクロールするコンポーネント-->
<!--===================================-->
<template>
  <button
      class="c-icon__scrolltop"
      :class="{'c-icon__scrolltop--none': scrollY < 120}"
      @click="scrollTop"
  >
    <span class="c-icon__scrolltop--text">↑</span>
  </button>
</template>

<script>
export default {
  data() {
    return {
      // Y軸の座標
      scrollY: 0
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll)
  },
  methods: {
    handleScroll() {
      this.scrollY = window.scrollY
    },
    scrollTop: function(){
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    }
  }
};
</script>