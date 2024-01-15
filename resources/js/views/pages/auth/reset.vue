<template>
  <div class="login-wrapper">
    <div class="w-100 d-flex">
      <div class="login-image">
        <img src="/images/login-left-girl.png" class="background" />
      </div>
      <div class="login-form-wrapper">
        <b-form class="p-2 login-form" @submit.prevent="reset">
          <div class="inner-form">
            <div class="logo-wrapper">
              <img src="/images/logo-new.png" class="logo" />
            </div>
            <div class="login-label">
              <span>Reset your password!</span>
            </div>
            <b-form-input
              type="password"
              v-model.trim="password"
              placeholder="Type your password"
              class="login-input mt-3"
            />
            <b-form-input
              type="password"
              v-model.trim="confirm"
              placeholder="Confirm password"
              class="login-input mt-3"
            />
            <b-button type="submit" class="login-button mt-4" ref="reset_button">
              Reset your password
            </b-button>
          </div>
        </b-form>
      </div>
    </div> 
  </div>
</template>

<style lang="scss" scoped>
.login-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  .login-image {
    width: 50%;
    text-align: center;
    @media (max-width: 992px) {
      display: none;
    }
    img {
      width: 80%;
      @media (max-width: 1200px) {
        width: 100% !important;
      }
    }
  }
  .login-form-wrapper {
    width: 50%;
    display: flex;
    align-items: center;
    flex: 1;
    margin-bottom: 5%;
    @media (max-width: 992px) {
      width: 100%;
      margin-bottom: 50px;
      display: flex;
      justify-content: center;
    }
    .login-form {
      max-width: 450px;
      width: 100%;
      .inner-form {
        padding: 10%;
        text-align: center;
      }
      .login-input {
        border-radius: 30px;
        padding: 0.75rem 1.5rem;
        height: auto !important;
      }
      .logo {
        width: 170px;
      }
      .login-label {
        margin-top: 20px;
        font-size: 1.2rem;
        line-height: 1.3;
        font-weight: 400;
        span {
          font-size: 1.5rem;
          font-weight: 800;
        }
      }
      .login-button {
        background-color: #1998e4;
        color: white;
        border-radius: 30px;
        padding: 0.75rem 1.5rem;
        height: auto !important;
        width: 100%;
        border: none !important;
        font-size: 1.1rem;
        font-weight: 500;
        &:hover {
          filter: brightness(110%);
        }
      }
    }
  }
}
</style>

<script>
import axios from 'axios';
import { LOGIN } from '@/services/store/auth.module';
export default {
  name: 'reset',
  data() {
    return {
      password: '',
      confirm: '',
      token: ''
    };
  },
  mounted() {
    if (this.$route.params.token) {
      this.token = decodeURIComponent(this.$route.params.token);
    }
    if (this.token === '') {
      // redirect to home
      this.$router.ush({ name: 'statistics'});
    }
  },
  methods: {
    reset() {
      if (this.password !== this.confirm || this.password == '') {
        this.$func.showTextMessage('Warning', 'Please correct your input!', 'error');
        return;
      }
      const submitButton = this.$refs['reset_button'];
      submitButton.disabled = true;
      const loader = this.$loading.show();
      axios.post('/api/reset', {
        token: this.token,
        password: this.password,
      }).then((res) => {
        if (res.data.user) {
          this.$store.dispatch(LOGIN, res.data);
          this.$router.push({ name: 'statistics'}).catch(() => {});
        } else {
          this.$func.showTextMessage('Warning', res.data, 'error');
        }
      }).catch((err) => {
        // console.log(err);
        this.$func.showTextMessage('Warning', 'Server error!', 'error');
      }).finally(() => {
        loader && loader.hide();
        submitButton.disabled = false;
      })
    }
  }
};
</script>
