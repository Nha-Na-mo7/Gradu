<!-- RouterView部分において各コンポーネントが切り替わります -->
<template>
  <div class="l-content-wrapper">
    <!-- ヘッダー -->
    <header id="header" class="l-header">
      <Header />
    </header>

    <!-- メイン -->
    <main>
      <div class="l-container">
        <RouterView />
      </div>
    </main>

    <!-- フッター -->
    <footer id="footer" class="l-footer p-footer">
      <Footer />
    </footer>
  </div>
</template>

<script>
import Header from './components/Header.vue';
import Footer from './components/footer.vue';

export default {
  components: {
    Header,
    Footer
  }
}

</script>