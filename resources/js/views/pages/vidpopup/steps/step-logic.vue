<template>
  <div id="step-flow-logic">
    <div class="flow-headers">
      <router-link
        :to="`/app/vidgen/${id}/${stepId}/edit/overlay`"
        tag="span"
      >
        Text
      </router-link>
      <router-link :to="`/app/vidgen/${id}/${stepId}/edit/answer`" tag="span">
        Answer
      </router-link>
      <router-link :to="`/app/vidgen/${id}/${stepId}/edit/video`" tag="span">
        Video
      </router-link>
      <span class="active">Funnel</span>
    </div>
    <div class="content-wrapper">
      <b-dropdown
        block
        menu-class="w-100"
        no-caret
        toggle-class="answer-toggle"
        :key="`choice-item-${c_idx}`"
        v-for="(choice, c_idx) in choices"
      >
        <template #button-content>
          <div class="logic-item">
            <div class="text">
              if
              <span class="current ml-2 mr-2">{{ getLetter(c_idx) }}</span>
              Jump To
              <fa-icon :icon="['fas', 'long-arrow-alt-right']" class="ml-2" />
            </div>
            <button class="destination" v-if="choice !== 'end'">
              <span class="index-badge">{{ getStepIndex(choice) }}</span>
              <!-- <div class="icon">
                <img :src="getThumbUrl(choice)" v-if="choice !== ''" />
              </div> -->
            </button>
            <button class="destination" v-if="choice === 'end'">
              <span class="index-badge end-badge">END</span>
              <!-- <div class="icon">
                <end-screen-icon color="#fff" />
              </div> -->
            </button>
          </div>
        </template>
        <div class="destination-form">
          <b-dropdown-item
            class="logic-list-item"
            v-for="(step, s_idx) in steps"
            :key="`step-item-${s_idx}`"
            @click="handleChooseStep(c_idx, step.id)"
          >
            <span class="index-badge">{{ step.index }}</span>
            <img :src="getUrl(step.thumb_url)" v-if="step.thumb_url" />
            {{ step.overlay.text }}
          </b-dropdown-item>
          <b-dropdown-item
            class="logic-list-item"
            @click="handleChooseStep(c_idx, 'end')"
          >
            <div class="bye-icon">
              <end-screen-icon />
            </div>
            End Screen
          </b-dropdown-item>

          <b-dropdown-divider></b-dropdown-divider>
        </div>
      </b-dropdown>
    </div>
    <button class="btn bt-large btn-primary w-100 mb-3" @click="gotoEndScreen">
      Edit End Screen
    </button>
    <button
      class="btn bt-large btn-primary w-100 mb-3"
      @click="gotoSetting"
      v-if="saved"
    >
      Go to Setting
    </button>
    <button class="btn btn-large btn-success w-100" @click="saveStep()">
      DONE
    </button>
  </div>
</template>

<style lang="scss" moduled>
#step-flow-logic {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  .content-wrapper {
    height: calc(100% - 50px);
    overflow-y: auto;
    padding-bottom: 50px;
    overflow-x: hidden;
    .destination-form {
      .hint {
        color: rgb(128, 128, 128);
        margin-top: -6px;
        font-size: 12px;
        margin-left: 40px;
      }

      .redirect-form {
        display: flex;
        padding: 10px 0px 8px 6px;
        -webkit-box-align: center;
        align-items: center;
        .indicator {
          display: flex;
          -webkit-box-align: center;
          align-items: center;
          -webkit-box-pack: center;
          justify-content: center;
          color: rgb(255, 255, 255);
          background: rgb(234, 234, 234);
          width: 24px;
          height: 24px;
          border-radius: 40px;
          min-width: 16px;
          max-width: 28px;
          font-size: 14px;
          font-weight: 500;
        }
        .indicator-wrapper {
          display: inline-flex;
          -webkit-box-align: center;
          align-items: center;
          flex-shrink: 0;
          margin-right: 6px;
        }
        .redirect-url-wrapper {
          color: black;
          flex: 1 1 0%;
          position: relative;
          display: flex;
          -webkit-box-align: center;
          align-items: center;
          -webkit-box-pack: justify;
          justify-content: space-between;
          .btn-box {
            position: absolute;
            bottom: -26px;
            right: -2px;
            transition: all 0.2s ease-in-out 0s;
            .app-button {
              background-color: rgb(54, 207, 113);
              pointer-events: auto;
              color: rgb(255, 255, 255);
              opacity: 1;
              background: rgb(54, 207, 113);
              font-family: 'Favorit Pro', favorit-pro;
              color: rgb(255, 255, 255);
              font-size: 12px;
              padding: 5px 8px;
              border-radius: 5px;
              font-weight: 700;
              border: none;
              z-index: 999;
              transition: all 0.1s ease-in-out 0s;
              outline: none;
            }
          }
          .border-none {
            border: none !important;
            height: 20px;
            box-shadow: none;
            padding-left: 0px;
          }
        }
      }
      .logic-list-item .dropdown-item {
        padding: 0.5rem 1.5rem;
        display: flex;
        align-items: center;
        .index-badge {
          width: 30px;
          height: 30px;
          border-radius: 30px;
          margin-right: 10px;
          background-color: #1998e4;
          color: white;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        img {
          width: 30px;
          height: 30px;
          border-radius: 50%;
          margin-right: 20px;
        }
        .bye-icon {
          width: 30px;
          height: 30px;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #f0f0f0;
          border-radius: 50%;
          margin-right: 20px;
        }
      }
    }
    .logic-item {
      border-radius: 8px;
      display: flex;
      -webkit-box-pack: justify;
      justify-content: space-between;
      -webkit-box-align: center;
      align-items: center;
      padding: 0px 12px;
      padding-right: 0px;
      margin-top: 8px;
      color: rgb(17, 17, 17);
      font-weight: 500;
      cursor: pointer;
      transition: all 0.1s ease-out 0s;
      background-color: #f0f1f333;
      height: 52px;
      font-size: 14px;
      width: 100%;
      .text {
        display: flex;
        margin-top: -3px;
        max-width: calc(100% - 48px);
        align-items: center;
        font-weight: 600;
        background-color: #f0f1f3;
        border-radius: 30px;
        padding: 10px;
        .current {
          border-radius: 100%;
          background: #000;
          color: #fff;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 24px;
          height: 24px;
          font-size: 15px;
        }
      }
      .destination {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        background: transparent;
        border: none;
        font-size: inherit;
        outline: none;
        color: #1998e4;
        padding: 0px;
        position: relative;
        .index-badge {
          left: 0;
          font-size: 0.9rem;
          background-color: #1998e4;
          color: white;
          z-index: 2;
          padding: 5px;
          border-radius: 30px;
          width: 30px;
          height: 30px;
          display: flex;
          align-items: center;
          justify-content: center;
          &.end-badge {
            width: 60px;
            font-size: 0.7rem;
            font-weight: 700;
          }
        }
        .icon {
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          background: #1998e4;
          border-radius: 15px;
          min-width: 16px;
          max-width: 100%;
          font-size: 12px;
          font-weight: 500;
          line-height: 140%;
          width: 100%;
          height: 100%;
          img {
            width: 100%;
            height: 100%;
            border-radius: 15px;
          }
        }
      }
    }
  }
  .dropdown-toggle:focus {
    box-shadow: none;
  }
}
</style>

<script>
import EndScreenIcon from '@/views/components/icon/EndScreenIcon.vue';
import RedirectIcon from '@/views/components/icon/RedirectIcon.vue';
import { STEP } from '@/services/store/vidpopup.module';
import { mapGetters } from 'vuex';
export default {
  components: { EndScreenIcon, RedirectIcon },
  name: 'step-logic',
  computed: {
    ...mapGetters(['vidpop', 'step', 'currentUser'])
  },
  data() {
    return {
      id: '',
      stepId: '',
      choices: [],
      steps: [],
      saved: false,
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
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
    this.awsUrl = process.env.MIX_CDN_URL;
    this.id = this.$route.params.id;
    this.stepId = this.$route.params.step;
    this.choices = [];
    if (this.step.next_step && this.step.next_step !== '') {
      this.choices = this.step.next_step.split(',');
    } else if (this.step.answer.type === 1) {
      this.choices = Array(this.step.answer.content.length).fill('end');
    } else {
      this.choices.push('end');
    }
    this.loadSteps();
  },
  methods: {
    getThumbUrl(stepId) {
      let step = this.steps.find(x => x.id == stepId);
      if (step) {
        return this.getUrl(step.thumb_url);
      } else {
        return '';
      }
    },
    getStepIndex(stepId) {
      let step = this.steps.find(x => x.id === stepId);
      if (step) {
        return step.index;
      } else {
        return '--';
      }
    },
    loadSteps() {
      this.steps = [];
      const loader = this.$loading.show();
      axios
        .get(`/api/steps?vid=${this.id}`)
        .then(res => {
          if (res.status === 200) {
            this.steps = res.data;
            this.steps = res.data.filter(x => x.id != this.stepId);
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot load steps for this vidpop!',
              'danger'
            );
          }
        })
        .catch(err => {
          // console.log(err);
          this.$func.makeToast(
            this,
            'Error',
            'Cannot load steps for this vidpop!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleNext() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.stepId}/edit/settings`
      });
    },
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.stepId}/edit/video`
      });
    },
    handleChooseStep(idx, stepId) {
      this.choices[idx] = stepId;
      const tmp = this.choices;
      tmp[idx] = stepId;
      this.choices = [];
      this.choices = tmp;
    },
    saveStep() {
      const validCheck = this.$func.validateStep(this.step);
      if (!validCheck.state) {
        this.$func.makeToast(this, 'Error', validCheck.msg, 'danger');
        return;
      }
      const loader = this.$loading.show();
      this.$store.dispatch(STEP, {
        ...this.step,
        next_step: this.choices.join(',')
      });
      const s = {
        ...this.step,
        next_step: this.choices.join(',')
      };
      axios
        .post('/api/update-step', s)
        .then(res => {
          if (res && res.status === 200) {
            this.$func.makeToast(
              this,
              'Notice',
              'Current step is saved!',
              'success'
            );
            this.saved = true;
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
    },
    gotoEndScreen() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/end-screen`
      });
    },
    getLetter(idx) {
      return String.fromCharCode(65 + idx);
    },
    gotoSetting() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/settings/settings`
      });
    },
    getUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    }
  }
};
</script>
