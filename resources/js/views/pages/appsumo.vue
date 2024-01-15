<template>
  <div id="page-appsumo">
    <div class="appsumo-form-wrapper">
      <b-form class="p-3 redeem-form" @submit.prevent="submit">
        <div class="w-100 d-flex justify-content-center align-items-center">
          <img src="/images/logo-new.png" class="normal-logo" />
          <fa-icon :icon="['fas', 'heart']" class="heart-icon" />
          <img src="/images/appsumo-logo.png" class="appsumo-logo" />
        </div>
        <b-form-group label="*Email" label-for="email" class="mt-3">
          <b-form-input
            id="email"
            v-model="email"
            type="email"
            disabled
            placeholder="Input your email"
          />
        </b-form-group>
        <b-form-group label="*Fist Name" label-for="firstName">
          <b-form-input
            id="firstName"
            v-model="$v.firstName.$model"
            type="text"
            required
            placeholder="Input your first name"
          />
          <div class="error">
            <span v-if="!$v.firstName.required && submitted"
              >First Name is required</span
            >
          </div>
        </b-form-group>
        <b-form-group label="*Last Name" label-for="firstName">
          <b-form-input
            id="lastName"
            v-model="$v.lastName.$model"
            type="text"
            required
            placeholder="Input your last name"
          />
          <div class="error">
            <span v-if="!$v.firstName.required && submitted"
              >Last Name is required</span
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
        <b-form-group label="*Confirm password" label-for="confirm">
          <b-form-input
            id="confirm"
            v-model="$v.confirm.$model"
            type="password"
            required
            placeholder="Input your password"
          />
          <div class="error">
            <span v-if="!$v.password.required && submitted"
              >Confirm password is required</span
            >
          </div>
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
          >Register</b-button
        >
      </b-form>
    </div>
  </div>
</template>

<style lang="scss" moduled>
#page-appsumo {
  width: 100%;
  height: 100%;
  min-height: 100vh;
  background-color: #eee;
  position: relative;
  .appsumo-form-wrapper {
    width: 500px;
    background-color: white;
    transform: translate(-50%, -50%);
    position: absolute;
    top: 50%;
    left: 50%;
    padding: 30px;
    .normal-logo {
      height: 60px;
    }
    .heart-icon {
      font-size: 30px;
      color: #e63333;
      margin-left: 15px;
    }
    .appsumo-logo {
      height: 25px;
      margin-left: 15px;
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
import { required } from 'vuelidate/lib/validators';
import { LOGIN } from '@/services/store/auth.module';
import axios from 'axios';
export default {
  name: 'appsumo',
  data() {
    return {
      id: '',
      email: '',
      firstName: '',
      lastName: '',
      password: '',
      confirm: '',
      terms: false,
      submitted: false
    };
  },
  validations: {
    password: {
      required
    },
    firstName: {
      required
    },
    lastName: {
      required
    },
    confirm: {
      required
    }
  },
  mounted() {
    if (this.$route.params.id) {
      this.id = this.$route.params.id;
      // load data
      axios
        .get(`/api/appsumo-user/${this.id}`)
        .then(res => {
          if (res && res.status === 200) {
            const { data } = res;
            this.email = data.email;
          } else {
            this.$func.showTextMessage('Error', 'Wrong user profile', 'error');
            this.$router.push({ name: 'error' }).catch(() => {});
          }
        })
        .catch(() => {
          this.$func.showTextMessage('Error', 'Wrong user profile', 'error');
          this.$router.push({ name: 'error' }).catch(() => {});
        });
    } else {
      this.$router.push({ name: 'error' }).catch(() => {});
    }
  },
  methods: {
    submit() {
      this.$v.firstName.$touch();
      this.$v.lastName.$touch();
      this.$v.password.$touch();
      this.$v.confirm.$touch();

      if (
        this.firstName === '' ||
        this.lastname === '' ||
        this.password === '' ||
        this.confirm === ''
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
      const firstName = this.firstName;
      const lastName = this.lastName;
      const password = this.password;
      const id = this.id;
      this.submitted = true;
      axios
        .post('/api/appsumo-profile', {
          id,
          firstName,
          lastName,
          password
        })
        .then(res => {
          if (res && res.status === 200) {
            // redirect to login
            this.$store.dispatch(LOGIN, res.data);
            const { data } = res;
            this.$router.push({ name: data.goto }).catch(() => {});
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
