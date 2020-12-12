<!--===================================-->
<!--ページのトップにスクロールするコンポーネント-->
<!--===================================-->
<template>
  <button
      v-if="checkCanUseScrollAgent"
      class="c-icon__scrolltop"
      :class="{'c-icon__scrolltop--none': scrollY < 120}"
      @click="scrollTop"
  >
    <!-- ^が2つ並んだアイコン -->
    <span class="c-icon__scrolltop--text">
      <i class="fas fa-angle-double-up"></i>
    </span>
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
  computed: {
    // Android4.4.4以下はscroll系のメソッド非対応なのでそもそも表示させない
    checkCanUseScrollAgent() {
      var version = ''
      // ブラウザ情報を取得
      var userAgent = window.navigator.userAgent
      // Androidであればバージョンを取得
      if( userAgent.indexOf('Android') > 0 ) {
        version = parseFloat(userAgent.slice(userAgent.indexOf('Android')+8))
      } else {
        // Androidでなければtrue
        return true
      }
      return version > 4.4;
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