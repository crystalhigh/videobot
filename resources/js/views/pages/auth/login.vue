<template>
  <div class="login-wrapper min-vh-100 pt-2 d-flex flex-column">
    <header class="login-wrapper-header">
      <b-navbar toggleable="md" type="dark" class="px-0 pb-3 login-wrapper-header-navbar">
        <div class="navbar-logo">
          <a href="/">
            <img src="/images/logo-new.png" alt="vidGEN" width="169" height="38"/>
          </a>
        </div>
        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
        <b-collapse id="nav-collapse" is-nav class="justify-content-end">
          <!-- <ul class="pl-0 mb-0 flex-grow-1 text-center navbar-container">
            <li class="nav-link">
              <a href="#" class="p-2 text-white text-decoration-none">Advertisers
              </a>
              <div class="underline"></div>
            </li>
            <li class="nav-link">
              <a href="#" class="p-2 text-white text-decoration-none">About Us</a>
              <div class="underline"></div>
            </li>
            <li class="nav-link" :class="mode == 3 ? 'active-link' : ''">
              <a class="p-2 text-white text-decoration-none" @click="mode=3">Contact</a>
              <div class="underline"></div>
            </li>
          </ul> -->
          <button type="button" class="btn btn-outline-secondary text-white btn-signup btn-px" @click="() => {loginShow = true; mode = 2;}">Sign Up</button>
          <button type="button" class="btn btn-outline-secondary text-white btn-login btn-px" @click="() => {loginShow = true; mode = 0;}">Log in</button>
        </b-collapse>
      </b-navbar>
    </header>
    <main style="position: relative; flex-grow: 1;" class="d-flex">
      <div class="login-wrapper-content d-flex align-items-center" :class="loginShow ? 'login-wrapper-content-start login-wrapper-content-start-class' : 'login-wrapper-content-center login-wrapper-content-center-class'">
        <div class="text-white py-3 login-wrapper-content-evolution" v-if="mode != 3">
          <div class="login-wrapper-content-evolution-header">
            <span>Branding + Engagement = Elite Performance</span>
          </div>
          <div class="underline mt-3" :class="!loginShow ? '' : 'underline-transparent'"></div>
          <div class="login-wrapper-content-evolution-title mt-3">
            <span>The Evolution of Lead Generation</span>
          </div>
          <a type="button" class="btn btn-outline-secondary text-white rounded-pill btn-get-started">Get Started</a>
        </div>
        <div class="text-white py-3 login-wrapper-content-contact" v-else>
          <div class="login-wrapper-content-contact-header">
            <span>Generate Better Leads.</span>
          </div>
          <div class="login-wrapper-content-contact-title mt-3">
            <span>Use Next generation video Technology to create high performing video ads to drive high quality leads</span>
          </div>
          <b-form
            class="p-2 mt-3 login-wrapper-content-contact-form"
            key="4"
            @submit.prevent="contact">
            <b-form-input
              type="text"
              v-model.trim="$v.send_firstname.$model"
              placeholder="First Name"
              class="login-input mt-2"
            />
            <b-form-input
              type="text"
              v-model.trim="$v.send_lastname.$model"
              placeholder="Last Name"
              class="login-input mt-2"
            />
            <b-form-input
              type="email"
              v-model.trim="$v.send_email.$model"
              placeholder="Your Email"
              class="login-input mt-2"
            />
            <b-form-textarea
              v-model="send_message.$model"
              placeholder="Your Message"
              class="login-input mt-2"
              rows="4"
              max-rows="4"
            />
            <button type="submit" class="btn btn-outline-secondary rounded-pill d-flex align-items-center justify-content-between btn-send-email" ref="send_button">
              <span class="btn-send-email-caption text-center">Send Mail</span>
              <RightArrowIcon color="#000000" stroke="3"/> 
            </button>
          </b-form>
        </div>
      </div>
    </main>
    <footer class="login-wrapper-footer py-3 d-flex align-items-center justify-content-between" v-if="mode==3">
      <div class="login-wrapper-footer-icons d-flex flex-row">
        <!-- <TwitterIcon class="mx-1" />
        <InstagramIcon class="mx-1" />
        <FacebookIcon class="mx-1" />
        <LinkedinIcon class="mx-1" />
        <GithubIcon class="mx-1" /> -->
      </div>
      <div class="login-wrapper-footer-text d-flex">
        <div class="px-3 login-wrapper-footer-text-copyright">
          <span class="text-white">&copy; 2023 Vidgen Inc. All rights reserved.</span>
        </div>
        <div class="px-3 login-wrapper-footer-text-nav">
          <ul class="login-wrapper-footer-text-nav-list pl-0 mb-0">
            <li class="d-inline-block px-2">
              <a href="/privacy" class="text-white" _target="blank">Privacy Policy</a>
            </li>
            <li class="d-inline-block px-2">
              <a href="/terms" class="text-white" _target="blank">Terms of Service</a>
            </li>
            <!-- <li class="d-inline-block px-2">
              <a href="" class="text-white">Notice</a>
            </li> -->
          </ul>
        </div>
      </div>
    </footer>
    <div class="blur" v-if="loginShow"></div>
    <div class="auth-form d-flex align-items-center justify-content-center" v-if="mode != 3" :class="loginShow ? 'auth-form-show' : 'auth-form-hide'">
      <div class="auth-form-btn-close" @click="closeLoginDialog">
        <fa-icon :icon="['fas', 'times']" />
      </div>
      <login-item v-if="mode==0" @onLogin="onLogin" @onChangeMode="onChangeMode"/>
      <forgot-item v-else-if="mode==1" @onForgot="onForgot" @onChangeMode="onChangeMode"/>
      <register-item v-else-if="mode==2" @onRegister="onRegister" @onChangeMode="onChangeMode"/>
    </div>
  </div>
</template>

<style lang="scss" scoped>
  @keyframes evolution-center-animation {
    0% {
      position: absolute;
      top: 50%;
      left: 3rem;
      transform: translateY(-50%);
      text-align: left;
    }
    100% {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }
  }
  @keyframes evolution-start-animation {
    0% {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }
    100% {
      position: absolute;
      top: 50%;
      left: 3rem;
      transform: translateY(-50%);
      text-align: left;
    }
  }
  .login-wrapper-content-center-class {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
  }
  .login-wrapper-content-start-class {
    position: absolute;
    top: 50%;
    left: 3rem;
    transform: translateY(-50%);
    text-align: left;
  }
  .login-wrapper-content-center {
    animation: evolution-center-animation .8s ease-in-out;
  }
  .login-wrapper-content-start {
    animation: evolution-start-animation .8s ease-in-out;
  }
  @keyframes login-show-animation {
    0% {
      right: -480px;
      opacity: .1;
    }
    100% {
      right: 20px;
      opacity: 1;
    }
  }
  .auth-form-show {
    right:20px;
    animation: login-show-animation 0.8s ease-out;
  }
  @keyframes login-hide-animation {
    0% {
      right: 20px;
      opacity: 1;
    }
    100% {
      right: -440px;
      opacity: .1;
    }
  }
  .auth-form-hide {
    right:-480px;
    animation: login-hide-animation 0.8s ease-out;
  }
  @media (min-width: 768px) {
    .auth-form {
      padding-left: 3rem;
      padding-right: 3rem;
      width: 460px;
    }
  }
  @media (max-width: 768px) {
    .auth-form {
      padding-left: 1rem;
      padding-right: 1rem;
      width: calc(100vw - 40px);
    }
  }
  .auth-form {
    position: fixed;
    top:0;
    background: white;
    height: calc(100vh - 40px);
    margin-top: 20px;
    margin-bottom: 20px;
    border-radius: 36px;
    .auth-form-btn-close {
      position: absolute;
      font-size: 2rem;
      right: 26px;
      top: 15px;
      &:hover {
        color: #777777;
        cursor: pointer;
      }
    }
  }
  .blur {
    position: absolute;
    width: 100%;
    min-height: 100vh;
    left: 0;
    top: 0;
    backdrop-filter: blur(3px);
  }
  .navbar-logo {
    min-width: 169px;
  }
  @media (max-width: 768px) {
    .login-wrapper {
      padding-left: 1rem;
      padding-right: 1rem;
    }
  }
  @media (min-width: 768px) {
    .login-wrapper {
      padding-left: 3rem;
      padding-right: 3rem;
    }
  }
  .login-wrapper {
    background: linear-gradient(0deg, rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.35)), url('/images/bg-login.png') no-repeat;
    @media (max-width: 768px) {
      .login-wrapper-header {
        border-bottom: unset;
        .navbar-container li {
          display: block;
          border-bottom: 1px solid rgba(255, 255, 255, .1)
        }
        .navbar-container {
          min-width: 100%;
          background-color: rgba(200, 200, 200, .4);
          margin-top: 8px;
        }
        .btn-signup, .btn-login {
          display: block;
          border-color: transparent;
          background-color: rgba(200, 200, 200, .7);
          border-bottom: 1px solid white;
          width: 100%;
          border-radius: 0;
          height: 46px;
        }
      }
    }
    @media (min-width: 768px) {
      .login-wrapper-header {
        border-bottom: 0.5px solid #ffffff55;
        .navbar-container li {
          display: inline-block;
        }
        .navbar-container {
          min-width: 370px;
        }
        .btn-px {
          padding-left: 2.4rem;
          padding-right: 2.4rem;
          border-radius: 40rem;
        }
        .btn-signup {
          border-color: rgba(255, 255, 255, .1);
          margin-left: 0.5rem;
          margin-right: 0.5rem;
        }
        .btn-login {
          border-color: white;
          margin-left: 0.5rem;
        }
        .btn-signup:hover, .btn-login:hover {
          background-color: #33D1F3;
          border-color: #33D1F3 !important;
        }
      }
    }
    .login-wrapper-header {
      .navbar-container li a {
        font-size: 16px;
        &:hover {
          cursor: pointer;
        }
      }
      .navbar-container li .underline {
        height: 3px;
        background-color: #33D1F3;
        width: 0;
        border-radius: 2px;
        transition: width 0.2s, background-color 0.5s;
        margin: 0 auto;
      }
      .navbar-container li.active-link .underline,
      .navbar-container li:hover .underline {
        width: 17px;
      }
    }
    @media (max-width: 768px) {
      .login-wrapper-content-evolution-title {
        max-width: 360px;
      }
      .login-wrapper-content-evolution-header span {
        font-size: 16px;
        font-weight: 600;
      }
      .login-wrapper-content-evolution-title span {
        font-size: 36px;
        font-weight: 700;
      }
      .btn-get-started {
        margin-top: 3rem;
        padding: 0.4rem 1.6rem;
      }
    }
    @media (min-width: 768px) {
      .login-wrapper-content-evolution-title {
        max-width: 640px;
      }
      .login-wrapper-content-evolution-header span {
        font-size: 18px;
        font-weight: 700;
      }
      .login-wrapper-content-evolution-title span {
        font-size: 72px;
        font-weight: 800;
      }
      .btn-get-started {
        margin-top: 5rem;
        padding: 0.6rem 2.4rem;
      }
    }
    @media (max-width: 768px) {
      #nav-collapse {
        margin-top: 12px;
      }
      .login-wrapper-content-contact-header span {
        font-size: 36px;
        font-weight: 700;
      }
      .login-wrapper-content-contact-title span {
        font-size: 14px;
        font-weight: 600;
      }
      .login-wrapper-content-contact-title {
        max-width: 240px;
      }
      .login-wrapper-content-contact-form input, textarea {
        font-size: 14px;
      }
      .login-wrapper-content-contact-form {
        max-width: 280px;
      }
      .btn-send-email {
        font-size: 14px;
        padding: 0.5rem;
        margin-top: 1rem;
      }
    }
    @media (min-width: 768px) {
      .login-wrapper-content-contact-header span {
        font-size: 72px;
        font-weight: 800;
      }
      .login-wrapper-content-contact-title span {
        font-size: 18px;
        font-weight: 700;
      }
      .login-wrapper-content-contact-title {
        max-width: 500px;
      }
      .login-wrapper-content-contact-form input, textarea {
        font-size: 18px;
      }
      .login-wrapper-content-contact-form {
        max-width: 480px;
      }
      .btn-send-email {
        font-size: 18px;
        padding: 1rem;
        margin-top: 1.5rem;
      }
    }
    .login-wrapper-content {
      flex: 1;
      .login-wrapper-content-evolution {
        .underline {
          width: 40px;
          height: 3px;
          background-color: #33D1F3;
          border-radius: 2px;
          margin: 0 auto;
        }
        .underline-transparent {
          background-color: transparent;
        }
        .login-wrapper-content-evolution-title {
          margin: 0 auto;
        }
        .login-wrapper-content-evolution-title span {
          line-height: 1;
        }
        .btn-get-started {
          background-color: black;
          border-color: black;
          
        }
        .btn-get-started:hover {
          background-color: #33D1F3;
          border-color: #33D1F3 !important;
        }
      }
      .login-wrapper-content-contact {
        .login-wrapper-content-contact-header span {
          line-height: 1.1;
        }
        .login-wrapper-content-contact-title {
          margin: 0 auto;
        }
        .login-wrapper-content-contact-form {
          margin: 0 auto;
        }
        .btn-send-email {
          width: 100%;
          background: linear-gradient(90deg, #FFD8A5 12.31%, #E0B276 53.26%, #BD7123 113.22%);
          backdrop-filter: blur(7.5px);
          color: black;
          border-radius: 10px !important;
          border: none !important;
          font-weight: 700;
          &:hover {
            opacity: .8;
          }
        }
        .btn-send-email-caption {
          flex: 1;
        }
      }
    }
    @media (max-width: 768px) {
      .login-wrapper-footer {
        flex-direction: column;
      }
      .login-wrapper-footer-text {
        flex-direction: column;
      }
      .login-wrapper-footer-text-copyright {
        margin-top: 0.5rem;
        text-align: center;
      }
    }
    @media (min-width: 768px) {
      .login-wrapper-footer {
        flex-direction: row;
      }
      .login-wrapper-footer-text {
        flex-direction: row;
      }
    }
  }
</style>

<script>
import { required, email } from 'vuelidate/lib/validators';
import { LOGIN } from '@/services/store/auth.module';
import TwitterIcon from '@/views/components/icon/TwitterIcon';
import InstagramIcon from '@/views/components/icon/InstagramIcon';
import FacebookIcon from '@/views/components/icon/FacebookIcon';
import LinkedinIcon from '@/views/components/icon/LinkedinIcon';
import GithubIcon from '@/views/components/icon/GithubIcon';
import RightArrowIcon from '@/views/components/icon/RightArrowIcon';
import LoginItem from '@/views/components/auth/login-item';
import ForgotItem from '@/views/components/auth/forgot-item';
import RegisterItem from '@/views/components/auth/register-item';
import axios from 'axios';

export default {
  name: 'login',
  components: {
    TwitterIcon, InstagramIcon, FacebookIcon, LinkedinIcon, GithubIcon, RightArrowIcon, LoginItem, ForgotItem, RegisterItem
  },
  data() {
    return {
      mode: 0, // 0 = login, 1 = forgot, 2 = register, 3 = contact
      email: '',
      password: '',
      repassword: '',
      name: '',
      role: 'advertiser',
      options: [
        {value: 'advertiser', text: 'advertiser'},
        {value: 'publisher', text: 'publisher'}
      ],
      loginShow: false, // check login dialog shown/hidden
      send_firstname: '',
      send_lastname: '',
      send_email: '',
      send_message: '',
    };
  },
  validations: {
    send_firstname: {
      required
    },
    send_lastname: {
      required
    },
    send_email: {
      required,
      email
    },
    send_message: {
      required
    }
  },
  mounted() {
    const mode = this.$route.query.mode;
    if (mode) {
      this.mode = mode
      if (mode == 3)
        this.loginShow = false
      else
        this.loginShow = true
    } else {
      this.mode = 0
      this.loginShow = true
    }
    const type = this.$route.query.type;
    if (type && type === 'register') {
      this.onChangeMode(2);
    }
  },
  methods: {
    onChangeMode(mode) {
      this.name = '';
      this.email = '';
      this.password = '';
      this.repassword = '';
      this.mode = mode;
    },
    onLogin(data) {
      this.email = data.email;
      this.password = data.password;
      this.login(); 
    },
    onForgot(data) {
      this.email = data.email;
      this.forgot();
    },
    onRegister(data) {
      this.name = data.name;
      this.email = data.email;
      this.password = data.password;
      this.role = data.role;
      this.register();
    },
    contact() {

    },
    closeLoginDialog() {
      this.loginShow = false;
    },
    async login() {
      const email = this.email;
      const password = this.password;
      const loader = this.$loading.show();
      try {
        const { data } = await axios.post('/api/login', {
          email,
          password
        });
        if (data.user) {
          this.$store.dispatch(LOGIN, data);
          this.$router.push({ name: data.goto }).catch(() => {});
        } else {
          this.$func.showTextMessage(data.title, data.msg, 'error');
        }
      } catch (err) {
        this.$func.showTextMessage(
          'Unauthorized',
          'Please check your credentials',
          'error'
        );
      } finally {
        loader && loader.hide();
      }
    },
    async forgot() {
      const email = this.email;
      const loader = this.$loading.show();
      axios
        .post('/api/forgot', {
          email
        })
        .then(res => {
          this.$func.showTextMessage('Notice', res.data, 'info');
        })
        .catch(err => {
          this.$func.showTextMessage(
            'Warning',
            'Cannot reset password for this emai',
            'error'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    async register() {
      const email = this.email;
      const password = this.password;
      const role = this.role;
      const name = this.name;
      const loader = this.$loading.show();
      axios
        .post('/api/register', {
          email,
          password,
          name,
          role
        })
        .then(res => {
          if (res && res.status === 200) {
            this.$store.dispatch(LOGIN, res.data);
            this.$router.push({ name: 'statistics' }).catch(() => {});
          } else {
            this.$func.showTextMessage(res.data.title, res.data.msg, res.data.icon);
            this.mode = 0;
          }
        })
        .catch(() => {
          this.$func.showTextMessage(
            'Error',
            'Cannot register your account!',
            'error'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    }
  }
};
</script>
