export const authcheckMixin =  {
  methods: {
    async auth_check(){
      const response = await axios.get(`/api/user/check`);
      // エラーチェック
      if(response.data === 419) {
        this.$store.commit('error/setErrorCode', 419);
      }
      return true;
    },
  }
}