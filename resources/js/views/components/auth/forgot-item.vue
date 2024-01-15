<template>
  <b-form class="p-2 forgot-form" key="1" @submit.prevent="forgot">
    <img class="mt-3" src="/images/logo-new.png" alt="vidGEN"/>
    <div class="forgot-form-span-title">
      <span>Forgot your password?</span>
    </div>
    <div class="forgot-form-span mt-2">
      <span>Please enter your email...</span>
    </div>
    <div class="forgot-form-span forgot-form-email-title">
      <span>Email address</span>
    </div>
    <b-form-input
      type="email"
      v-model.trim="$v.email.$model"
      placeholder="For e.g.johndoe@gmail.com"
      class="forgot-input mt-2"
    />
    <div class="error mt-1">
      <span v-if="!$v.email.required && submitted">Email is required</span>
    </div>
    <div class="mt-4 py-2">
      <button type="submit" class="btn btn-outline-secondary rounded-pill d-flex align-items-center justify-content-center p-2 btn-reset" ref="forgot_button">
        <span class="text-white text-center">Reset my password</span>
        <RightArrowIcon color="#ffffff" stroke="2"/> 
      </button>
    </div>
    <div class="forgot-form-span text-center forgot-form-back-title">
      <a
        href="javascript:;"
        @click="changeMode(0)"
        class="forgot-form-btn-back"
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
    .forgot-form-span-title {
      font-size: 20px;
      font-weight: 700;
      margin-top: 20px;
    }
    .forgot-form-span {
      font-size: 14px;
    }
    .forgot-form-email-title, .forgot-form-back-title {
      margin-top: 0.5rem;
    }
    .forgot-input {
      padding: 0.3rem 1rem;
    }
    .btn-reset {
      font-size: 14px;
    }
  }
  @media (min-width: 768px) {
    img {
      width: 169px;
      height: 38px;
    }
    .forgot-form-span-title {
      font-size: 32px;
      font-weight: 700;
      margin-top: 60px;
    }
    .forgot-form-span {
      font-size: 16px;
    }
    .forgot-form-email-title, .forgot-form-back-title {
      margin-top: 1.5rem;
    }
    .forgot-input {
      padding: 0.75rem 1.5rem;
    }
    .btn-reset {
      font-size: 16px;
    }
  }
  .forgot-form {
    .btn-reset {
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
    .forgot-input {
      border-radius: 30px;
      height: auto !important;
    }
  }
</style>
<script>
import { required, email } from 'vuelidate/lib/validators';
import RightArrowIcon from '@/views/components/icon/RightArrowIcon';
export default {
  name: 'forgot-item',
  components: {
    RightArrowIcon,
  },
  data() {
    return {
      email: '',
      submitted: false,
    };
  },
  validations: {
    email: {
      required,
      email
    }
  },
  mounted() {

  },
  methods: {
    forgot() {
      this.$v.email.$touch();
      this.submitted = true;
      if (this.email === '') {
        return;
      }
      this.$emit('onForgot', {email: this.email});
    },
    changeMode(mode) {
      this.$emit('onChangeMode', mode);
    }
  }
};
</script>
