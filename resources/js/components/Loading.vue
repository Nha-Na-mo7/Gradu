<template>
  <div>
    <!-- カバー -->
    <div class="c-loading__cover"></div>

    <!-- 四角い表示画面-->
    <div class="c-loading">

      <!-- メッセージのタイトル(例「検索中」) -->
      <div class="c-loading__message-area">
        <p class="c-loading__title">{{ this.title }}</p>
      </div>

      <!-- 円がグルグルしているエリア -->
      <div class="c-loading__circle-area">
        <span class="c-loading__circle">●</span>
      </div>
    </div>
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
.c-loading{
  width: 20%;
  height: 20%;
  position: fixed;
  display: flex;
  flex-direction: column;

  top: 40%;
  left: 40%;
  z-index: 4;
  border-radius: 10px;
  box-shadow: 0 0 15px #dedede;
}
.c-loading__message-area {
  height: 20%;
  text-align: center;
  margin: auto 0;
}
.c-loading__title {
  font-size: 20px;
}

.c-loading__circle-area {
  height: 70%;
  padding: 0 10%;
  background: #ffffff;
}
.c-loading__circle{
  border-radius: 25px;
  background: #4FB4D7;
  margin: 0 auto;
  height: 80px;
  width: 80px;
}
.c-loading__cover {
  transition: .1s all;
  width: 100%;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 5;
  opacity: 0.1;
  background: #000000;
}
</style>