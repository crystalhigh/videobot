import Vue from 'vue';
import axios from 'axios';
import store from './store';
import { PURGE_AUTH } from './store/auth.module';
import router from '../router';

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
Vue.config.productionTip = false;

axios.interceptors.request.use(request => {
  const token = store.getters['token'];
  if (token) {
    request.headers.common['Authorization'] = `Bearer ${token}`;
  }

  return request;
});

axios.interceptors.response.use(
  response => {
    return response;
  },
  error => {
    const { status } = error.response;

    if (status === 401 && store.getters.check) {
      store.commit(PURGE_AUTH);
      router.push({ name: 'login' });
    } else {
      return error.response;
    }
  }
);
