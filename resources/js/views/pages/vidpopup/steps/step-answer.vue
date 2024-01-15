<template>
  <div id="step-flow-answer">
    <div class="flow-headers">
      <router-link
        :to="`/app/vidgen/${id}/${step_id}/edit/overlay`"
        tag="span"
      >
        Text
      </router-link>
      <span class="active">Answer</span>
      <router-link :to="`/app/vidgen/${id}/${step_id}/edit/video`" tag="span">
        Video
      </router-link>
      <router-link :to="`/app/vidgen/${id}/${step_id}/edit/logic`" tag="span">
        Funnel
      </router-link>
    </div>
    <div class="content-wrapper">
      <p>Select answer type:</p>
      <b-dropdown
        block
        menu-class="w-100"
        no-caret
        toggle-class="answer-toggle"
      >
        <template #button-content>
          <div class="w-100 space-between px-3">
            <div>
              <img :src="answerOption.image" class="mr-2" />
              {{ answerOption.title }}
            </div>
            <fa-icon :icon="['fas', 'chevron-down']" />
          </div>
        </template>
        <b-dropdown-item-button
          v-for="(item, index) in answerOptions"
          @click="answerOption = item"
          :key="`answer-option-${index}`"
          :class="item === answerOption ? 'active' : ''"
        >
          <img :src="item.image" class="mr-2" />{{ item.title }}
        </b-dropdown-item-button>
      </b-dropdown>

      <!-- <div class="answer-button-choice-wrapper" v-if="answerOption.value == 4">
        <div class="answer-button-choice mt-5">
          <b-form-group
            id="payment-link-group"
            label="Add your payment link"
            label-for="payment-link"
            class="mt-5"
          >
            <b-form-input
              id="payment-link"
              type="text"
              class="borderless-text-field"
              v-model="payment_link"
              required
            ></b-form-input>
          </b-form-group>
          <b-form-group
            id="payment-content-group"
            label="Add your payment text"
            label-for="payment-content"
            class="mt-5"
          >
            <b-form-input
              id="payment-content"
              type="text"
              class="borderless-text-field"
              maxlength="10"
              required
              v-model="payment_text"
            ></b-form-input>
          </b-form-group>
        </div>
        <div class="answer-button-choice mt-5">
          <div class="space-between mt-4">
            <span class="left-label">Amount</span>
            <input
              class="rounded-input"
              type="number"
              v-model="payment_value"
            />
          </div>
        </div>
      </div>

      <div class="answer-button-choice-wrapper" v-if="answerOption.value == 3">
        <div class="answer-button-choice mt-5">
          <b-form-group
            id="schedule-link-group"
            label="Add your appointment scheduling link:"
            label-for="schedule-link"
            class="mt-5"
          >
            <b-form-input
              id="schedule-link"
              type="text"
              class="borderless-text-field"
              v-model="calendar_link"
              required
            ></b-form-input>
          </b-form-group>
        </div>
      </div> -->

      <div class="answer-button-choice-wrapper" v-if="answerOption.value == 2">
        <div class="answer-button-choice mt-5">
          <b-form-group
            id="call-to-action-group"
            label="Call to action text:"
            label-for="call-to-action-text"
            class="mt-5"
          >
            <b-form-input
              id="call-to-action-text"
              type="text"
              class="borderless-text-field"
              maxlength="21"
              required
              v-model="button_content"
            ></b-form-input>
          </b-form-group>
          <b-form-group
            id="call-to-link-group"
            label="Call to action link:"
            label-for="call-to-action-link"
            class="mt-3"
          >
            <b-form-input
              id="call-to-action-link"
              type="text"
              class="borderless-text-field"
              required
              v-model="button_link"
            ></b-form-input>
          </b-form-group>
        </div>
      </div>
      <div
        class="answer-multiple-choice-wrapper"
        v-if="answerOption.value == 1"
      >
        <div class="answer-multiple-choice mt-4">
          <div class="d-flex justify-content-end mb-3">
            <a
              href="javascript:;"
              class="btn btn-sm btn-outline-primary"
              @click="addChoice"
            >
              <fa-icon :icon="['fas', 'plus']" /> Add Choice
            </a>
          </div>
          <div class="choice-wrapper">
            <choice-item
              :key="`choice-item-${index}`"
              :item="item"
              v-for="(item, index) in choices"
              @onRemoveChoiceItem="handleRemoveChoiceItem"
            />
          </div>
          <div class="space-between mt-4">
            <span class="left-label">Enable multiple selection?</span>
            <b-form-checkbox
              v-model="is_multiple"
              name="fit-video"
              switch
              size="lg"
            ></b-form-checkbox>
          </div>
          <div class="space-between mt-4">
            <span class="left-label">Show option count</span>
            <b-form-checkbox
              v-model="show_count"
              name="fit-video"
              switch
              size="lg"
            ></b-form-checkbox>
          </div>
        </div>
      </div>
      <!-- <div class="answer-open-ended-wrapper" v-if="answerOption.value == 0">
        <div class="answer-open-ended mt-5">
          <b-form-group
            id="answer-title-group"
            label="Add answer title here:"
            label-for="answer-title"
            class="mt-5"
          >
            <b-form-input
              id="answer-title"
              type="text"
              class="borderless-text-field"
              v-model="answerTitle"
              required
            />
          </b-form-group>
          <div class="d-flex align-items-center justify-content-between">
            <div class="answer-type-wrapper">
              <p>Video</p>
              <button
                class="btn btn-circle"
                :class="is_video && 'active'"
                @click="is_video = !is_video"
              >
                <img src="/images/icons/camera.svg" />
              </button>
            </div>
            <div class="answer-type-wrapper">
              <p>Audio</p>
              <button
                class="btn btn-circle"
                :class="is_audio && 'active'"
                @click="is_audio = !is_audio"
              >
                <img src="/images/icons/mic.svg" />
              </button>
            </div>
            <div class="answer-type-wrapper">
              <p>Text</p>
              <button
                class="btn btn-circle"
                :class="is_text && 'active'"
                @click="is_text = !is_text"
              >
                <img src="/images/icons/text-square.svg" />
              </button>
            </div>
          </div>

          <div class="space-between mt-5">
            <span class="left-label">Time limit on video/audio (in sec)</span>
            <input class="rounded-input" type="number" v-model="timeLimit" />
          </div>
        </div>
      </div> -->

      <div class="space-between mt-4">
        <span class="left-label">Delay interaction (in sec)</span>
        <input class="rounded-input" type="number" v-model="delay" />
      </div>

      <div class="space-between mt-4">
        <span class="left-label">Collect contact details on this step</span>
        <b-form-checkbox
          v-model="data_collection"
          name="fit-video"
          switch
          size="lg"
        ></b-form-checkbox>
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

<style lang="scss" moduled>
#step-flow-answer {
  height: 100%;
  display: flex;
  flex-direction: column;
  .content-wrapper {
    height: calc(100% - 50px);
    overflow-y: auto;
    padding-bottom: 50px;
    padding-left: 5px;
    padding-right: 5px;
    .dropdown {
      padding: 5px;
      background-color: #eee;
      border-radius: 30px;
      .dropdown-menu li.active button {
        background-color: #eee;
      }
    }
    p {
      margin-bottom: 5px;
      color: #777;
      margin-left: 10px;
    }
    .answer-multiple-choice-wrapper {
      margin-left: 1px;
      margin-right: 1px;
    }
    .answer-type-wrapper {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      p {
        font-size: 1.1rem;
        font-weight: 700;
        color: black;
        text-align: center;
        margin-left: 0px;
      }
      .btn-circle {
        background-color: #f0f1f3;
        color: #222;
        font-size: 1.1rem;
        border-radius: 50%;
        padding: 0px;
        width: 50px;
        height: 50px;
        @media (min-width: 1500px) {
          width: 70px;
          height: 70px;
          img {
            width: 25px;
            height: 25px;
          }
        }
        img {
          width: 20px;
          height: 20px;
        }
        &:hover,
        &.active {
          background-color: #1998e4;
          img {
            filter: invert(1);
          }
        }
      }
    }
    .answer-toggle {
      border-radius: 30px;
      padding-top: 10px;
      padding-bottom: 10px;
      color: black;
      background-color: #f0f1f3;
      border-color: #f0f1f3;
      &:focus {
        box-shadow: none;
      }
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
    .border-rounded {
      border-radius: 20px;
      background: #000;
    }
    .payment-desc {
      color: gray;
    }
  }
}
</style>

<script>
import { STEP, STEP_UPDATED } from '@/services/store/vidpopup.module';
import { mapGetters } from 'vuex';
import ChoiceItem from '@/views/components/choice-item.vue';
export default {
  components: { ChoiceItem },
  name: 'step-answer',
  computed: {
    ...mapGetters(['vidpop', 'step', 'currentUser'])
  },
  watch: {
    is_video: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          content: {
            ...this.step.answer.content,
            is_video: newVal
          }
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    is_audio: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          content: {
            ...this.step.answer.content,
            is_audio: newVal
          }
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    is_text: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          content: {
            ...this.step.answer.content,
            is_text: newVal
          }
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    answerTitle: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          content: {
            ...this.step.answer.content,
            answerTitle: newVal
          }
        }
      });
    },
    timeLimit: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          option: {
            limit: newVal
          }
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    delay: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        video_delay: newVal
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    data_collection: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        data_collection: newVal
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    calendar_link: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          content: newVal
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    show_count: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          option: {
            ...this.step.answer.option,
            show_count: newVal
          }
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    payment_value: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          option: {
            ...this.step.answer.option,
            value: newVal
          }
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    payment_text: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          option: {
            ...this.step.answer.option,
            text: newVal
          }
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    payment_link: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          content: newVal
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    answerOption: {
      handler: function(newVal, oldVal) {
        if (oldVal.value === -1) {
          return;
        }
        if (newVal) {
          let content = '';
          let option = '';
          let ns = 'end';
          switch (newVal.value) {
            case 0:
              // open ended
              this.is_video = 1;
              this.is_audio = 1;
              this.is_text = 1;
              this.answerTitle = 'How would you like to answer?';
              this.timeLimit = 120;
              content = {
                is_video: 1,
                is_audio: 1,
                is_text: 1,
                answerTitle: 'How would you like to answer?'
              };
              option = {
                limit: 120
              };

              break;
            case 1:
              // multiple choice
              this.choices = [];
              this.show_count = 0;
              this.is_multiple = 0;
              content = [];
              option = {
                show_count: 0,
                is_multiple: 0
              };
              ns = '';
              break;
            case 2:
              // button
              content = '';
              option = '';
              this.button_content = '';
              break;
            case 3:
              // calendar
              content = '';
              option = '';
              break;
            case 4:
              // payment
              content = '';
              option = {
                value: 1
              };
              break;
            default:
              break;
          }
          this.$store.dispatch(STEP, {
            ...this.step,
            answer: {
              ...this.step.answer,
              type: newVal.value,
              content,
              option
            },
            next_step: ns
          });
          this.next_step = ns;
          this.$store.dispatch(STEP_UPDATED, true);
        }
      },
      deep: true
    },
    selectedCurrency: {
      handler: function(newVal, oldVal) {
        if (newVal) {
          this.$store.dispatch(STEP, {
            ...this.step,
            payment_currency: newVal.value
          });
          this.$store.dispatch(STEP_UPDATED, true);
        }
      },
      deep: true
    },
    is_multiple: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        is_multiple: newVal
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    button_content: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          content: newVal
        }
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    button_link: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        answer: {
          ...this.step.answer,
          option: {
            link: newVal
          }
        }
      });
    },
    choices: {
      handler: function(newVal, oldVal) {
        this.$store.dispatch(STEP, {
          ...this.step,
          answer: {
            ...this.step.answer,
            content: newVal
          }
        });
        this.$store.dispatch(STEP_UPDATED, true);
      },
      deep: true
    }
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
  data() {
    return {
      answerOptions: [
        // {
        //   title: 'Multimedia',
        //   image: '/images/icons/camera.svg',
        //   value: 0
        // },
        {
          title: 'Multiple Choice',
          image: '/images/icons/multiple.svg',
          value: 1
        },
        {
          title: 'Button',
          image: '/images/icons/button.svg',
          value: 2
        },
        // {
        //   title: 'Calendar',
        //   image: '/images/icons/calendar.svg',
        //   value: 3
        // },
        // {
        //   title: 'Payment',
        //   image: '/images/icons/payment.svg',
        //   value: 4
        // }
      ],
      answerOption: {
        value: -1
      },
      id: '',
      step_id: '',
      is_video: false,
      is_audio: false,
      is_text: false,
      timeLimit: 120,
      delay: 2,
      data_collection: false,
      choices: [],
      show_count: true,
      calendar_link: '',
      payment_value: 1,
      payment_link: '',
      payment_text: 'Pay It',
      is_multiple: false,
      button_content: '',
      button_link: '',
      next_step: '',
      answerTitle: 'How would you like to answer?'
    };
  },
  mounted() {
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;

    this.answerOption = this.answerOptions.find(
      option => option.value === this.step.answer.type
    );

    this.data_collection = this.step.data_collection ? true : false;

    this.next_step = this.step.next_step;

    this.delay = this.step.video_delay;

    switch (this.step.answer.type) {
      case 0:
        // open ended
        this.is_video = this.step.answer.content.is_video;
        this.is_audio = this.step.answer.content.is_audio;
        this.is_text = this.step.answer.content.is_text;
        this.answerTitle =
          this.step.answer.content.answerTitle ||
          'How would you like to answer?';
        this.timeLimit = this.step.answer.option.limit;
        break;
      case 1:
        // multiple choice
        this.choices = this.step.answer.content;
        this.is_multiple = this.step.answer.option.is_multiple;
        this.show_count = this.step.answer.option.show_count;
        break;
      case 2:
        // button
        this.button_content = this.step.answer.content;
        this.button_link = this.step.answer.option ? this.step.answer.option.link : '';
        break;
      case 3:
        this.calendar_link = this.step.answer.content;
        break;
      case 4:
        // payment
        this.payment_value = this.step.answer.option.value;
        this.payment_text = this.step.answer.option.text || 'Pay It';
        this.payment_link = this.step.answer.content;
      default:
        break;
    }
  },
  methods: {
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/edit/overlay`
      });
    },
    handleNext() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/edit/video`
      });
    },
    addChoice() {
      if (this.choices.length == 3) {
        this.$func.makeToast(
          this,
          'Error',
          'Cannot Create More than 3 Choices',
          'danger'
        );
        return;
      }
      this.choices = [
        ...this.choices,
        {
          title: `Choice ${this.choices.length + 1} `,
          selectedStep: '',
          isEndScreen: true
        }
      ];
      if (this.next_step === '') {
        this.next_step = 'end';
      } else {
        this.next_step = `${this.next_step},end`;
      }
      this.$store.dispatch(STEP, {
        ...this.step,
        next_step: this.next_step
      });
      // this.step = {
      //   ...this.step,
      //   next_step: this.next_step
      // };
    },
    handleRemoveChoiceItem(info) {
      const { item } = info;
      var selectedItem = this.choices.find(x => x === item);
      if (selectedItem) {
        let index = this.choices.indexOf(selectedItem);
        const ns = this.next_step.split(',');
        ns.splice(index, 1);
        this.next_step = ns.join(',');
        this.$store.dispatch(STEP, {
          ...this.step,
          next_step: this.next_step
        });
        // this.step = {
        //   ...this.step,
        //   next_step: this.next_step
        // };
        this.choices.splice(index, 1);
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
