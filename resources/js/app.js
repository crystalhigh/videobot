import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from '@/services/store';
import axios from 'axios';
import '@/services/axios.service.js';
import '../sass/init.scss'; // this is important to let vue-loader load styleLoader

import { func } from '@/services/func.service.js';
Vue.prototype.$func = func;

Vue.prototype.$http = axios;
Vue.config.productionTip = false;

import { BootstrapVue } from 'bootstrap-vue';
Vue.use(BootstrapVue);

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
Vue.use(Loading, {
  color: '#81aee9',
  loader: 'dots',
  canCancel: false
});

import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
Vue.component('v-select', vSelect);

import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';

library.add(fas);
library.add(far);
Vue.component('fa-icon', FontAwesomeIcon);

import PerfectScrollbar from 'vue2-perfect-scrollbar';
import 'vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css';
Vue.use(PerfectScrollbar);

import vueDebounce from 'vue-debounce';
Vue.use(vueDebounce);

import VueCalendly from 'vue-calendly';
Vue.use(VueCalendly);

import VueProgress from 'vue-progress';
Vue.use(VueProgress);

import VueMasonry from 'vue-masonry-css';
Vue.use(VueMasonry);


new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app');
