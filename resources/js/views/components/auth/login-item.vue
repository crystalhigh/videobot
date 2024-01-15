<template>
  <b-form class="p-2 login-form" key="0" @submit.prevent="login">
    <img class="mt-3" src="/images/logo-new.png" alt="vidGEN"/>
    <div class="login-form-span-title">
      <span>Log in</span>
    </div>
    <div class="login-form-span mt-2">
      <span>In order to log into your Vidgen profile, please provide your email and password</span>
    </div>
    <div class="login-form-span login-form-email-title">
      <span>Email address</span>
    </div>
    <b-form-input
      type="email"
      v-model.trim="$v.email.$model"
      placeholder="For e.g.johndoe@gmail.com"
      class="login-input"
    />
    <div class="error mt-1">
      <span v-if="!$v.email.required && submitted">Email is required</span>
    </div>
    <div class="login-form-span login-form-password-title">
      <span>Password</span>
    </div>
    <b-form-input
      type="password"
      v-model="$v.password.$model"
      placeholder="Your password"
      class="login-input"
    />
    <div class="error mt-1">
      <span v-if="!$v.password.required && submitted">Password is required</span>
    </div>
    <div class="mt-2 login-form-span">
      <a
        href="javascript:;"
        @click="changeMode(1)"
        class="forgot-password"
        >Forgot password?</a>
    </div>
    <div class="mt-4 py-2">
      <button type="submit" class="btn btn-outline-secondary rounded-pill d-flex align-items-center justify-content-center p-2 btn-log-me-in" ref="login_button">
        <span class="text-white text-center">Log me in</span>
        <RightArrowIcon color="#ffffff" stroke="2"/> 
      </button>
    </div>
    <div class="login-form-span text-center login-form-register-title">
      Don't have an account yet? <a
        href="javascript:;"
        @click="changeMode(2)"
        class="login-form-btn-register"
        >Register here</a>
    </div>
  </b-form>
</template>

<style lang="scss" scoped>
  .error {
    color: #d64c2d;
    text-align: left;
    margin-left: 24px;
    font-weight: 600;
  }
  @media (max-width: 768px) {
    img {
      width: 120px;
      height: 30px;
    }
    .login-form-span-title {
      font-size: 20px;
      font-weight: 700;
      margin-top: 20px;
    }
    .login-form-span {
      font-size: 14px;
    }
    .login-form-email-title, .login-form-password-title, .login-form-register-title {
      margin-top: 0.5rem;
    }
    .login-input {
      padding: 0.3rem 1rem;
    }
    .btn-log-me-in {
      font-size: 14px;
    }
  }
  @media (min-width: 768px) {
    img {
      width: 169px;
      height: 38px;
    }
    .login-form-span-title {
      font-size: 32px;
      font-weight: 700;
      margin-top: 60px;
    }
    .login-form-span {
      font-size: 16px;
    }
    .login-form-email-title, .login-form-password-title, .login-form-register-title {
      margin-top: 1.5rem;
    }
    .login-input {
      padding: 0.75rem 1.5rem;
    }
    .btn-log-me-in {
      font-size: 16px;
    }
  }
  .login-form {
    .login-form-span {
      color: black;
    }
    
    .login-input {
      border-radius: 30px;
      height: auto !important;
    }
    .forgot-password {
      color: #7D7D7D;
      font-weight: 700;
      &:hover {
        filter: brightness(110%);
      }
    }
    .login-form-btn-register {
      color: black;
      text-decoration: underline;
      &:hover {
        color: #474747;
      }
    }
    .btn-log-me-in {
      width: 100%;
      background-color: #474747;
      border-color: #474747;
      gap: 12px;
      &:hover {
        background-color: #777777;
        border-color: #777777;
      }
    }
  }
</style>
<script>
import { required, email } from 'vuelidate/lib/validators';
import RightArrowIcon from '@/views/components/icon/RightArrowIcon';
export default {
  name: 'login-item',
  components: {
    RightArrowIcon,
  },
  data() {
    return {
      email: '',
      password: '',
      submitted: false,
    };
  },
  validations: {
    email: {
      required,
      email
    },
    password: {
      required
    },
  },
  mounted() {

  },
  methods: {
    login() {
      this.$v.email.$touch();
      this.$v.password.$touch();
      this.submitted = true;
      if (this.email === '' || this.password === '') {
        return;
      }
      this.$emit('onLogin', {email: this.email, password: this.password});
    },
    changeMode(mode) {
      this.$emit('onChangeMode', mode);
    }
  }
};
</script>
