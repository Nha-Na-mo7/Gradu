<!--===================================-->
<!--コンテンツ概要/更新時刻を表示するリボンナビ-->
<!--===================================-->
<template>
  <div class="c-ribbon">
    <!-- タイトル -->
    <p class="c-ribbon__content c-ribbon__content--left" v-if="title">
      {{ title }}
    </p>
    <!-- ナビの右側 更新時刻など-->
    <p class="c-ribbon__content c-ribbon__content--right" v-if="date">
      <i class="far fa-clock"></i>
      最終更新: <span class="c-ribbon__date">{{ date | update }}</span> JST
    </p>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  props: {
    title: {
      type: String,
      required: false,
    },
    date: {
      type: [Date, String],
      required: false,
    },
  },
  filters: {
    update: function (date) {
      return moment(date).format('YYYY/MM/DD HH:mm');
    },
  },
};
</script>

<style scoped></style>
