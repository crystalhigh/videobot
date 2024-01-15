<template>
  <div class="root-container">
    <div class="limit-badge" v-if="impression > 0 && ((limit - impression) <= 1) && limit !== -1">
      Your Monthly limits has been reached!
      <span @click="gotoPlan">Click here to upgrade your account</span>
    </div>
    <div class="main-container">
      <left-side-bar />
      <div class="main-page">
        <transition name="page" mode="out-in">
          <div class="h-100" v-if="activated">
            <router-view></router-view>
          </div>
        </transition>
      </div>
    </div>
    <credit-initial-modal
      :user="currentUser"
      :productId="productId"
      @onChecked="handleSuccess"
    />
  </div>
</template>
<style lang="scss" scoped>
.root-container {
  .limit-badge {
    width: 100%;
    background-color: #1998e4;
    text-align: center;
    color: white;
    padding: 10px;
    font-weight: 700;
    span {
      text-decoration: underline;
      cursor: pointer;
      &:hover {
        opacity: 0.75;
      }
    }
  }
  .main-container {
    display: flex;
    .main-page {
      background-color: #eff7fe;
      border-top-left-radius: 30px;
      border-bottom-left-radius: 30px;
      min-height: 100%;
      width: 100%;
      padding: 20px;
      @media (max-width: 1400px) {
        padding: 10px;
      }
      @media (max-width: 768px) {
        padding: 0px;
      }
    }
  }
}
</style>
<script>
import { mapGetters } from 'vuex';
import axios from 'axios';
import LeftSideBar from '@/views/layout/left-side-bar.vue';
import CreditInitialModal from '@/views/components/credit-initial-modal.vue';
import { UPDATE_AUTH } from '@/services/store/auth.module';
export default {
  name: 'layout',
  computed: {
    ...mapGetters(['currentUser'])
  },
  components: {
    LeftSideBar,
    CreditInitialModal
  },
  data() {
    return {
      activated: true,
      productId: '',
      impression: 0,
      limit: 0
    };
  },
  mounted() {

    const loader = this.$loading.show();
    axios
      .get(`/api/monthly-impressions`)
      .then(res => {
        if (res && res.status === 200) {
          this.limit = Number(res.data.limit);
          this.impression = Number(res.data.count);
        } else {
          this.limit = 0;
          this.impression = 0;
        }
      })
      .catch(() => {
        this.limit = 0;
        this.impression = 0;
      })
      .finally(() => {
        loader && loader.hide();
      });
  },
  methods: {
    handleSuccess(user) {
      this.$store.dispatch(UPDATE_AUTH, user);
      this.$router.push({
        path: `/app/home`
      });
    },
    gotoPlan() {
      this.$router.push({
        name: 'memberships'
      });
    }
  }
};
</script>
