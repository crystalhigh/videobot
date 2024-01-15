<template>
  <b-form class="p-2 register-form" key="2" @submit.prevent="register">
    <img class="mt-3" src="/images/logo-new.png" alt="vidGEN" width="169" height="38"/>
    <div class="signup-form-span-title">
      <span>Register</span>
    </div>
    <div class="signup-form-span signup-form-email-title">
      <span>Name</span>
    </div>
    <b-form-input
      type="text"
      v-model.trim="$v.name.$model"
      placeholder="John Doe"
      class="signup-input"
    />
    <div class="error mt-1">
      <span v-if="!$v.name.required && submitted">Name is required</span>
    </div>
    <div class="signup-form-span signup-form-email-title">
      <span>Email address</span>
    </div>
    <b-form-input
      type="email"
      v-model.trim="$v.email.$model"
      placeholder="For e.g.johndoe@gmail.com"
      class="signup-input"
    />
    <div class="error mt-1">
      <span v-if="!$v.email.required && submitted">Email is required</span>
    </div>
    <div class="signup-form-span signup-form-password-title">
      <span>Password</span>
    </div>
    <b-form-input
      type="password"
      v-model="$v.password.$model"
      placeholder="Your password"
      class="signup-input"
    />
    <div class="error mt-1">
      <span v-if="!$v.password.required && submitted">Password is required</span>
    </div>
    <div class="signup-form-span signup-form-password-title">
      <span>Confirm Password</span>
    </div>
    <b-form-input
      type="password"
      v-model="$v.repassword.$model"
      placeholder="Your password"
      class="signup-input"
    />
    <div class="error mt-1">
      <span v-if="!$v.repassword.required && submitted">Confirm Password is required</span>
    </div>
    <div class="signup-form-span signup-form-password-title">
      <span>User Role</span>
    </div>
    <b-form-select v-model="role" :options="options"  class="signup-input">
    </b-form-select>
    <div class="mt-4 py-2">
      <button type="submit" class="btn btn-outline-secondary rounded-pill d-flex align-items-center justify-content-center p-2 btn-sign-up" ref="register_button">
        <span class="text-white text-center">Sign up</span>
        <RightArrowIcon color="#ffffff" stroke="2"/> 
      </button>
    </div>
    <div class="signup-form-span text-center signup-form-back-title">
      <a
        href="javascript:;"
        @click="changeMode(0)"
        class="signup-form-btn-register"
        >Back to login</a>
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
    .signup-form-span-title {
      font-size: 20px;
      font-weight: 700;
      margin-top: 20px;
    }
    .signup-form-span {
      font-size: 14px;
    }
    .signup-form-email-title, .signup-form-password-title, .signup-form-register-title {
      margin-top: 0.5rem;
    }
    .signup-input {
      padding: 0.3rem 1rem;
    }
    .btn-sign-up {
      font-size: 14px;
    }
  }
  @media (min-width: 768px) {
    img {
      width: 169px;
      height: 38px;
    }
    .signup-form-span-title {
      font-size: 32px;
      font-weight: 700;
      margin-top: 60px;
    }
    .signup-form-span {
      font-size: 16px;
    }
    .signup-form-email-title, .signup-form-password-title, .signup-form-back-title {
      margin-top: 1.5rem;
    }
    .signup-input {
      padding: 0.75rem 1.5rem;
    }
    .btn-sign-up {
      font-size: 16px;
    }
  }
  .register-form {
    width: 100%;
    .signup-form-span {
      color: black;
    }
    .signup-input {
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
    .signup-form-btn-register {
      color: black;
      text-decoration: underline;
      &:hover {
        color: #474747;
      }
    }
    .btn-sign-up {
      width: 100%;
      background-color: #474747;
      border-color: #474747;
      gap: 12px;
      &:hover {
        background-color: #777777;
        border-color: #777777;
      }
    }
    .forgot-form-btn-back {
      color: #474747;
      text-decoration: underline;
      &:hover {
        color: black;
      }
    }  
  }
</style>

<script>
import { required, email } from 'vuelidate/lib/validators';
import RightArrowIcon from '@/views/components/icon/RightArrowIcon';
export default {
  name: 'register-item',
  components: {
    RightArrowIcon,
  },
  data() {
    return {
      name: '',
      email: '',
      password: '',
      repassword: '',
      role: 'advertiser',
      options: [
        {value: 'advertiser', text: 'advertiser'},
        {value: 'publisher', text: 'publisher'},
        {value: 'agent', text: 'agent'}
      ],
      submitted: false,
    };
  },
  validations: {
    password: {
      required
    },
    repassword: {
      required
    },
    email: {
      required,
      email
    },
    name: {
      required
    }
  },
  mounted() {

  },
  methods: {
    register() {
      this.$v.email.$touch();
      this.$v.password.$touch();
      this.submitted = true;
      if (this.email === '' || this.password === '' || this.name === '') {
        return;
      }
      if (this.password !== this.repassword) {
        this.$func.showTextMessage(
            'Warning',
            'Incorrect confirm password!',
            'error'
          );
        return;
      }
      this.$emit('onRegister', {name: this.name, email: this.email, password: this.password, role: this.role});
    },
    changeMode(mode) {
      this.$emit('onChangeMode', mode);
    }
  }
};
</script>
