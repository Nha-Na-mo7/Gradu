<!--============================-->
<!--ページネーション用のコンポーネント-->
<!--============================-->

<template>
  <div class="c-pagination">
    <RouterLink
        v-if="!isFirstPage"
        :to="`?p=${currentPage - 1}`"
        class="c-btn"
    >&laquo; prev</RouterLink>
    <RouterLink
        v-if="!isLastPage"
        :to="`?p=${currentPage + 1}`"
        class="c-btn"
    >next &raquo;</RouterLink>
  </div>
</template>

<script>
export default {
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    lastPage: {
      type: Number,
      required: true
    }
  },
  computed: {
    isFirstPage() {
      return this.currentPage === 1
    },
    isLastPage() {
      return this.currentPage === this.lastPage
    }
  }
}
</script>

<style scoped>

</style>