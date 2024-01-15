<template>
  <div id="page-vidpopup-creator">
    <b-container fluid>
      <div class="back">
        <router-link to="/app/vidgen" tag="div" class="return-wrapper">
          <fa-icon :icon="['fas', 'times']" />
        </router-link>
      </div>
      <div class="creator-form">
        <!-- <div class="creator-card">
          <div class="w-100 text-center">
            <img src="/images/template.png" />
          </div>
          <div class="creator-card-label">
            Let Us Power Your Campaign Success
          </div>
          <p class="text-center mt-4 mb-0" style="padding-top: 21px; min-height: 130px;">
            Let Our Team Craft Compelling Videos and Seamlessly Set Up Your Campaign - Just Hit the Button Below!
          </p>
          <div class="btn-creator-choose mt-4" @click="gotoTemplate">
            Contact Us Now
          </div>
        </div> -->
        <div class="creator-card ml-5">
          <div class="w-100 text-center">
            <img src="/images/scratch.png" />
          </div>
          <div class="creator-card-label">
            Start From Scratch
          </div>
          <b-input
            class="form-control mt-4"
            v-model="name"
            placeholder="Name this VidGen"
          />
          <b-input
            type="number"
            class="form-control mt-4"
            v-model="cost"
            placeholder="Cost per lead"
          />
          <b-form-select v-model="advertiser_id" :options="options" class="form-control mt-4">
          </b-form-select>
          <div
            class="btn-creator-choose mt-4"
            @click="submit"
            :disabled="name.length === 0"
          >
            New VidGen from Scratch
          </div>
        </div>
      </div>
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
#page-vidpopup-creator {
  height: 100%;
  display: flex;
  justify-content: center;
  position: relative;
  .container {
    height: 100%;
  }
  .creator-form {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 98%;
    .creator-card {
      min-height: 500px;
      width: 400px;
      padding: 20px;
      background-color: white;
      box-shadow: 0px 5px 40px rgba(0, 0, 0, 0.1);
      img {
        width: 100px;
      }
      .creator-card-label {
        background-color: #1998e4;
        padding: 10px;
        text-transform: uppercase;
        color: white;
        margin-top: 15px;
        text-align: center;
        font-size: 1.2rem;
      }
      .btn-creator-choose {
        width: 100%;
        border-radius: 30px;
        text-transform: uppercase;
        color: white;
        background-color: #333333;
        font-weight: 600;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        &:hover {
          background-color: #5a6268;
        }
      }
    }
  }
  .back {
    text-align: right;
    font-size: 2rem;
    color: #ad4545;
    position: absolute;
    right: 20px;
    top: 20px;
    .return-wrapper {
      cursor: pointer;
      display: flex;
      flex-direction: column;
      align-items: center;
      span {
        font-size: 1rem;
      }
      &:hover {
        filter: brightness(120%);
      }
    }
  }
}
</style>

<script>
import axios from 'axios';
import { VIDPOP } from '@/services/store/vidpopup.module';
import { mapGetters } from 'vuex';

export default {
  name: 'vidpopups',
  computed: {
    ...mapGetters(['vidpop', 'defaultVidpop', 'currentUser'])
  },
  data() {
    return {
      activeSubmit: false,
      name: '',
      cost: '',
      options: [{value: -1, text: 'Select advertiser', disabled: true}],
      advertiser_id: -1,
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
    axios.get('/api/advertiser-list')
    .then(res => {
      if (res && res.status == 200) {
        res.data.map(val => {
          this.options = [...this.options, {
            value: val.id, text: val.name
          }];
        })
      }
    })
    .catch(err => {
      console.log('err =>', err);
    })
  },
  methods: {
    submit() {
      if (this.name == '') {
        this.$func.makeToast(
          this,
          'Warning',
          'Please input the title',
          'danger'
        );
        return;
      }
      if (this.cost == 0 || this.cost == '') {
        this.$func.makeToast(
          this,
          'Warning',
          'Please input the cost',
          'danger'
        );
        return;
      }
      if (this.advertiser_id == -1) {
        this.$func.makeToast(
          this,
          'Warning',
          'Please select advertiser',
          'danger'
        );
        return;
      }
      const loader = this.$loading.show();
      axios
        .post('/api/vidpop', {
          name: this.name,
          cost: this.cost,
          advertiser_id: this.advertiser_id,
          brand: 'VidGen'
        })
        .then(res => {
          if (res.status === 200) {
            loader && loader.hide();
            this.$store.dispatch(VIDPOP, {
              ...this.defaultVidpop,
              id: res.data.id,
              name: res.data.name
            });
            this.$router.push({
              path: `/app/vidgen/${res.data.id}/newStep/manage?index=-1`
            });
          }
        })
        .catch(err => {
          // console.log(err);
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    gotoTemplate() {
      window.location.href = "mailto:support@vid-gen.com";
      // this.$router.push({ name: 'templates' });
    }
  }
};
</script>
