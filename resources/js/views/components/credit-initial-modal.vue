<template>
  <b-modal
    id="credit-initial-modal"
    hide-header
    hide-footer
    centered
    no-close-on-backdrop
  >
    <div class="credit-modal-body">
      <fa-icon :icon="['fas', 'info-circle']" class="info-circle" />
      <h4 class="mt-3">Enjoy your 7 days FREE Trial</h4>
      <h5 class="mt-3">
        Please activate your 7 Days FREE trial by submitting your payment
        details.
      </h5>
      <div class="text-center mt-4">
        <b-button type="button" variant="primary" @click="handlePayment"
          >Activate Now</b-button
        >
        <b-button
          type="button"
          variant="danger"
          @click="handleSignOut"
          class="ml-2"
          >Sign Out</b-button
        >
      </div>
      <p class="mt-3">
        ( You won't be charged before 7 days, you will only be charged
        <b>$39/Month</b> for the <b>UNLIMITED PLAN</b> after the trial period
        expiry )
      </p>
    </div>
  </b-modal>
</template>

<style lang="scss" scoped>
$base-color: #3490dc;
.credit-modal-body {
  text-align: center;
  .info-circle {
    font-size: 3rem;
    color: $base-color;
  }
  h5 {
    color: #647fa1;
  }
  p {
    font-size: 0.8rem;
  }
}
</style>

<script>
import axios from 'axios';
import { LOGOUT } from '@/services/store/auth.module';
export default {
  props: {
    user: { type: Object, default: {} },
    productId: { type: String, default: '' }
  },
  methods: {
    handlePayment() {
      const Paddle = window.Paddle;

      Paddle.Checkout.open({
        product: this.productId,
        email: this.user.email,
        successCallback: function(data) {
          if (data.checkout.completed) {
            const loader = this.$loading.show();
            axios
              .get('/api/verify-credit')
              .then(res => {
                if (res && res.status === 200) {
                  this.$emit('onChecked', res.data.user);
                }
              })
              .catch(() => {})
              .finally(() => {
                loader && loader.hide();
              });
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'We cannot verify your payment this time, Please try again later!',
              'danger'
            );
          }
        }
      });
    },
    handleSignOut() {
      this.$store.dispatch(LOGOUT);
      this.$router.push({ name: 'login' }).catch(() => {});
    }
  }
};
</script>
