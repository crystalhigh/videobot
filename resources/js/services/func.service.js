import Swal from 'sweetalert2';
export const func = {
  makeToast: (handle, title, msg, variant, duration = 2000) => {
    handle.$bvToast.toast(msg, {
      title: title,
      variant: variant,
      solid: true,
      autoHideDelay: duration
    });
  },

  showTextMessage: (title, msg, icon) => {
    Swal.fire({
      title: title,
      icon: icon,
      text: msg
    });
  },

  showHtmlMessage: (title, msg, icon) => {
    Swal.fire({
      title: title,
      icon: icon,
      html: msg
    });
  },

  capitalized: str => {
    return str.charAt(0).toUpperCase() + str.slice(1);
  },

  formatDate(date) {
    if (!date) {
      return '';
    }
    const dt = new Date(date);
    return `${(dt.getMonth() + 1).toString().padStart(2, '0')}/${dt
      .getDate()
      .toString()
      .padStart(2, '0')}/${dt
      .getFullYear()
      .toString()
      .padStart(4, '0')} ${dt
      .getHours()
      .toString()
      .padStart(2, '0')}:${dt
      .getMinutes()
      .toString()
      .padStart(2, '0')}:${dt
      .getSeconds()
      .toString()
      .padStart(2, '0')}`;
  },

  validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
  },

  formatTime(time) {
    if (time > 0 && isNaN(time)) {
      return new Date(Math.ceil(time) * 1000).toISOString().substr(14, 5);
    }
    return '00:00';
  },

  getDate(timestamp = null, firstday = false) {
    let val = timestamp ? new Date(timestamp) : new Date();
    let year = val.getFullYear()
    let month = val.getMonth() + 1
    month = month < 10 ? '0' + month : month
    let day = firstday ? 1 : val.getDate()
    return `${year}-${month}-${day}`
  },

  validateStep(step) {
    if (
      step.answer.type === 2 &&
      (!step.answer.option || (step.answer.option && !step.answer.option.link))
    ) {
      return {
        state: false,
        msg: 'You need to add action link!'
      };
    }
    return {
      state: true
    };
  }
};
