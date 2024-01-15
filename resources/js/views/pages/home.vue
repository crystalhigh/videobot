<template>
  <div id="page-home">
    <b-container class="h-100">
      <div v-html="page" />
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
#page-home {
  padding: 50px;
  height: 100%;
  @media (max-width: 575px) {
    padding: 20px;
  }
}
</style>

<script>
import axios from 'axios';
export default {
  name: 'home',
  data() {
    return {
      page: ''
    };
  },
  mounted() {
    const loader = this.$loading.show();
    axios
      .get('/api/page?label=Home')
      .then(res => {
        if (res && res.status === 200) {
          this.page = res.data.page;
        } else {
        }
      })
      .catch(() => {})
      .finally(() => {
        loader && loader.hide();
      });
  },
  methods: {}
};
</script>
