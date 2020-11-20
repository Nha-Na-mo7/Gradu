<template>
<div class="l-container__content">

  <!-- サイトリンク -->
  <SiteLinknav :currentPageTitle='page_title'/>

  <!-- ページタイトル -->
  <PageTitle :title='page_title'/>








</div>
</template>

<script>
import SiteLinknav from '../Components/SiteLinknav.vue';
import PageTitle from '../Components/PageTitle.vue';
const PAGE_TITLE = 'マイページ(アカウント設定)';

export default {
  computed: {
    page_title() {
      return PAGE_TITLE
    }
  },
  components: {
    SiteLinknav,
    PageTitle
  }
}
</script>

<style scoped>

</style>