<template>
  <div id="page-membership">
    <b-container fluid>
      <b-row>
        <b-col
          xl="4"
          v-for="(p, idx) in plans"
          :key="`plan-${idx}`"
          class="mt-4"
        >
          <div
            class="plan-card"
            :class="p.level === activeLevel ? 'active' : ''"
          >
            <div>
              <h4>{{ p.title }}</h4>
              <ul>
                <li
                  v-for="(f, idx1) in p.features"
                  :key="`p-${idx}-feature-${idx1}`"
                >
                  {{ f }}
                </li>
              </ul>
            </div>
            <div class="px-3 mt-4" v-if="p.level !== activeLevel">
              <b-button
                type="button"
                variant="primary"
                block
                @click="handleUpdate(p)"
              >
                {{ getButtonText(p) }}
              </b-button>
            </div>
          </div>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
#page-membership {
  padding: 30px;
  height: 100%;
  background-color: white;
  border-radius: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  .plan-card {
    padding: 30px 15px;
    background: #ffffff;
    box-shadow: 0 0 16px 9px rgba(47, 50, 55, 0.05);
    border-radius: 10px;
    text-align: center;
    width: 450px;
    max-width: 100%;
    height: 100%;
    display: flex;
    justify-content: space-between;
    flex-direction: column;
    transition: all 0.2s;
    ul {
      text-align: left;
    }
    &.active {
      border: 2px solid #4db0c2;
    }
    &:hover {
      cursor: pointer;
      box-shadow: 0 0 16px 9px rgba(128, 160, 145, 0.45);
    }
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
import axios from 'axios';
import Swal from 'sweetalert2';
import { LOGOUT } from '@/services/store/auth.module';
export default {
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      plan: 1,
      plans: [],
      activeLevel: 'FET'
    };
  },
  mounted() {
    this.activeLevel = this.currentUser.level.split(',').pop();
    const loader = this.$loading.show();
    axios
      .get('/api/plans')
      .then(res => {
        if (res && res.status === 200) {
          if (this.activeLevel === 'OTO1') {
            this.plans = res.data.plans;
          } else {
            const p = res.data.plans.filter(r => r.level !== 'OTO1');
            this.plans = p;
          }
        } else {
          this.plans = [];
        }
      })
      .catch(() => {
        this.$func.makeToast(this, 'Error', 'Cannot load plans now!', 'danger');
      })
      .finally(() => {
        loader && loader.hide();
      });
  },
  methods: {
    handleUpdate(p) {
      const title = this.getButtonText(p);
      if (title === 'DOWNGRADE') {
        Swal.fire({
          icon: 'warning',
          html: `<h5>Do you want <span class="text-danger">${this.getButtonText(
            p
          )}</span> your plan?</h5>`,
          showCancelButton: true,
          confirmButtonText: this.getButtonText(p)
        }).then(async result => {
          if (result.isConfirmed) {
            this.updateUser(p);
          }
        });
      } else {
        this.updateUser(p);
      }
    },
    getButtonText(p) {
      let levels = ['FET', 'FE', 'OTO1', 'OTO2'];
      if (['TIER1', 'TIER2', 'TIER3'].includes(this.activeLevel)) {
        levels = ['TIER1', 'TIER2', 'TIER3'];
      }
      const idx1 = levels.findIndex(l => l === this.activeLevel);
      const idx2 = levels.findIndex(l => l === p.level);
      return idx1 < idx2 ? 'UPGRADE' : 'DOWNGRADE';
    },
    async paddleCheckout(plan, email) {
      return new Promise((resolve, reject) => {
        const Paddle = window.Paddle;
        Paddle.Checkout.open({
          product: plan.product,
          email,
          successCallback: function(data) {
            resolve(data);
          },
          errorCallback: function(data) {
            reject(data);
          }
        });
      });
    },
    async updateUser(p) {
      if (['TIER1', 'TIER2', 'TIER3'].includes(this.currentUser.level)) {
        // open new page with appsumo
        window.open('https://appsumo.com/account/products/', '_blank');
      } else {
        // unsubscribe the current plan
        const loader = this.$loading.show();
        const res = await axios.get('/api/cancel-subscribe');
        loader && loader.hide();
        if (res && res.status === 200) {
          // user unsubscribed
          if (Number(p.product) !== 0) {
            this.paddleCheckout(p, this.currentUser.email)
              .then(res => {
                // callback for success
                if (res.checkout.completed) {
                  // const innerLoader = this.$loading.show();
                  this.$store.dispatch(LOGOUT);
                  Swal.fire({
                    icon: 'warning',
                    html: `<h5>Your membership is upgrade, you need to login again!!</h5>`,
                    confirmButtonText: 'Logout',
                    allowOutsideClick: false
                  }).then(() => {
                    this.$router.push({ name: 'login' }).catch(() => {});
                  });
                }
              })
              .catch(() => {
                // callback for failed
                this.$func.makeToast(
                  this,
                  'Error',
                  'Cannot unsubscribe your current plan!',
                  'danger'
                );
              });
          }
        } else {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot unsubscribe your current plan!',
            'danger'
          );
          return;
        }
      }
    }
  }
};
</script>
