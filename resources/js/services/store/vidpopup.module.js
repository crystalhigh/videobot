export const VIDPOP = 'vidpop';
export const SET_VIDPOP = 'setVidpopup';
export const STEP = 'step';
export const SET_STEP = 'setStep';
export const VIDPOP_UPDATED = 'vidpopUpdated';
export const SET_VIDPOP_UPDATED = 'setVidpopUpdated';
export const STEP_UPDATED = 'stepUpdated';
export const SET_STEP_UPDATED = 'setStepUpdated';
export const UNREAD_UPDATED = 'unreadUpdated';
export const SET_UNREAD = 'setUnread';
export const SET_DURATION = 'setDuration';
export const UPDATE_DURATION = 'updateDuration';

export const ADV_REQUEST_COUNT = 'advReqCount';
export const SET_ADV_REQUEST_COUNT = 'setAdvReqCount';

const vidpop = localStorage.getItem('vidpop');
const step = localStorage.getItem('step');

const state = {
  vidpop:
    'undefined' === typeof vidpop || vidpop === null
      ? {
          id: '',
          name: '',
          end_font: '',
          end_bg: '',
          end_color: '',
          end_position: '',
          title: '',
          title_size: '2.5rem',
          content: '',
          content_size: '1.5rem',
          is_custom: 0,
          custom_text: '',
          custom_color: '#ffffff',
          custom_bgcolor: '#199e84',
          custom_link: '',
          advanced: {
            show_control: 0,
            auto_play: 0,
            oversize: 0,
            show_title: 0,
            reply_noti: 0,
            button_radius: 0
          },
          social: '',
          widget: '',
          brand: '',
          code: '',
          is_template: 0,
          integration: '',
          list: [],
          arlist: ''
        }
      : JSON.parse(localStorage.getItem('vidpop')),
  defaultVidpop: {
    id: '',
    name: '',
    end_font: '',
    show_control: 0,
    end_bg: '',
    end_color: '',
    end_position: '',
    title: '',
    title_size: '2.5rem',
    content: '',
    content_size: '1.5rem',
    is_custom: 0,
    custom_text: '',
    custom_color: '#ffffff',
    custom_bgcolor: '#199e84',
    custom_link: '',
    advanced: {
      show_control: 0,
      auto_play: 0,
      oversize: 0,
      show_title: 0,
      reply_noti: 0,
      button_radius: 0
    },
    social: '',
    widget: '',
    brand: '',
    code: '',
    is_template: 0,
    integration: '',
    list: [],
    arlist: ''
  },
  step:
    'undefined' === typeof step || step === null
      ? {
          id: '',
          vidpop_id: '',
          video_url: '',
          thumb_url: '',
          video_note: '',
          video_cc: '',
          fit_video: 0,
          next_step: '',
          overlay: '',
          answer: '',
          video_delay: 2,
          data_collection: 0,
          duration: 0
        }
      : JSON.parse(localStorage.getItem('step')),
  defaultStep: {
    id: '',
    vidpop_id: '',
    video_url: '',
    thumb_url: '',
    video_note: '',
    video_cc: '',
    fit_video: 0,
    next_step: '',
    overlay: '',
    answer: '',
    video_delay: 2,
    data_collection: 0,
    duration: 0
  },
  vidpopUpdated: false,
  stepUpdated: false,
  unread: 0,
  duration: 0,
  advReqCount: 0,
};

const getters = {
  vidpop: state => state.vidpop,
  defaultVidpop: state => state.defaultVidpop,
  step: state => state.step,
  defaultStep: state => state.defaultStep,
  vidpopUpdated: state => state.vidpopUpdated,
  stepUpdated: state => state.stepUpdated,
  unread: state => state.unread,
  duration: state => state.duration,
  advReqCount: state => state.advReqCount,
};

const actions = {
  [VIDPOP](context, payload) {
    context.commit(SET_VIDPOP, payload);
  },
  [STEP](context, payload) {
    context.commit(SET_STEP, payload);
  },
  [VIDPOP_UPDATED](context, payload) {
    context.commit(SET_VIDPOP_UPDATED, payload);
  },
  [STEP_UPDATED](context, payload) {
    context.commit(SET_STEP_UPDATED, payload);
  },
  [UNREAD_UPDATED](context, payload) {
    context.commit(SET_UNREAD, payload);
  },
  [UPDATE_DURATION](context, payload) {
    context.commit(SET_DURATION, payload);
  },
  [ADV_REQUEST_COUNT](context, payload) {
    context.commit(SET_ADV_REQUEST_COUNT, payload);
  }
};

const mutations = {
  [SET_VIDPOP](state, data) {
    state.vidpop = data;
    localStorage.setItem('vidpop', JSON.stringify(data));
  },
  [SET_STEP](state, data) {
    state.step = data;
    localStorage.setItem('step', JSON.stringify(data));
  },
  [SET_VIDPOP_UPDATED](state, data) {
    state.vidpopUpdated = data;
    localStorage.setItem('vidpopUpdated', data);
  },
  [SET_STEP_UPDATED](state, data) {
    state.stepUpdated = data;
    localStorage.setItem('stepUpdated', data);
  },
  [SET_UNREAD](state, data) {
    state.unread = data;
  },
  [SET_DURATION](state, data) {
    state.duration = data;
  },
  [SET_ADV_REQUEST_COUNT](state, data) {
    state.advReqCount = data;
  }
};

export default {
  state,
  actions,
  mutations,
  getters
};
