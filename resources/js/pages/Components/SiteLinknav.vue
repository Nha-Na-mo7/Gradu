<!--============================================-->
<!--"トップ > XXXX" 形式のサイトリンクナビコンポーネント-->
<!--============================================-->
<template>
  <div class="c-site-linknav">
    <RouterLink class="c-site__link-nav__to-top" to="/">トップ</RouterLink>
    <span>></span>
    <RouterLink class="c-site__link-nav__to-content" :to="currentPath">{{ currentPageTitle }}</RouterLink>
  </div>
</template>

<script>
export default {
  props: {
    currentPageTitle: {
      type: String,
      required: true
    }
  },
  computed: {
    currentPath() {
      return this.$route.path
    },
  }
}
</script>

<style scoped>

</style>