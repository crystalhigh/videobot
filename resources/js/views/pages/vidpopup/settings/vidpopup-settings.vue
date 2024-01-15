<template>
  <div id="vidpopup-flow-settings">
    <div class="flow-headers">
      <span class="active">SEO</span>
      <router-link :to="`/app/vidgen/${id}/settings/advance`" tag="span">
        Advance
      </router-link>
      <router-link :to="`/app/vidgen/${id}/settings/link`" tag="span">
        Link
      </router-link>
      <router-link :to="`/app/vidgen/${id}/settings/widget`" tag="span">
        Widget
      </router-link>
    </div>
    <perfect-scrollbar>
      <div class="content-wrapper">
        <div v-if="currentUser && isEnabled()">
          <h5 class="mt-3">Tracking info</h5>
          <p>
            Add your Facebook Pixel, Instagram Analytics and Tag Manager codes
            to track the progress
          </p>
          <b-form-group
            id="facebook-pixel"
            label="Facebook Pixel ID"
            label-for="facebook-pixel-input"
          >
            <b-form-input
              id="facebook-pixel-input"
              v-model="fb_id"
              type="text"
              class="borderless-text-field"
            ></b-form-input>
          </b-form-group>
          <b-form-group
            id="google-analytics"
            label="Google Analytics ID"
            label-for="google-analytics-input"
          >
            <b-form-input
              id="google-analytics-input"
              v-model="ga_id"
              type="text"
              class="borderless-text-field"
            ></b-form-input>
          </b-form-group>
          <b-form-group
            id="google-tag"
            label="Google Tag Manager"
            label-for="google-tag-input"
          >
            <b-form-input
              id="google-tag-input"
              v-model="gtm"
              type="text"
              class="borderless-text-field"
            ></b-form-input>
          </b-form-group>
          <div class="space-between mt-4">
            <h5 class="mb-0">SEO</h5>
            <b-form-checkbox
              v-model="seo"
              name="seo"
              switch
              size="lg"
            ></b-form-checkbox>
          </div>
          <p class="mt-5">Add Description Branding To your VidGen</p>
          <h5>VidGen Description</h5>
          <div class="description-wrapper">
            <b-form-textarea
              id="description-text"
              v-model="description"
              placeholder="Enter your Text here"
              rows="4"
            ></b-form-textarea>
          </div>
        </div>
        <div
          v-else
          class="h-100 d-flex align-items-center justify-content-center"
        >
          <div class="text-center">
            <h5>
              You cannot use this feature.<br />Upgrade your plan to use this
              feature
            </h5>
            <button
              type="button"
              class="btn btn-primary mt-4"
              @click="upgradePlan"
            >
              Upgrade plan
            </button>
          </div>
        </div>
      </div>
    </perfect-scrollbar>
    <div class="vidpopup-step-button-group mt-3">
      <div>
        <button
          type="button"
          class="btn circle-button btn-save mr-2"
          @click="saveVidpop()"
        >
          <fa-icon :icon="['fas', 'check']" />
        </button>
        <button type="button" disabled class="btn circle-button">
          <fa-icon :icon="['fas', 'arrow-left']" />
        </button>
        <button type="button" @click="handleNext" class="btn circle-button">
          <fa-icon :icon="['fas', 'arrow-right']" />
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" moduled>
#vidpopup-flow-settings {
  height: 100%;
  display: flex;
  flex-direction: column;
  .ps {
    height: calc(100vh - 265px);
  }
  .content-wrapper {
    overflow-y: auto;
    padding: 0px 10px 50px 5px;
    height: 100%;

    .description-wrapper {
      textarea {
        background-color: #f0f1f3;
        padding: 1rem;
        resize: none;
        border-radius: 0.5rem;
        border: none;
      }
    }
  }
  .custom-select {
    max-width: 200px;
  }
}
</style>

<script>
import { VIDPOP, VIDPOP_UPDATED } from '@/services/store/vidpopup.module';
import { mapGetters } from 'vuex';
export default {
  name: 'vidpopup-settings',
  computed: {
    ...mapGetters(['vidpop', 'step', 'currentUser'])
  },
  data() {
    return {
      id: '',
      step_id: '',
      fb_id: '',
      ga_id: '',
      gtm: '',
      seo: false,
      description: '',
      brands: [
        {
          value: 'VidGen',
          text: 'VidGen'
        }
      ]
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
    fb_id: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        social: {
          ...this.vidpop.social,
          fb_id: newVal
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    ga_id: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        social: {
          ...this.vidpop.social,
          ga_id: newVal
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    gtm: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        social: {
          ...this.vidpop.social,
          gtm: newVal
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    seo: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        social: {
          ...this.vidpop.social,
          seo: newVal
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    description: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        social: {
          ...this.vidpop.social,
          description: newVal
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    }
  },
  mounted() {
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.fb_id = this.vidpop.social.fb_id;
    this.ga_id = this.vidpop.social.ga_id;
    this.gtm = this.vidpop.social.gtm;
    this.seo = this.vidpop.social.seo ? true : false;
    this.description = this.vidpop.social.description;
  },
  methods: {
    handleNext() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/settings/advance`
      });
    },
    saveVidpop() {
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
    isEnabled() {
      const level = this.currentUser.origin_level
        ? this.currentUser.origin_level
        : this.currentUser.level;
      return ['OTO2', 'TIER2', 'TIER3'].includes(level);
    },
    upgradePlan() {
      this.$router.push({
        path: '/app/memberships'
      });
    }
  }
};
</script>
