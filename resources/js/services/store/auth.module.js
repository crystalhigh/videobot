import Cookies from 'js-cookie';

export const LOGIN = 'login';
export const LOGOUT = 'logout';
export const SET_AUTH = 'setAuth';
export const UPDATE_AUTH = 'updateAUth';
export const PURGE_AUTH = 'removeAuth';
export const TUTORIAL = 'tutorial';
export const UPDATE_TUTORIAL = 'updateTutorial';

const userc = localStorage.getItem('vpop-user');

const state = {
  user:
    'undefined' === typeof userc
      ? null
      : JSON.parse(localStorage.getItem('vpop-user')),
  token: Cookies.get('token'),
  tutorial: Cookies.get('tutorial') || 0,
  s3Storage: Cookies.get('s3Storage') || 0
};

const getters = {
  currentUser: state => state.user,
  token: state => state.token,
  check: state => state.user !== null && state.user.approved,
  tutorial: state => state.tutorial,
  s3Storage: state => state.s3Storage
};

const actions = {
  [LOGIN](context, user) {
    context.commit(SET_AUTH, user);
  },
  [LOGOUT](context) {
    context.commit(PURGE_AUTH);
  },
  [UPDATE_AUTH](context, user) {
    context.commit(UPDATE_AUTH, user);
  },
  [TUTORIAL](context) {
    context.commit(UPDATE_TUTORIAL, 0);
  }
};

const mutations = {
  [SET_AUTH](state, data) {
    state.user = {
      ...data.user,
      origin_level: data.origin_level || ''
    };
    localStorage.setItem(
      'vpop-user',
      JSON.stringify({
        ...data.user,
        origin_level: data.origin_level || ''
      })
    );
    Cookies.set('token', data.access_token, { expires: data.expires_in });
    Cookies.set('tutorial', data.user.show_tutorial, {
      expires: data.expires_in
    });
    state.tutorial = data.user.show_tutorial;
    state.s3Storage = data.s3.toFixed(2);
    Cookies.set('s3Storage', data.s3.toFixed(2), { expires: data.expires_in });
  },
  [PURGE_AUTH](state) {
    state.user = null;
    state.token = null;
    localStorage.removeItem('vpop-user');
    Cookies.remove('token');
    localStorage.removeItem('vidpop');
    localStorage.removeItem('step');
    Cookies.remove('tutorial');
    Cookies.remove('s3Storage');
  },
  [UPDATE_AUTH](state, data) {
    state.user = data;
    localStorage.setItem('vpop-user', JSON.stringify(data));
  },
  [UPDATE_TUTORIAL](payload) {
    Cookies.set('tutorial', 0);
    state.tutorial = 0;
  }
};

export default {
  state,
  actions,
  mutations,
  getters
};
