<template>
  <router-view></router-view>
</template>

<script>
import Axios from 'axios';

export default {
  name: "vidpopup",
  mounted() {
    // const intervalId = setInterval(() => {
    //   this.getConversionData();
    // }, 3000);
    // this.getConversionData();
  },
  methods: {
    getConversionData() {
      axios.get('/api/conversion-data?from=2023-12-18&to=2023-12-22')
      .then(resp => {
        console.log('conversions =>', resp);
      })
      .catch(err => {
        console.log('err =>', err);
      })
      // axios.defaults.headers.common['Accept'] = 'application/json';
      // axios.defaults.headers.common['Content-Type'] = 'application/json;charset=utf-8';
      // // axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
      // // axios.defaults.headers.common['Access-Control-Allow-Methods'] = 'GET, PUT, POST, DELETE, OPTIONS, post, get';
      // // axios.defaults.headers.common['Access-Control-Allow-Headers'] = 'Origin, Content-Type, X-Auth-Token';
      // // axios.defaults.headers.common['Access-Control-Allow-Credentials'] = 'true';
      // // axios.defaults.headers.common['Referrer'] = 'localhost';
      // axios.post('https://api.voluum.com/auth/access/session', {
      //   accessId: "e4bd6554-a0e4-4f29-a8f7-3ad87e0ec443",
      //   accessKey: "glolSIOpM5ebi0-A2702Rb2Eo_jGokwyRFuk"
      // })
      // .then(res => {
      //   console.log('session => ', res);
      // })
      // .catch(err => {
      //   console.log(err);
      // })
    }
  }
};
</script>
