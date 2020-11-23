<!--============================================-->
<!--各ページの上部に表示されるページタイトルコンポーネント-->
<!--============================================-->
<template>
  <div class="c-site__title">
    <span>{{ title }}</span>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    }
  },
}
</script>

<style scoped>

</style>