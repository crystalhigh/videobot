<template>
  <div id="page-vidpopup">
    <b-container>
      <b-row>
        <b-col>
          <h4 class="mt-5 text-center">
            How would you like to create this step?
          </h4>
        </b-col>
      </b-row>
      <b-row class="mt-4">
        <!-- <b-col md="5" class="mt-4">
          <router-link
            :to="`/app/vidgen/${id}/${step_id}/ai?index=${index}`"
            tag="a"
          >
            <span>
              <div class="bot-type">
                <img src="/images/videobots/create/desktop-chat.svg" />
                <h4>AI SpokesPerson</h4>
              </div>
            </span>
          </router-link> -->
          <!-- <span>
            <div class="bot-type">
              <img src="/images/videobots/create/desktop-chat.svg" />
              <h4>AI SpokesPerson</h4>
              <h5>(Coming Soon)</h5>
            </div>
          </span> -->
        <!-- </b-col> -->
        <b-col md="4" class="mt-4">
          <!-- <router-link
            :to="`/app/vidgen/${id}/${step_id}/camera?index=${index}`"
            tag="a"
          >
            <div class="bot-type">
              <img src="/images/videobots/create/camera.svg" />
              <h4>Record Video</h4>
            </div>
          </router-link> -->
        </b-col>
        <b-col md="4" class="mt-4">
          <router-link
            :to="`/app/vidgen/${id}/${step_id}/upload?index=${index}`"
            tag="a"
          >
            <div class="bot-type">
              <img src="/images/icons/upload.svg" />
              <h4>Upload</h4>
            </div>
          </router-link>
        </b-col>
        <b-col md="4" class="mt-4">
          <!-- <router-link
            :to="`/app/vidgen/${id}/${step_id}/screen?index=${index}`"
            tag="a"
          >
            <div class="bot-type">
              <img src="/images/videobots/create/desktop.svg" />
              <h4>Record Screen</h4>
            </div>
          </router-link> -->
        </b-col>
      </b-row>
      <!-- <b-row>
        <b-col md="6" class="mt-4">
          <router-link
            :to="`/app/vidgen/${id}/${step_id}/screen?index=${index}`"
            tag="a"
          >
            <div class="bot-type">
              <img src="/images/videobots/create/desktop.svg" />
              <h4>Record Screen</h4>
            </div>
          </router-link>
        </b-col>
        <b-col md="6" class="mt-4">
          <router-link
            :to="`/app/vidgen/${id}/${step_id}/library?index=${index}`"
            tag="a"
          >
            <div class="bot-type">
              <img src="/images/videobots/create/4rect.svg" />
              <h4>Stock Video</h4>
            </div>
          </router-link>
        </b-col>
      </b-row> -->
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
#page-vidpopup {
  height: 100%;
  background-color: white;
  padding: 30px;
  border-radius: 30px;
  a {
    text-decoration: none;
    &:hover {
      text-decoration: none;
    }
  }
  h4 {
    font-weight: 700;
  }
  .bot-type {
    background-color: #eff7fe;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    border-radius: 15px;
    img {
      width: 70px;
      height: 70px;
    }
    h4 {
      margin-top: 10px;
      color: black;
    }
    h5 {
      color: black;
    }
    height: 250px;
    cursor: pointer;
    &:hover {
      background-color: #1998e4 !important;
      img {
        filter: invert(1);
      }
      h4,
      h5 {
        color: white;
      }
    }
  }
}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import Swal from 'sweetalert2';
export default {
  name: 'vidpopup-edit',
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      id: '',
      step_id: '',
      index: -1
    };
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'agent')) {
      this.$func.makeToast(
        this,
        'Error',
        'You are not allowed to visit this page!',
        'danger'
      );
      if (this.currentUser.role == 'admin')
        this.$router.push({path: '/app/admin-payout'});
      else
        this.$router.push({path: '/app/statistics'});
    }
  },
  mounted() {
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.index = this.$route.query.index;

    const level = this.currentUser.origin_level
        ? this.currentUser.origin_level
        : this.currentUser.level;

    if (
      this.step_id === 'newStep' &&
      !['OTO2', 'TIER2', 'TIER3'].includes(level)
    ) {
      // get step count for vidpop
      axios
        .get(`/api/step-counts/${this.id}`)
        .then(res => {
          if (res && res.status === 200) {
            let max = process.env.MIX_MAX_FET_STEP;
            if (level !== 'FET') {
              max = process.env.MIX_MAX_FE_STEP;
            }
            if (
              (level === 'FET' &&
                res.data.count >= process.env.MIX_MAX_FET_STEP) ||
              (['FE', 'OTO1', 'TIER1'].includes(level) &&
                res.data.count >= process.env.MIX_MAX_FE_STEP)
            ) {
              this.errorCase(
                `You can't create more than ${max} steps! Upgrade your plan!`
              );
            }
          } else {
            this.errorCase(res.data.error);
          }
        })
        .catch(err => {
          this.errorCase('Cannot load VidGen!');
        });
    }
  },
  methods: {
    errorCase(msg) {
      Swal.fire({
        title: 'Error',
        text: msg,
        icon: 'warning',
        showCancelButton: false,
        allowOutsideClick: false,
        showDenyButton: true,
        confirmButtonText: 'Back',
        denyButtonText: 'Upgrade plan!'
      }).then(result => {
        if (result.isConfirmed) {
          this.$router.push({
            path: `/app/vidgen`
          });
        } else if (result.isDenied) {
          this.$router.push({
            path: `/app/memberships`
          });
        }
      });
    }
  }
};
</script>
