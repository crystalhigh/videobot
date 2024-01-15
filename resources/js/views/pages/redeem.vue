<template>
  <div id="page-redeem">
    <div class="redeem-form-wrapper">
      <b-form class="p-3 redeem-form" @submit.prevent="submit">
        <div class="w-100 d-flex justify-content-center">
          <img src="/images/logo-new.png" class="logo" />
        </div>
        <b-form-group label="*Name" label-for="name">
          <b-form-input
            id="name"
            v-model="$v.name.$model"
            type="text"
            required
            placeholder="Input your name"
          />
          <div class="error">
            <span v-if="!$v.name.required && submitted">Name is required</span>
          </div>
        </b-form-group>
        <b-form-group label="*Email" label-for="email">
          <b-form-input
            id="email"
            v-model="$v.email.$model"
            type="email"
            required
            placeholder="Input your email"
          />
          <div class="error">
            <span v-if="!$v.email.required && submitted"
              >Email is required</span
            >
          </div>
        </b-form-group>
        <b-form-group label="*Password" label-for="password">
          <b-form-input
            id="password"
            v-model="$v.password.$model"
            type="password"
            required
            placeholder="Input your password"
          />
          <div class="error">
            <span v-if="!$v.password.required && submitted"
              >Password is required</span
            >
          </div>
        </b-form-group>
        <b-form-group label="*Coupon Code" label-for="coupon">
          <b-form-input
            id="coupon"
            v-model="coupon"
            type="text"
            required
            placeholder="Input your coupon"
          />
        </b-form-group>
        <b-form-group>
          <b-form-checkbox v-model="terms" size="lg">
            <span class="agree-terms">
              I agree the
              <a href="https://vid-gen.com/terms" target="_blank"
                >Terms of use</a
              >
              and the
              <a href="https://vid-gen.com/privacy" target="_blank"
                >Privacy policy</a
              >
            </span>
          </b-form-checkbox>
        </b-form-group>
        <b-button variant="primary" block type="submit" :disabled="submitted"
          >Redeem Coupon Code</b-button
        >
      </b-form>
    </div>
  </div>
</template>

<style lang="scss" moduled>
#page-redeem {
  width: 100%;
  height: 100%;
  min-height: 100vh;
  background-color: #eee;
  position: relative;
  .redeem-form-wrapper {
    width: 500px;
    background-color: white;
    transform: translate(-50%, -50%);
    position: absolute;
    top: 50%;
    left: 50%;
    padding: 30px;
    img {
      height: 60px;
    }
    label {
      color: black;
      font-weight: 700;
      margin-bottom: 5px;
    }
    .agree-terms {
      font-size: 1rem;
    }
  }
}
</style>

<script>
import { required, email } from 'vuelidate/lib/validators';
import { LOGIN } from '@/services/store/auth.module';
import axios from 'axios';
export default {
  name: 'redeem',
  data() {
    return {
      email: '',
      name: '',
      password: '',
      coupon: '',
      terms: false,
      submitted: false
    };
  },
  validations: {
    password: {
      required
    },
    email: {
      required,
      email
    },
    name: {
      required
    },
    coupon: {
      required
    }
  },
  mounted() {
    if (this.$route.params.coupon) {
      this.coupon = this.$route.params.coupon;
    }
  },
  methods: {
    submit() {
      this.$v.name.$touch();
      this.$v.email.$touch();
      this.$v.password.$touch();
      this.$v.coupon.$touch();

      if (
        this.name === '' ||
        this.password === '' ||
        this.email === '' ||
        this.coupon === ''
      ) {
        return;
      }
      if (!this.terms) {
        this.$func.showTextMessage(
          'Error',
          'You have to agree terms and policy!',
          'error'
        );
        return;
      }
      const loader = this.$loading.show();
      const name = this.name;
      const email = this.email;
      const password = this.password;
      const coupon = this.coupon;
      this.submitted = true;
      axios
        .post('/api/redeem', {
          name,
          email,
          password,
          coupon
        })
        .then(res => {
          if (res && res.status === 200) {
            // this.$func.showTextMessage(
            //   'Notice',
            //   'User is successfully created!',
            //   'success'
            // );
            // redirect to login
            this.$store.dispatch(LOGIN, res.data);
            this.$router.push({ name: 'statistics' }).catch(() => {});
          } else {
            this.$func.showTextMessage('Error', res.data.error, 'error');
            this.submitted = false;
          }
        })
        .catch(err => {
          this.$func.showTextMessage('Error', 'Coupon is not valid!', 'error');
          this.submitted = false;
        })
        .finally(() => {
          loader && loader.hide();
        });
    }
  }
};
</script>
