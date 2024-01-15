<template>
  <div id="vidpopup-flow-advance">
    <div class="flow-headers">
      <router-link :to="`/app/vidgen/${id}/settings/settings`" tag="span">
        SEO
      </router-link>
      <span class="active">Advance</span>
      <router-link :to="`/app/vidgen/${id}/settings/link`" tag="span">
        Link
      </router-link>
      <router-link :to="`/app/vidgen/${id}/settings/widget`" tag="span">
        Widget
      </router-link>
    </div>
    <div class="content-wrapper">
      <b-form-group id="vidpop-name" label="Name" label-for="vidpop-name-input">
        <b-form-input
          id="vidpop-name-input"
          v-model="vidpopName"
          type="text"
          class="borderless-text-field"
        ></b-form-input>
      </b-form-group>
      <div class="space-between mt-5">
        <h5 class="mb-0">Show Controls</h5>
        <b-form-checkbox
          v-model="showControl"
          name="show-control"
          switch
          size="lg"
        ></b-form-checkbox>
      </div>
      <hr class="w-100" />
      <div class="space-between">
        <h5 class="mb-0">Pause / Auto-Play</h5>
        <b-form-checkbox
          v-model="autoPlay"
          name="auto-play"
          switch
          size="lg"
        ></b-form-checkbox>
      </div>
      <hr class="w-100" />
      <div class="space-between">
        <h5 class="mb-0">Oversize Video Buttons</h5>
        <b-form-checkbox
          v-model="oversize"
          name="oversize"
          switch
          size="lg"
        ></b-form-checkbox>
      </div>
      <hr class="w-100" />
      <div class="space-between">
        <h5 class="mb-0">Show VidGen Title</h5>
        <b-form-checkbox
          v-model="showTitle"
          name="show-title"
          switch
          size="lg"
        ></b-form-checkbox>
      </div>
      <hr class="w-100" />
      <div class="space-between mt-2">
        <span class="left-label">Border Radius</span>
        <input class="rounded-input" type="number" v-model="buttonRadius" />
      </div>
      <hr class="w-100" />
      <div class="space-between">
        <h5 class="mb-0 mr-2">Integrations</h5>
        <b-form-select
          v-model="integration"
          :options="integrations"
          @change="updateList"
        ></b-form-select>
      </div>
      <div
        class="space-between mt-2"
        v-if="
          !['csv', 'zapier', 'pabbly'].includes(integration) && lists.length > 0
        "
      >
        <h5 class="mb-0 mr-2">List</h5>
        <b-form-select v-model="arlist" :options="lists" />
      </div>
      <div v-if="['zapier', 'pabbly'].includes(integration)" class="mt-2">
        <b-form-input disabled :value="getWebhook(integration)" />
      </div>
      <hr class="w-100" />
      <div
        class="space-between"
        v-if="currentUser && currentUser.template_admin"
      >
        <h5 class="mb-0">Is template</h5>
        <b-form-checkbox
          v-model="isTemplate"
          name="show-title"
          switch
          size="lg"
        ></b-form-checkbox>
      </div>
      <div v-if="checkLevel()">
        <hr class="w-100" />
        <div class="mt-4 space-between">
          <h5 class="mb-0">Branding</h5>
          <b-form-select v-model="brand" :options="brands"></b-form-select>
        </div>
      </div>
    </div>
    <div class="vidpopup-step-button-group">
      <div>
        <button
          type="button"
          class="btn circle-button btn-save mr-2"
          @click="saveStep()"
        >
          <fa-icon :icon="['fas', 'check']" />
        </button>
        <button type="button" @click="handleBack" class="btn circle-button">
          <fa-icon :icon="['fas', 'arrow-left']" />
        </button>
        <button type="button" @click="handleNext" class="btn circle-button">
          <fa-icon :icon="['fas', 'arrow-right']" />
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
#vidpopup-flow-advance {
  height: 100%;
  display: flex;
  flex-direction: column;
  .ps {
    height: 550px;
  }
  .content-wrapper {
    overflow-y: auto;
    padding: 0px 10px 50px 5px;
    .selected-pane {
      width: 35px;
      height: 35px;
      border-radius: 100%;
      background: #1998e4;
    }
    .left-label {
      font-size: 1.1rem;
      font-weight: 700;
    }
    .rounded-input {
      width: 50px;
      padding: 7px 10px;
      &::-webkit-outer-spin-button,
      &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }
      -moz-appearance: textfield;
      border-radius: 10px;
      border: 2px solid #1998e4;
      outline: none;
      text-align: right;
    }
    .fontStyleBtn {
      width: 100%;
      -webkit-box-pack: justify;
      justify-content: space-between;
      display: flex;
      -webkit-box-align: center;
      align-items: center;
      background: transparent;
      height: 41px;
      padding: 0px 20px 0px 20px;
      border: none;
      font-weight: 500;
      font-size: 16px;
      outline: none;
      transition: all 0.1s ease-in-out 0s;
    }
    .current {
      background: #f0f0f0;
      border-radius: 25px;
    }
    .custom-select {
      max-width: 200px;
    }
  }
}
</style>

<script>
import EditBgIcon from '@/views/components/icon/EditBgIcon.vue';
import { VIDPOP, VIDPOP_UPDATED } from '@/services/store/vidpopup.module';
import { mapGetters } from 'vuex';
import axios from 'axios';
export default {
  components: { EditBgIcon },
  name: 'vidpopup-advance',
  computed: {
    ...mapGetters(['vidpop', 'step', 'currentUser', 'stepUpdated'])
  },
  data() {
    return {
      id: '',
      step_id: '',
      vidpopName: '',
      showControl: false,
      autoPlay: false,
      oversize: false,
      showTitle: false,
      buttonRadius: 30,
      replyNoti: false,
      color1: '#FFFFFF',
      color2: '#3490DC',
      color3: '#E4EAFA',
      isTemplate: false,
      integration: '',
      integrations: [],
      lists: [],
      arlist: '',
      brands: [
        {
          value: 'vid-gen.com',
          text: 'vid-gen.com'
        }
      ],
      brand: 'vid-gen.com'
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
  watch: {
    showControl: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        advanced: {
          ...this.vidpop.advanced,
          show_control: newVal ? 1 : 0
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    autoPlay: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        advanced: {
          ...this.vidpop.advanced,
          auto_play: newVal ? 1 : 0
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    oversize: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        advanced: {
          ...this.vidpop.advanced,
          oversize: newVal ? 1 : 0
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    showTitle: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        advanced: {
          ...this.vidpop.advanced,
          show_title: newVal ? 1 : 0
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    buttonRadius: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        advanced: {
          ...this.vidpop.advanced,
          button_radius: newVal
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    replyNoti: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        advanced: {
          ...this.vidpop.advanced,
          reply_noti: newVal ? 1 : 0
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    vidpopName: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        name: newVal
      });
    },
    integration: async function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        integration: newVal
      });
    },
    arlist: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        arlist: newVal
      });
    },
    isTemplate: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        is_template: this.isTemplate ? 1 : 0
      });
    },
    brand: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        brand: newVal
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    }
  },
  async mounted() {
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.integrations.push({
      value: 'csv',
      text: 'CSV'
    });
    const loader = this.$loading.show();
    try {
      const [integrations, brands] = await Promise.all([
        axios.get('/api/integrations'),
        axios.get('/api/brands')
      ]);
      if (integrations && integrations.status === 200) {
        integrations.data.forEach(element => {
          this.integrations.push({
            value: element.type,
            text: this.$func.capitalized(element.type),
            api: element.api
          });
        });
      }

      if (brands && brands.status === 200) {
        brands.data.forEach(element => {
          this.brands.push({
            value: element.id,
            text: element.name
          });
          if (this.vidpop.brand === element.id) {
            this.brand = element.id;
          }
        });
      }
    } catch {
      this.$func.makeToast(
        this,
        'Error',
        'Cannot load AR from server!',
        'error'
      );
    }
    await this.updateInfo();
    loader && loader.hide();
  },
  methods: {
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/settings/settings`
      });
    },
    handleNext() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/settings/link`
      });
    },
    saveStep() {
      const loader = this.$loading.show();
      axios
        .post('/api/vidpop', this.vidpop)
        .then(res => {
          if (res && res.status === 200) {
            this.$func.makeToast(
              this,
              'Notice',
              'Current step is saved!',
              'success'
            );
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot save the current step!',
              'danger'
            );
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot save the current step!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    async updateInfo() {
      this.vidpopName = this.vidpop.name;
      this.showControl = this.vidpop.advanced.show_control === 1 ? true : false;
      this.autoPlay = this.vidpop.advanced.auto_play === 1 ? true : false;
      this.oversize = this.vidpop.advanced.oversize === 1 ? true : false;
      this.showTitle = this.vidpop.advanced.show_title === 1 ? true : false;
      this.replyNoti = this.vidpop.advanced.reply_noti === 1 ? true : false;
      this.buttonRadius = this.vidpop.advanced.button_radius;
      this.isTemplate = this.vidpop.is_template === 1 ? true : false;
      this.integration = this.vidpop.integration || 'csv';
      this.arlist = this.vidpop.arlist;

      if (!['csv', 'zapier', 'pabbly'].includes(this.integration)) {
        this.lists = await this.loadARList();
      }
    },
    async loadARList() {
      const res = await axios.get(
        `/api/integration-list?type=${this.integration}`
      );
      if (res && res.status === 200) {
        const l = [];
        res.data.forEach(element => {
          l.push({
            value: element.id,
            text: element.name
          });
        });
        return l;
      } else {
        const label = this.$func.capitalized(this.integration);
        this.$func.makeToast(
          this,
          'Error',
          `Cannot load AR list for ${label}`,
          'danger'
        );
        this.integration = 'csv';
        return [];
      }
    },
    async updateList() {
      if (this.integration === 'csv') {
        return;
      }
      if (['zapier', 'pabbly'].includes(this.integration)) {
        // load zapier
      } else {
        const loader = this.$loading.show();
        this.arlist = '';
        this.lists = await this.loadARList();
        loader && loader.hide();
      }
    },

    isDisabled() {},

    getWebhook(i) {
      const l = this.integrations.find(v => v.value === i);
      return l ? l.api : '';
    },

    checkLevel() {
      const level = this.currentUser.origin_level
        ? this.currentUser.origin_level
        : this.currentUser.level;
      return ['OTO2', 'TIER2', 'TIER3'].includes(level);
    }
  }
};
</script>
