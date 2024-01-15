<template>
  <div id="step-flow-overlay">
    <div class="flow-headers">
      <span class="active">Text</span>
      <router-link
        :to="`/app/vidgen/${id}/${step_id}/edit/answer`"
        tag="span"
      >
        Answer
      </router-link>
      <router-link :to="`/app/vidgen/${id}/${step_id}/edit/video`" tag="span">
        Video
      </router-link>
      <router-link :to="`/app/vidgen/${id}/${step_id}/edit/logic`" tag="span">
        Funnel
      </router-link>
    </div>
    <div class="content-wrapper">
      <b-form-textarea
        id="overlay-text"
        v-model="overlayText"
        placeholder="Enter your Text here"
        rows="4"
      ></b-form-textarea>
      <!-- <h5 class="learn-more">
        Learn more about sending name. <a href="#">click</a>
      </h5> -->
      <div class="space-between mt-4">
        <h5>Fit videos</h5>
        <b-form-checkbox
          v-model="fitVideo"
          name="fit-video"
          @change="handleFit"
          switch
          size="lg"
          class="cursor-pointer"
        ></b-form-checkbox>
      </div>
      <div class="space-between mt-4">
        <h5>Position</h5>
        <div class="text-position-wrapper">
          <div
            class="position-wrapper"
            v-for="(p, idx) in positions"
            :key="`postion-${idx}`"
          >
            <button
              type="button"
              class="btn btn-position"
              @click="handlePosition(p)"
              :class="p === position ? 'active' : ''"
            >
              {{ p }}
            </button>
          </div>
        </div>
      </div>
      <div class="overlay-text-size mt-4">
        <h5 class="mr-3 mb-0">Text Size</h5>
        <b-form-select
          v-model="overlaySize"
          :options="overlaySizes"
        ></b-form-select>
      </div>
      <div class="overlay-text-size mt-4">
        <h5 class="mr-3 mb-0">Text Color</h5>
        <input
          type="color"
          class="overlay-color"
          ref="colorRef"
          v-model="overlayTextColor"
        />
      </div>
      <div class="overlay-text-size mt-4">
        <input
          type="checkbox"
          v-model="isBackColor"
          @change="handleBackVisible"
        />
        <h5 class="mr-3 mb-0">Background Color</h5>
        <input
          type="color"
          class="overlay-color"
          ref="colorRef"
          v-model="overlayBackColor"
        />
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
        <button type="button" class="btn circle-button" disabled>
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
input[type='color'] {
  -webkit-appearance: none;
  border: none;
  width: 32px;
  height: 32px;
  width: 30px;
  height: 30px;
  border-radius: 7px;
  display: flex;
  cursor: pointer;
}
input[type='checkbox'] {
  width: 20px;
  height: 20px;
  margin-right: 10px;
}
input[type='color']::-webkit-color-swatch-wrapper {
  padding: 0;
}
#step-flow-overlay {
  height: 100%;
  display: flex;
  flex-direction: column;
  .content-wrapper {
    height: calc(100% - 50px);
    overflow-y: auto;
    padding-bottom: 50px;
    .overlay-text {
      font-weight: 700;
    }
    textarea {
      margin-top: 20px;
      background-color: #f0f1f3;
      padding: 1rem;
      resize: none;
      border-radius: 0.5rem;
      border: none;
      width: 98%;
      margin-left: 1%;
    }
    .learn-more {
      color: #c4c4c4;
      margin-top: 20px;
      a {
        color: black;
        font-weight: 700;
        text-decoration: underline;
      }
    }
    .overlay-text-size {
      display: flex;
      align-items: center;
    }
    .text-position-wrapper {
      display: flex;
      flex-wrap: wrap;
      padding: 10px;
      .position-wrapper {
        width: 33.33%;
        padding: 5px;
        .btn-position {
          width: 100%;
          background-color: #eee;
          font-weight: 700;
          &:hover {
            filter: brightness(90%);
          }
          &.active {
            background-color: #1998e4;
            color: white;
          }
        }
      }
    }
  }
}
</style>

<script>
import { STEP, STEP_UPDATED } from '@/services/store/vidpopup.module';
import { mapGetters } from 'vuex';
export default {
  name: 'step-overlay',
  computed: {
    ...mapGetters(['vidpop', 'step', 'currentUser'])
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
    overlayText: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        overlay: {
          ...this.step.overlay,
          text: newVal
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    overlaySize: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        overlay: {
          ...this.step.overlay,
          size: newVal
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    overlayTextColor: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        overlay: {
          ...this.step.overlay,
          color: newVal
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    overlayBackColor: function(newVal, oldVal) {
      if (this.isBackColor) {
        this.$store.dispatch(STEP, {
          ...this.step,
          overlay: {
            ...this.step.overlay,
            bg_color: newVal
          }
        });
        this.$store.dispatch(STEP_UPDATED, true);
      }
    },
    step: function(newVal, oldVal) {
      this.overlayText = newVal.overlay.text;
      this.fitVideo = newVal.fit_video ? true : false;
      this.overlayTextColor = newVal.overlay.color;
      this.overlaySize = newVal.overlay.size;
      this.position = newVal.overlay.position;
      this.overlayBackColor = newVal.overlay.bg_color || '#000000';
      this.isBackColor = newVal.overlay.bg_color ? true : false;
    }
  },
  data() {
    return {
      fitVideo: false,
      overlayText: '',
      overlaySize: '1.5rem',
      overlaySizes: [
        { value: '1.5rem', text: 'Extra Small' },
        { value: '2rem', text: 'Small' },
        { value: '2.5rem', text: 'Medium' },
        { value: '3rem', text: 'Large' },
        { value: '4rem', text: 'Extra Large' }
      ],
      id: '',
      step_id: '',
      overlayTextColor: '#ffffff',
      overlayBackColor: '#000000',
      isBackColor: false,
      positions: ['TL', 'TC', 'TR', 'CL', 'CC', 'CR', 'BL', 'BC', 'BR'],
      position: 'TC'
    };
  },
  mounted() {
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.overlayText = this.step.overlay.text;
    this.fitVideo = this.step.fit_video ? true : false;
    this.overlayTextColor = this.step.overlay.color;
    this.overlaySize = this.step.overlay.size;
    this.position = this.step.overlay.position || 'TC';
    this.overlayBackColor = this.step.overlay.bg_color || '#000000';
    this.isBackColor = this.step.overlay.bg_color ? true : false;
  },
  methods: {
    handleNext() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/edit/answer`
      });
    },
    handleFit(e) {
      this.$store.dispatch(STEP, {
        ...this.step,
        fit_video: e
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    handleBackVisible(e) {
      if (e.target.checked) {
        this.$store.dispatch(STEP, {
          ...this.step,
          overlay: {
            ...this.step.overlay,
            bg_color: this.overlayBackColor
          }
        });
      } else {
        this.$store.dispatch(STEP, {
          ...this.step,
          overlay: {
            ...this.step.overlay,
            bg_color: ''
          }
        });
      }
      this.$store.dispatch(STEP_UPDATED, true);
    },
    handlePosition(p) {
      if (this.position !== p) {
        this.position = p;
        this.$store.dispatch(STEP, {
          ...this.step,
          overlay: {
            ...this.step.overlay,
            position: p
          }
        });
        this.$store.dispatch(STEP_UPDATED, true);
      }
    },
    saveStep() {
      const validCheck = this.$func.validateStep(this.step);
      if (!validCheck.state) {
        this.$func.makeToast(this, 'Error', validCheck.msg, 'danger');
        return;
      }
      const loader = this.$loading.show();
      axios
        .post('/api/update-step', this.step)
        .then(res => {
          if (res && res.status === 200) {
            this.$func.makeToast(
              this,
              'Notice',
              'Current step is saved!',
              'success'
            );
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'danger');
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
    }
  }
};
</script>
